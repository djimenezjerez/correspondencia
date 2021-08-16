<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'message' => 'Lista de roles',
            'payload' => [
                'roles' => RoleResource::collection(Role::orderBy('name')->get()),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($user->hasRole($role) || $user->can('LEER ROL')) {
            return [
                'message' => 'Datos de rol',
                'payload' => [
                    'role' => new RoleResource($role),
                    'permissions' => PermissionResource::collection($role->permissions()->orderBy('id')->get()),
                ],
            ];
        }
        abort(403, 'Prohibido');
    }
}
