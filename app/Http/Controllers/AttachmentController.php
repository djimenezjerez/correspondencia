<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Procedure;
use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Procedure $procedure)
    {
        return [
            'message' => 'Lista de archivos adjuntos',
            'payload' => [
                'files' => AttachmentResource::collection($procedure->attachments),
            ]
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttachmentRequest $request, Procedure $procedure)
    {
        foreach ($request->file('attachments') as $file) {
            $name = trim(mb_strtoupper($file->getClientOriginalName()));
            $path = 'uploads/procedures/'.$procedure->id.'/attachments';
            $file_stored = $procedure->attachments()->where('filename', $name)->where('path', $path)->first();
            if ($file_stored) {
                abort(422, 'Error al cargar el archivo '.$name.' porque ya se encuentra cargado');
            }
        }
        foreach ($request->file('attachments') as $file) {
            $name = trim(mb_strtoupper($file->getClientOriginalName()));
            $path = 'uploads/procedures/'.$procedure->id.'/attachments';
            $file->storeAs($path, $name);
            $file = $procedure->attachments()->create([
                'filename' => $name,
                'path' => $path,
                'user_id' => auth()->user()->id,
            ]);
        }
        return [
            'message' => 'Archivos cargados correctamente',
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attachment  $Attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Procedure $procedure, Attachment $attachment)
    {
        if (Storage::exists($attachment->full_path)) {
            return Storage::download($attachment->full_path);
        } else {
            abort(404, 'El archivo solicitado fue eliminado');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attachment  $Attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attachment $Attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attachment  $Attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure, Attachment $attachment)
    {
        $file = $attachment->path . '/' . $attachment->name;
        if (Storage::exists($file)) {
            Storage::delete($file);
        }
        $attachment->delete();
        return [
            'message' => 'Archivo adjunto eliminado',
        ];
    }
}
