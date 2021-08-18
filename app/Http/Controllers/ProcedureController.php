<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Procedure;
use App\Models\ProcedureType;
use App\Http\Requests\ProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Models\ProcedureFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Procedure::query();
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('updated_at', 'DESC')->orderBy('code', 'DESC');
        }
        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where('code', 'like', '%'.mb_strtoupper($request->search).'%');
            }
        }
        return [
            'message' => 'Lista de tipos de trámites',
            'payload' => ProcedureResource::collection($query->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1))->resource,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcedureRequest $request)
    {
        if (Procedure::whereCode($request->code)->exists()) {
            return response()->json([
                'message' => 'Hoja de ruta inválida',
                'errors' => [
                    'code' => ['La hoja de ruta ya existe, sugerencia: '.ProcedureType::find($request->procedure_type_id)->next_code]
                ]
            ], 400);
        }
        $request->merge([
            'area_id' => auth()->user()->area_id,
        ]);
        $procedure = Procedure::create($request->except('archived'));
        $procedure->procedure_type()->increment('counter');
        return [
            'message' => 'Trámite creado',
            'payload' => [
                'procedure' => new ProcedureResource($procedure),
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function show(Procedure $procedure)
    {
        return [
            'message' => 'Datos del trámite',
            'payload' => [
                'procedure' => new ProcedureResource($procedure),
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function update(ProcedureRequest $request, Procedure $procedure)
    {
        if (Procedure::whereCode($request->code)->where('id', '!=', $procedure->id)->exists()) {
            return response()->json([
                'message' => 'Hoja de ruta inválida',
                'errors' => [
                    'code' => ['La hoja de ruta ya existe, sugerencia: '.ProcedureType::find($procedure->procedure_type_id)->next_code]
                ]
            ], 400);
        }
        $procedure->update($request->except('archived', 'area_id'));
        return [
            'message' => 'Trámite actualizado',
            'payload' => [
                'procedure' => new ProcedureResource($procedure),
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure)
    {
        if ($procedure->has_flowed || $procedure->archived || ($procedure->area_id != auth()->user()->area_id)) {
            return response()->json([
                'message' => 'El trámite no puede ser eliminado porque ya fue derivado',
            ], 422);
        }
        $procedure->delete();
        return [
            'message' => 'Trámite eliminado',
        ];
    }

    public function flow(Request $request, Procedure $procedure)
    {
        if ($procedure->archived) {
            return response()->json([
                'message' => 'El trámite no se puede derivar porque ya fue archivado',
            ], 422);
        } elseif ($procedure->area_id != auth()->user()->area_id) {
            return response()->json([
                'message' => 'El trámite no se puede derivar porque no se encuentra en su bandeja',
            ], 422);
        }
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
        ]);
        if (!$validated) {
            return response()->json([
                'message' => 'El campo sección es requerido',
            ], 422);
        } else {
            DB::beginTransaction();
            try {
                DB::table('procedures')->where([
                    'id' => $procedure->id,
                ])->update([
                    'area_id' => $request->area_id,
                ]);
                DB::table('procedure_flows')->insert([
                    'procedure_id' => $procedure->id,
                    'from_area' => auth()->user()->id,
                    'to_area' => $request->area_id,
                    'user_id' => auth()->user()->id,
                    'created_at' =>  Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                DB::commit();
                return [
                    'message' => 'Trámite derivado',
                    'payload' => [
                        'procedure' => $procedure,
                    ],
                ];
            } catch(\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Error al derivar el trámite',
                    'errors' => [
                        'area_id' => 'Ocurrió un error al derivar el trámite',
                    ],
                ], 500);
            }
        }
    }

    public function archive(Procedure $procedure)
    {
        if ($procedure->archived) {
            return response()->json([
                'message' => 'El trámite ya fue archivado',
            ], 422);
        } elseif ($procedure->area_id != auth()->user()->area_id) {
            return response()->json([
                'message' => 'El trámite no se puede archivar porque no se encuentra en su bandeja',
            ], 422);
        }
        DB::beginTransaction();
        try {
            DB::table('procedures')->where([
                'id' => $procedure->id,
            ])->update([
                'archived' => true,
            ]);
            DB::table('procedure_flows')->insert([
                'procedure_id' => $procedure->id,
                'from_area' => auth()->user()->area_id,
                'to_area' => auth()->user()->area_id,
                'user_id' => auth()->user()->id,
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::commit();
            return [
                'message' => 'Trámite archivado',
                'payload' => [
                    'procedure' => $procedure,
                ],
            ];
        } catch(\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al archivar el trámite',
            ], 500);
        }
    }
}
