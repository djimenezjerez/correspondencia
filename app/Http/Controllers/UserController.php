<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::where('username', '!=', auth()->user()->username);
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
                    return $q->orWhere(DB::raw('upper(name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(last_name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(identity_card)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere('phone', 'like', '%'.$request->search.'%')->orWhere(DB::raw('upper(username)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        $query->withTrashed();
        return [
            'message' => 'Lista de usuarios',
            'payload' => UserResource::collection($query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1))->resource,
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
        $area = Area::findOrFail($request->area_id);
        $request->merge([
            'password' => $request->identity_card,
        ]);
        $user = User::create($request->all());
        $user->syncRoles([$area->role_id]);
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
                    'user' => collect([
                        'area' => $user->area->name,
                    ])->merge(new UserResource($user)),
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
        if ($request->has('username')) {
            if (User::whereUsername($request->username)->where('id', '!=', $user->id)->withTrashed()->exists()) {
                return response()->json([
                    'message' => 'Nombre de usuario inválido',
                    'errors' => [
                        'username' => ['El nombre de usuario ya existe']
                    ]
                ], 400);
            }
        }
        if ($request->has('identity_card')) {
            if (User::whereIdentityCard($request->identity_card)->where('id', '!=', $user->id)->withTrashed()->exists()) {
                return response()->json([
                    'message' => 'Documento de identidad inválido',
                    'errors' => [
                        'username' => ['El documento de identidad ya existe']
                    ]
                ], 400);
            }
        }
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
        $user->update($request->all());
        if ($request->has('area_id')) {
            $area = Area::findOrFail($request->area_id);
            $user->syncRoles([$area->role_id]);
        }
        return [
            'message' => 'Datos de usuario actualizados',
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

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function restore($user)
    {
        User::withTrashed()->where('id', $user)->restore();
        return [
            'message' => 'Usuario reactivado',
        ];
    }

}
