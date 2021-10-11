<?php

namespace App\Http\Controllers;

use App\Models\ProcedureType;
use App\Http\Requests\ProcedureTypeRequest;
use App\Http\Resources\ProcedureTypeResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcedureTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            if ($request->boolean('all')) {
                return [
                    'message' => 'Lista de tipos de trámites',
                    'payload' => [
                        'procedure_types' => ProcedureType::withCount('requirements')->orderBy('name', 'ASC')->get(),
                    ],
                ];
            }
        }
        $query = ProcedureType::query();
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('name', 'ASC');
        }
        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de tipos de trámites',
            'payload' => ProcedureTypeResource::collection($query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1))->resource,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcedureTypeRequest $request)
    {
        $procedure_type = ProcedureType::create($request->except('counter'));
        $procedure_type->counter = 0;
        $procedure_type->requirements()->sync($request->has('requirements') ? $request->requirements : []);
        return [
            'message' => 'Tipo de trámite creado',
            'payload' => [
                'procedure_type' => new ProcedureTypeResource($procedure_type),
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProcedureType  $procedureType
     * @return \Illuminate\Http\Response
     */
    public function show(ProcedureType $procedure_type)
    {
        return [
            'message' => 'Datos de tipo de trámite',
            'payload' => [
                'procedure_type' => new ProcedureTypeResource($procedure_type),
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcedureType  $procedureType
     * @return \Illuminate\Http\Response
     */
    public function update(ProcedureTypeRequest $request, ProcedureType $procedure_type)
    {
        if (ProcedureType::whereCode($request->code)->where('id', '!=', $procedure_type->id)->exists()) {
            return response()->json([
                'message' => 'Cödigo inválido',
                'errors' => [
                    'code' => ['El código ya existe']
                ]
            ], 400);
        }
        $procedure_type->update($request->except('counter'));
        if ($request->has('requirements')) {
            $procedure_type->requirements()->sync($request->requirements);
        }
        return [
            'message' => 'Tipo de trámite actualizado',
            'payload' => [
                'procedure_type' => new ProcedureTypeResource($procedure_type),
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcedureType  $procedureType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProcedureType $procedure_type)
    {
        $total = $procedure_type->total_procedures;
        if ($total == 0) {
            $procedure_type->requirements()->sync([]);
            $procedure_type->delete();
            return [
                'message' => 'Tipo de trámite eliminado',
            ];
        } else {
            return response()->json([
                'message' => 'El tipo de trámite no puede ser eliminado porque tiene '.$total.' trámites asociado(s)',
            ], 422);
        }
    }

    public function getCode(ProcedureType $procedure_type)
    {
        return [
            'message' => 'Hoja de ruta válida',
            'payload' => [
                'code'  => $procedure_type->next_code,
            ],
        ];
    }
}
