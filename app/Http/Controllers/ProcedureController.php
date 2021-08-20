<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Procedure;
use App\Models\ProcedureType;
use Spatie\Permission\Models\Role;
use App\Http\Requests\FlowRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\ProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Http\Resources\ProcedureFlowResource;
use App\Http\Resources\TimelineResource;
use Illuminate\Support\Facades\Auth;
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
        /** @var \App\Models\User */
        $user = Auth::user();
        $area_id = $user->area_id;
        $owned_procedures = DB::table('procedures')->where('area_id', $area_id)->select('id');
        // Tramites que se encuentran actualmente en la sección
        $current = DB::table('procedures as p')->select('p.*', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), DB::raw($user->hasRole('RECEPCIÓN') ? 'FALSE as has_flowed' : 'TRUE as has_flowed'))->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures)->where('p.deleted_at', null)->where('p.archived', false);
        if ($request->has('search')) {
            if ($request->search != '') {
                $current->where('p.code', 'like', '%'.trim(mb_strtoupper($request->search)).'%');
            }
        }
        // Tramites archivados en la sección
        $archived = DB::table('procedures as p')->select('p.*', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), DB::raw('TRUE as has_flowed'))->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures)->where('p.deleted_at', null)->where('p.archived', true);
        if ($request->has('search')) {
            if ($request->search != '') {
                $archived->where('p.code', 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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
        if ($user->hasRole('RECEPCIÓN')) {
            $flowed = DB::table('procedures as p')->rightJoinSub($outgoing, 'o', function ($join) {
                $join->on('p.id', '=', 'o.procedure_id');
            })->select( 'p.*', 'i.from_area', 'i.created_at as incoming_at', 'i.user_id as incoming_user', 'o.to_area', 'o.created_at as outgoing_at', 'o.user_id as outgoing_user', DB::raw('FALSE as owner'), DB::raw('TRUE as has_flowed'))->leftJoinSub($incoming, 'i', function ($join) {
                $join->on('p.id', '=', 'i.procedure_id');
            })->where('p.deleted_at', null);
        } else {
            $flowed = DB::table('procedures as p')->rightJoinSub($incoming, 'i', function ($join) {
                $join->on('p.id', '=', 'i.procedure_id');
            })->leftJoinSub($outgoing, 'o', function ($join) {
                $join->on('p.id', '=', 'o.procedure_id');
            })->select( 'p.*', 'i.from_area', 'i.created_at as incoming_at', 'i.user_id as incoming_user', 'o.to_area', 'o.created_at as outgoing_at', 'o.user_id as outgoing_user', DB::raw('FALSE as owner'), DB::raw('TRUE as has_flowed'))->where('p.deleted_at', null);
        }
        if ($request->has('search')) {
            if ($request->search != '') {
                $flowed->where('p.code', 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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

    public function flow(FlowRequest $request, Procedure $procedure)
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
        DB::beginTransaction();
        try {
            DB::table('procedures')->where([
                'id' => $procedure->id,
            ])->update([
                'area_id' => $request->area_id,
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

    public function timeline(Procedure $procedure)
    {
        $role = Role::whereName('RECEPCIÓN')->firstOrFail();
        $area = Area::where('role_id', $role->id)->first();
        $created = [[
            'to_area' => [
                'name' => $area->name,
            ],
            'action' => 'TRÁMITE CREADO',
            'created_at' => $procedure->created_at,
        ]];
        $timeline = $procedure->procedure_flows()->select('to_area', 'created_at')->selectRaw("'DERIVADO' as action")->with(['to_area' => function($q) {
            return $q->select('id', 'name');
        }])->orderBy('created_at', 'DESC')->get()->toArray();
        $archived = [];
        if ($procedure->archived) {
            $archived = [[
                'to_area' => [
                    'name' => $procedure->area->name,
                ],
                'action' => 'ARCHIVADO',
                'created_at' => $procedure->updated_at,
            ]];
        }
        if (count($timeline) > 0) $timeline[0]['action'] = 'EN POSESIÓN';
        return [
            'message' => 'Historia del trámite',
            'payload' => [
                'timeline' => TimelineResource::collection(array_merge($archived, $timeline, $created)),
            ],
        ];
    }

    public function code(CodeRequest $request)
    {
        $procedure = Procedure::where('code', $request->code)->first();
        return [
            'message' => 'Datos del trámite de acuerdo a hoja de ruta',
            'payload' => [
                'procedure' => $procedure,
            ],
        ];
    }
}
