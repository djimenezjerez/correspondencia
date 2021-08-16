<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DocumentTypeController;
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
    Route::get('role', [RoleController::class, 'index'])->middleware('permission:LEER ROL');
    Route::get('role/{role}', [RoleController::class, 'show']);
    Route::get('area', [AreaController::class, 'index'])->middleware('permission:LEER ÁREA');
    Route::get('area/{area}', [AreaController::class, 'show']);
    Route::get('document_type', [DocumentTypeController::class, 'index'])->middleware('permission:LEER TIPO DOCUMENTO');
    Route::get('document_type/{document_type}', [DocumentTypeController::class, 'show']);
    Route::get('user', [UserController::class, 'index'])->middleware('permission:LEER USUARIO');
    Route::post('user', [UserController::class, 'store'])->middleware('permission:CREAR USUARIO');
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy'])->middleware('permission:ELIMINAR USUARIO');
    Route::patch('deleted/user/{user}', [UserController::class, 'restore'])->middleware('permission:CREAR USUARIO');
    // Route::get('role/{role}/permission', [PermissionController::class, 'index']);
});