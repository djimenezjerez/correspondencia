<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::where('username', '!=', auth()->user()->username)->orderBy($request->order_by ?? 'name')->withTrashed();
        return [
            'message' => 'Lista de usuarios',
            'payload' => UserResource::collection($data->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1))->resource,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        return [
            'message' => 'Usuario creado',
            'payload' => [
                'user' => new UserResource($user),
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        /** @var \App\Models\User */
        $auth_user = Auth::user();
        if ($auth_user->id == $user->id || $auth_user->can('LEER USUARIO')) {
            return [
                'message' => 'Datos de usuario',
                'payload' => [
                    'user' => new UserResource($user),
                ]
            ];
        }
        abort(403, 'Prohibido');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        if (auth()->user()->id == $user->id) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'message' => 'Credenciales inválidas',
                    'errors' => [
                        'old_password' => ['Contraseña actual incorrecta']
                    ]
                ], 400);
            }
        }
        $user->update($request->except('username'));
        return [
            'message' => 'Usuario actualizado',
            'payload' => [
                'user' => new UserResource($user),
            ]
        ];
        abort(403, 'Prohibido');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return [
            'message' => 'Usuario desactivado',
        ];
    }
}
