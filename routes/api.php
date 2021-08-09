<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout', [AuthController::class, 'destroy']);
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::get('role', [RoleController::class, 'index']);
    Route::get('role/{role}/permission', [PermissionController::class, 'index']);
    Route::post('user', [UserController::class, 'store'])->middleware('permission:CREATE USER');
    Route::get('user', [UserController::class, 'index'])->middleware('permission:READ USER');
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy'])->middleware('permission:DELETE USER');
});