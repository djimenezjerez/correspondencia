<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Http\Requests\ProcedureRequirementRequest;
use App\Http\Resources\ProcedureRequirementResource;
use App\Http\Resources\ProcedureFlowResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcedureRequirementController extends Controller
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
        $owned_procedures = DB::table('procedures')->where('area_id', $area_id)->where('verified', '=', false)->select('id');
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
        $procedures = DB::table($current)->orderBy('archived', 'ASC')->orderByRaw('-outgoing_at ASC')->orderByRaw('incoming_at DESC');
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
        return [
            'message' => 'Requisitos de hoja de ruta',
            'payload' => [
                'procedure' => new ProcedureRequirementResource($procedure),
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
    public function update(ProcedureRequirementRequest $request, Procedure $procedure)
    {
        DB::beginTransaction();
        try {
            foreach($request->requirements as $requirement) {
                $procedure->requirements()->updateExistingPivot($requirement['id'], [
                    'validated' => $requirement['validated'],
                ], false);
            }
            $procedure->update([
                'verified' => $procedure->validated,
            ]);
            DB::commit();
            return [
                'message' => $procedure->validated ? 'Hoja de ruta lista para derivar' : 'Hoja de ruta enviada a regularización',
                'payload' => [
                    'procedure' => new ProcedureRequirementResource($procedure),
                ]
            ];
        } catch(\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar los requisitos del hoja de ruta',
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
        //
    }
}
