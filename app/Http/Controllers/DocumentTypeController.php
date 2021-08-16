<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'message' => 'Lista de tipos de documento',
            'payload' => [
                'document_types' => DocumentType::orderBy('name')->get(),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentType  $documentType
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentType $document_type)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($user->document_type_id == $document_type->id || $user->can('LEER TIPO DOCUMENTO')) {
            return [
                'message' => 'Datos de tipo de documento',
                'payload' => [
                    'document_type' => $document_type,
                ],
            ];
        }
        abort(403, 'Prohibido');
    }
}
