<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;

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
                    $token = $user->remember_token;
                } else {
                    if ($tokens > 1) {
                        $user->tokens()->delete();
                    }
                    $token = $user->createToken('api')->plainTextToken;
                    $user->remember_token = $token;
                    $user->save();
                }
                return [
                    'message' => 'Logged in',
                    'payload' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'user' => new UserResource($user),
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $user->getAllPermissions()->pluck('name')->unique(),
                    ],
                ];
            }
        }
        return response()->json([
            'message' => 'Invalid credentials',
            'errors' => [
                'username' => ['Username or password incorrect']
            ]
        ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }
}
