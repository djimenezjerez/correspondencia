<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Models\Area;
use App\Http\Resources\ProcedureFlowResource;
use App\Http\Requests\ProcedureTrackingRequest;
use App\Http\Resources\TimelineResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcedureTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProcedureTrackingRequest $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $area_id = $user->area_id;
        $flowed = DB::table('procedure_flows')->selectRaw('procedure_id, count(*) as flows_count')->groupBy('procedure_id')->having('flows_count', '>', 1);
        $owned_procedures = DB::table('procedures as p')->joinSub($flowed, 'pf', function($join) {
            $join->on('pf.procedure_id', '=', 'p.id');
        })->where('p.area_id', $area_id)->select('p.id');
        // Tramites que se encuentran actualmente en la sección
        $current = DB::table('procedures as p')->select('p.id', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), 'p.archived')->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures->where('pending', false))->where('p.deleted_at', null)->where('p.archived', false);
        if ($request->search_by == 'created_at') {
            $start_date = Carbon::parse($request->search)->startOfDay();
            $end_date = Carbon::parse($request->search)->endOfDay();
            $current->whereDate('p.created_at', '>=', $start_date)->whereDate('p.created_at', '<=', $end_date);
        } else {
            $current->where(DB::raw('upper(p.'.$request->search_by.')'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
        }
        // Tramites archivados en la sección
        $archived = DB::table('procedures as p')->select('p.id', 'pf.from_area', 'pf.created_at as incoming_at', 'pf.user_id as incoming_user', DB::raw('null as to_area'), DB::raw('null as outgoing_at'), DB::raw('null as outgoing_user'), DB::raw('TRUE as owner'), 'p.archived')->leftJoin('procedure_flows as pf', function($query) {
            $query->on('pf.procedure_id','=','p.id')->whereRaw('pf.id IN (select MAX(a.id) from procedure_flows as a join procedures as b on a.procedure_id = b.id group by b.id)');
        })->whereIn('p.id', $owned_procedures)->where('p.deleted_at', null)->where('p.archived', true);
        if ($request->search_by == 'created_at') {
            $archived->whereDate('p.created_at', '>=', $start_date)->whereDate('p.created_at', '<=', $end_date);
        } else {
            $archived->where(DB::raw('upper(p.'.$request->search_by.')'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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
        if ($request->search_by == 'created_at') {
            $flowed->whereDate('p.created_at', '>=', $start_date)->whereDate('p.created_at', '<=', $end_date);
        } else {
            $flowed->where(DB::raw('upper(p.'.$request->search_by.')'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function show(Procedure $procedure)
    {
        $area = Area::find($procedure->procedure_flows()->first()->from_area);
        $created = [[
            'flowed_to_area' => [
                'name' => $area->name,
            ],
            'action' => 'TRÁMITE CREADO',
            'created_at' => $procedure->created_at,
        ]];
        $timeline = $procedure->procedure_flows()->select('to_area', 'created_at')->selectRaw("'DERIVADO' as action")->with(['flowed_to_area' => function($q) {
            return $q->select('id', 'name');
        }])->orderBy('created_at', 'DESC')->get()->toArray();
        $archived = [];
        if ($procedure->archived) {
            $archived = [[
                'flowed_to_area' => [
                    'name' => $procedure->area->name,
                ],
                'action' => 'ARCHIVADO',
                'created_at' => $procedure->updated_at,
            ]];
        }
        if (count($timeline) > 0) $timeline[0]['action'] = 'ÚLTIMO DESTINO';
        return [
            'message' => 'Historia del trámite',
            'payload' => [
                'timeline' => TimelineResource::collection(array_merge($archived, $timeline, $created)),
            ],
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Procedure $procedure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure)
    {
        //
    }
}
