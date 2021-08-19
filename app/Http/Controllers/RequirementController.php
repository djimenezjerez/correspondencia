<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Http\Requests\RequirementRequest;
use App\Http\Resources\RequirementResource;
use Illuminate\Http\Request;

class RequirementController extends Controller
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
                    'message' => 'Lista de requisitos',
                    'payload' => [
                        'requirements' => RequirementResource::collection(Requirement::get()),
                    ],
                ];
            }
        }
        $query = Requirement::query();
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('name', 'ASC');
        }
        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where('name', 'like', '%'.trim(mb_strtoupper($request->search)).'%');
            }
        }
        return [
            'message' => 'Lista de requisitos',
            'payload' => RequirementResource::collection($query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1))->resource,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequirementRequest $request)
    {
        $requirement = Requirement::create($request->all());
        return [
            'message' => 'Requisito creado',
            'payload' => [
                'user' => new RequirementResource($requirement),
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function show(Requirement $requirement)
    {
        return [
            'message' => 'Datos de requisito',
            'payload' => [
                'user' => new RequirementResource($requirement),
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function update(RequirementRequest $request, Requirement $requirement)
    {
        if (Requirement::whereName($request->name)->where('id', '!=', $requirement->id)->exists()) {
            return response()->json([
                'message' => 'Nombre inválido',
                'errors' => [
                    'name' => ['El requisito ya existe']
                ]
            ], 400);
        }
        $requirement->update($request->all());
        return [
            'message' => 'Requisito actualizado',
            'payload' => [
                'user' => new RequirementResource($requirement),
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Requirement  $requirement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requirement $requirement)
    {
        if (!$requirement->is_used) {
            $requirement->delete();
            return [
                'message' => 'Requisito eliminado',
            ];
        } else {
            return response()->json([
                'message' => 'El requisito no puede ser eliminado porque tiene trámites asociados',
            ], 422);
        }
    }
}
