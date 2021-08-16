<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AuthRequest $request)
    {
        $user = User::whereUsername($request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $tokens = $user->tokens()->count();
                if ($tokens == 1) {
                    $token = $user->getRememberToken();
                } else {
                    if ($tokens > 1) {
                        $user->tokens()->delete();
                    }
                    $token = $user->createToken('api')->plainTextToken;
                    $user->remember_token = $token;
                    $user->save();
                }
                return [
                    'message' => 'Sesión iniciada',
                    'payload' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'user' => new UserResource($user),
                        'permissions' => $user->getAllPermissions()->pluck('name')->unique(),
                    ],
                ];
            }
        }
        return response()->json([
            'message' => 'Credenciales inválidas',
            'errors' => [
                'username' => ['Usuario o contraseña incorrecta']
            ]
        ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $user->setRememberToken(null);
        $user->tokens()->delete();
        return [
            'message' => 'Sesión finalizada',
        ];
    }
}
