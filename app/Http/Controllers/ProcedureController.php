<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Http\Requests\ProcedureRequest;
use App\Http\Resources\ProcedureResource;
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
            $query->orderBy('code', 'DESC');
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
        $request->merge([
            'area_id' => auth()->user()->area_id,
        ]);
        $procedure = Procedure::create($request->all());
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
        $procedure->update($request->except('area_id', 'archived'));
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
                'message' => 'El trámite no puede ser eliminado porque ya fue editado',
            ], 422);
        }
        $procedure->delete();
        return [
            'message' => 'Trámite eliminado',
        ];
    }
}
