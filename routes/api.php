<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\ProcedureTypeController;
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

    // Roles
    Route::get('role', [RoleController::class, 'index'])->middleware('permission:LEER ROL');
    Route::get('role/{role}', [RoleController::class, 'show']);

    // Tipos de documento
    Route::get('document_type', [DocumentTypeController::class, 'index'])->middleware('permission:LEER TIPO DOCUMENTO');
    Route::get('document_type/{document_type}', [DocumentTypeController::class, 'show']);

    // Áreas
    Route::get('area', [AreaController::class, 'index'])->middleware('permission:LEER ÁREA');
    Route::get('area/{area}', [AreaController::class, 'show']);

    // Usuarios
    Route::get('user', [UserController::class, 'index'])->middleware('permission:LEER USUARIO');
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy'])->middleware('permission:ELIMINAR USUARIO');
    Route::patch('deleted/user/{user}', [UserController::class, 'restore'])->middleware('permission:CREAR USUARIO');

    // Requisitos
    Route::get('requirement', [RequirementController::class, 'index'])->middleware('permission:LEER REQUISITO');
    Route::get('requirement/{requirement}', [RequirementController::class, 'show'])->middleware('permission:LEER REQUISITO');
    Route::post('requirement', [RequirementController::class, 'store'])->middleware('permission:CREAR REQUISITO');
    Route::patch('requirement/{requirement}', [RequirementController::class, 'update'])->middleware('permission:EDITAR REQUISITO');
    Route::delete('requirement/{requirement}', [RequirementController::class, 'destroy'])->middleware('permission:ELIMINAR REQUISITO');

    // Tipos de trámite
    Route::get('procedure_type', [ProcedureTypeController::class, 'index'])->middleware('permission:LEER TIPO TRÁMITE');
    Route::get('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'show'])->middleware('permission:LEER TIPO TRÁMITE');
    Route::post('procedure_type', [ProcedureTypeController::class, 'store'])->middleware('permission:CREAR TIPO TRÁMITE');
    Route::patch('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'update'])->middleware('permission:EDITAR TIPO TRÁMITE');
    Route::delete('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'destroy'])->middleware('permission:ELIMINAR TIPO TRÁMITE');
});