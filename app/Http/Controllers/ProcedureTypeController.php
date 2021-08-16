<?php

namespace App\Http\Controllers;

use App\Models\ProcedureType;
use App\Http\Requests\ProcedureTypeRequest;
use App\Http\Resources\ProcedureTypeResource;
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
                $query->where('name', 'like', '%'.mb_strtoupper($request->search).'%');
            }
        }
        return [
            'message' => 'Lista de tipos de trámites',
            'payload' => ProcedureTypeResource::collection($query->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1))->resource,
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
        $procedure_type = ProcedureType::create($request->all());
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
        $procedure_type->update($request->except('code'));
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
}
