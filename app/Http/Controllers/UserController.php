<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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
        $data = User::where('username', '!=', 'admin')->orderBy($request->order_by ?? 'name')->withTrashed();
        return response()->json([
            'message' => 'Users list',
            'payload' => UserResource::collection($data->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1))->resource,
        ]);
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
            'message' => 'User created',
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
    public function show(Request $request, User $user)
    {
        if (auth()->user()->id == $user->id || $request->user()->hasRole('Super Admin')) {
            return [
                'message' => 'User data',
                'payload' => [
                    'user' => new UserResource($user),
                ]
            ];
        }
        abort(403, 'Not allowed');
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
        if (auth()->user()->id == $user->id || $request->user()->can('Update User')) {
            $user->update($request->except('username'));
            return [
                'message' => 'User updated',
                'payload' => [
                    'user' => new UserResource($user),
                ]
            ];
        }
        abort(403, 'Not allowed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin')) abort(403, 'Not allowed');
        $user->delete();
        return [
            'message' => 'User removed',
        ];
    }
}
