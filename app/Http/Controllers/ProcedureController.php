<?php

namespace App\Http\Controllers;

use Salman\Mqtt\MqttClass\Mqtt;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Procedure;
use App\Http\Requests\FlowRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\ProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Http\Resources\ProcedureFlowResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $area_id = $user->area_id;
        $owned_procedures = DB::table('procedures')->where('area_id', $area_id)->select('id');
        if ($user->hasRole('VERIFICADOR')) {
            $owned_procedures->where(function($q) {
                return $q->orWhere('verified', '=', null)->orWhere('verified', '=', true);
            });
        }
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $owned_procedures->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        }
        // Tramites que se encuentran actualmente en la sección
        $current = DB::table('procedures as p')->select('p.id', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), 'p.archived')->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures->where('pending', false))->where('p.deleted_at', null)->where('p.archived', false);
        if ($request->has('search')) {
            if ($request->search != '') {
                $current->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(p.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(p.origin)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        // Tramites archivados en la sección
        $archived = DB::table('procedures as p')->select('p.id', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), 'p.archived')->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures)->where('p.deleted_at', null)->where('p.archived', true);
        if ($request->has('search')) {
            if ($request->search != '') {
                $archived->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(p.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(p.origin)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        // Tramites que llegaron a la sección
        $incoming = DB::table('procedure_flows as a')->select('a.*')->leftJoin('procedure_flows as b', function($join) {
            $join->on('a.procedure_id', '=', 'b.procedure_id');
            $join->on('a.to_area', '=', 'b.to_area');
            $join->on('a.created_at', '<', 'b.created_at');
        })->where('b.id', null)->where('a.to_area', $area_id)->where('a.to_area', $area_id)->whereNotIn('a.procedure_id', $owned_procedures);
        // Tramites que salieron a la sección
        $outgoing = DB::table('procedure_flows as a')->select('a.*')->leftJoin('procedure_flows as b', function($join) {
            $join->on('a.procedure_id', '=', 'b.procedure_id');
            $join->on('a.from_area', '=', 'b.from_area');
            $join->on('a.created_at', '<', 'b.created_at');
        })->where('b.id', null)->where('a.from_area', $area_id)->where('a.from_area', $area_id)->whereNotIn('a.procedure_id', $owned_procedures);
        // Tramites entraron y salieron por la sección
        $flowed = DB::table('procedures as p')->rightJoinSub($incoming, 'i', function ($join) {
            $join->on('p.id', '=', 'i.procedure_id');
        })->leftJoinSub($outgoing, 'o', function ($join) {
            $join->on('p.id', '=', 'o.procedure_id');
        })->select( 'p.id', 'i.from_area', 'i.created_at as incoming_at', 'i.user_id as incoming_user', 'o.to_area', 'o.created_at as outgoing_at', 'o.user_id as outgoing_user', DB::raw('FALSE as owner'), DB::raw('FALSE as archived'))->where('p.deleted_at', null)->where('p.area_id' , '!=', $area_id);
        if ($request->has('search')) {
            if ($request->search != '') {
                $flowed->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(p.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(p.origin)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        // Union de los resultados, primero los que se encuentran en la sección, después los que pasaron por la sección y por último los que fueron archivados en la sección
        $procedures = DB::table($current)->union($flowed)->union($archived)->orderBy('archived', 'ASC')->orderByRaw('-outgoing_at ASC')->orderByRaw('incoming_at DESC');
        return [
            'message' => 'Lista de trámites',
            'payload' => ProcedureFlowResource::collection($procedures->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1))->resource,
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
        $user = auth()->user();
        $area_id = $user->area_id;
        if (Procedure::whereCode($request->code)->exists()) {
            return response()->json([
                'message' => 'Hoja de ruta inválida',
                'errors' => [
                    'code' => ['La hoja de ruta ya existe']
                ]
            ], 400);
        }
        $request->merge([
            'area_id' => $area_id,
        ]);
        $procedure = new Procedure;
        $procedure->fill($request->except('archived'));
        DB::beginTransaction();
        try {
            $procedure->counter = $procedure->area->counter + 1;
            $procedure->user_id = $user->id;
            if ($procedure->procedure_type->requirements()->count() == 0) {
                $procedure->verified = true;
            } else {
                $procedure->verified = null;
            }
            $procedure->save(['timestamps' => true]);
            $procedure->requirements()->sync($procedure->procedure_type->requirements);
            $procedure->procedure_type()->increment('counter');
            $procedure->area()->increment('counter');
            $procedure->procedure_flows()->create([
                'from_area' => $area_id,
                'to_area' => $area_id,
                'user_id' => $user->id,
            ]);
            DB::commit();
            return [
                'message' => 'Trámite creado',
                'payload' => [
                    'procedure' => new ProcedureResource($procedure),
                ]
            ];
        } catch(\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el trámite',
                'errors' => [
                    'area_id' => 'Ocurrió un error al crear el trámite',
                ],
            ], 500);
        }
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
                    'code' => ['La hoja de ruta ya existe']
                ]
            ], 400);
        }
        $update_requirements = false;
        if ($request->has('procedure_type_id')) {
            if ($request->procedure_type_id != $procedure->procedure_type_id) {
                $update_requirements = true;
            }
        }
        DB::beginTransaction();
        try {
            $procedure->update($request->except('archived', 'area_id'));
            if ($update_requirements) {
                $requirements = $procedure->procedure_type->requirements->pluck('id')->all();
                $requirements = array_map(function() {
                    return [
                        "validated" => false,
                    ];
                }, array_flip($requirements));
                $procedure->requirements()->sync($requirements);
                $procedure->procedure_type()->increment('counter');
                $procedure->update([
                    'verified' => null,
                ]);
            }
            DB::commit();
            return [
                'message' => 'Trámite actualizado',
                'payload' => [
                    'procedure' => new ProcedureResource($procedure),
                ]
            ];
        } catch(\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el trámite',
                'errors' => [
                    'area_id' => 'Ocurrió un error al actualizar el trámite',
                ],
            ], 500);
        }
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

    public function flow(FlowRequest $request, Procedure $procedure)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($procedure->archived) {
            return response()->json([
                'message' => 'El trámite no se puede derivar porque ya fue archivado',
            ], 422);
        } elseif (!$procedure->verified && $user->hasRole('VERIFICADOR')) {
            return response()->json([
                'message' => 'Antes de derivar debe verificar los requisitos',
            ], 422);
        }
        DB::beginTransaction();
        try {
            DB::table('procedures')->where([
                'id' => $procedure->id,
            ])->update([
                'area_id' => $request->area_id,
                'pending' => true,
                'user_id' => null,
            ]);
            DB::table('procedure_flows')->insert([
                'procedure_id' => $procedure->id,
                'from_area' => auth()->user()->area_id,
                'to_area' => $request->area_id,
                'user_id' => auth()->user()->id,
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::commit();
            try {
                $mqtt = new Mqtt();
                $notify = $mqtt->ConnectAndPublish('procedures/tray/area/'.$request->area_id, Procedure::where('area_id', $request->area_id)->where('pending', true)->count());
                if (!$notify) {
                    Log::error('Conexión perdida con el servidor de Websockets');
                }
            } catch(\Throwable $e) {
                Log::error($e);
                Log::error('Servidor de Websockets inalcanzable');
            }
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

    public function archive(Procedure $procedure)
    {
        if ($procedure->archived) {
            return response()->json([
                'message' => 'El trámite ya fue archivado',
            ], 422);
        }
        DB::beginTransaction();
        try {
            DB::table('procedures')->where([
                'id' => $procedure->id,
            ])->update([
                'archived' => true,
                'pending' => false,
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

    public function code(CodeRequest $request)
    {
        $procedure = Procedure::where('code', $request->code)->first();
        $procedure->procedure_type;
        return [
            'message' => 'Datos del trámite de acuerdo a hoja de ruta',
            'payload' => [
                'procedure' => $procedure,
            ],
        ];
    }

    public function pending()
    {
        $user = Auth::user();
        $area_id = $user->area_id;
        $query = Procedure::where('pending', true)->where('area_id', $area_id);
        return [
            'message' => 'Trámites esperando ingresar a la bandeja',
            'payload' => [
                'badge' => $query->count(),
                'procedures' => $query->select('id', 'code')->get(),
            ],
        ];
    }

    public function receive(Procedure $procedure)
    {
        $user = Auth::user();
        if ($procedure->area_id == $user->area_id) {
            $procedure->update([
                'pending' => false,
                'user_id' => $user->id,
            ]);
            $mqtt = new Mqtt();
            $notify = $mqtt->ConnectAndPublish('procedures/tray/area/'.$procedure->area_id, Procedure::where('area_id', $procedure->area_id)->where('pending', true)->count());
            if (!$notify) {
                Log::error('Conexión perdida con el servidor de Websockets');
            }
            return [
                'message' => 'Trámite añadido a la bandeja',
                'payload' => [
                    'procedure' => $procedure,
                ],
            ];
        } else {
            abort(403, 'El trámite no le pertenece');
        }
    }

    public function print(FlowRequest $request, Procedure $procedure)
    {
        $areas = Area::where('group', '>', 0)->orderBy('order', 'ASC')->orderBy('name', 'ASC')->select('id', 'name', 'code', 'order')->get();
        foreach ($areas as $i => $area) {
            $areas[$i]->selected = ($area->id == $request->area_id);
        }
        try {
            $procedure->detail = explode('\n', wordwrap($procedure->detail, 75, '\n'));
            $data = [
                'filename' => 'Trámite ' . $procedure->code . ' - ' . Carbon::now()->format('d-m-Y H-i') . '.pdf',
                'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/logo_low.png'))),
                'procedure' => $procedure,
                'areas_styles' => [
                    'border-right: none; text-align: start; vertical-align: top;',
                    'border-right: none; border-left: none; text-align: start; vertical-align: top;',
                    'border-left: none; text-align: start; vertical-align: top;',
                ],
                'areas' => $areas->chunk(6),
            ];
            $pdf = PDF::loadView('pdf.procedure', $data)->output();
            return [
                'message' => 'PDF generado',
                'payload' => [
                    'file' => [
                        'content' => base64_encode($pdf),
                        'name' => $data['filename'],
                    ],
                ],
            ];
        } catch(\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el PDF',
            ], 500);
        }
    }
}
