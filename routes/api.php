<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\ProcedureTypeController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\AttachmentController;
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

// Autenticación
Route::post('login', [AuthController::class, 'store']);

// Consulta externa
Route::get('procedure/{procedure}/flow', [ProcedureController::class, 'timeline']);
Route::get('procedure_code', [ProcedureController::class, 'code']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    // Cerrar sesión
    Route::post('logout', [AuthController::class, 'destroy']);

    // Roles
    Route::get('role', [RoleController::class, 'index'])->middleware('permission:LEER ROL');
    Route::get('role/{role}', [RoleController::class, 'show']);

    // Secciones
    Route::get('area', [AreaController::class, 'index']);
    Route::get('area/{area}', [AreaController::class, 'show']);

    // Usuarios
    Route::get('user', [UserController::class, 'index'])->middleware('permission:LEER USUARIO');
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy'])->middleware('permission:ELIMINAR USUARIO');
    Route::patch('deleted/user/{user}', [UserController::class, 'restore'])->middleware('permission:CREAR USUARIO');

    // Requisitos
    Route::get('requirement', [RequirementController::class, 'index']);
    Route::get('requirement/{requirement}', [RequirementController::class, 'show']);
    Route::post('requirement', [RequirementController::class, 'store'])->middleware('permission:CREAR REQUISITO');
    Route::patch('requirement/{requirement}', [RequirementController::class, 'update'])->middleware('permission:EDITAR REQUISITO');
    Route::delete('requirement/{requirement}', [RequirementController::class, 'destroy'])->middleware('permission:ELIMINAR REQUISITO');

    // Tipos de trámite
    Route::get('procedure_type', [ProcedureTypeController::class, 'index']);
    Route::get('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'show']);
    Route::get('procedure_type/{procedure_type}/code', [ProcedureTypeController::class, 'getCode']);
    Route::post('procedure_type', [ProcedureTypeController::class, 'store'])->middleware('permission:CREAR TIPO TRÁMITE');
    Route::patch('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'update'])->middleware('permission:EDITAR TIPO TRÁMITE');
    Route::delete('procedure_type/{procedure_type}', [ProcedureTypeController::class, 'destroy'])->middleware('permission:ELIMINAR TIPO TRÁMITE');

    // Trámites
    Route::get('procedure', [ProcedureController::class, 'index'])->middleware('permission:LEER TRÁMITE');
    Route::get('procedure/{procedure}', [ProcedureController::class, 'show'])->middleware('permission:LEER TRÁMITE');
    Route::post('procedure', [ProcedureController::class, 'store'])->middleware('permission:CREAR TRÁMITE');
    Route::patch('procedure/{procedure}', [ProcedureController::class, 'update']);
    Route::delete('procedure/{procedure}', [ProcedureController::class, 'destroy'])->middleware('permission:ELIMINAR TRÁMITE');

    // Requisitos de trámite
    Route::patch('procedure/{procedure}/requirement', [ProcedureController::class, 'requirements'])->middleware('role:VERIFICADOR');

    // Derivar trámite
    Route::post('procedure/{procedure}/flow', [ProcedureController::class, 'flow'])->middleware('permission:DERIVAR TRÁMITE');

    // Impresión de PDF
    Route::post('procedure/{procedure}/print', [ProcedureController::class, 'print'])->middleware('permission:DERIVAR TRÁMITE');

    // Archivar trámite
    Route::post('procedure/{procedure}/archive', [ProcedureController::class, 'archive'])->middleware('permission:ARCHIVAR TRÁMITE');

    // Archivos adjuntos a trámites
    Route::get('procedure/{procedure}/attachment', [AttachmentController::class, 'index'])->middleware('permission:LEER TRÁMITE');
    Route::get('procedure/{procedure}/attachment/{attachment}', [AttachmentController::class, 'show']);
    Route::post('procedure/{procedure}/attachment', [AttachmentController::class, 'store'])->middleware('permission:ADJUNTAR ARCHIVO');
    Route::delete('procedure/{procedure}/attachment/{attachment}', [AttachmentController::class, 'destroy']);

    // Bandeja de trámites
    Route::get('procedure/tray/pending', [ProcedureController::class, 'pending']);
    Route::post('procedure/tray/receive', [ProcedureController::class, 'receive']);
});