<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\Procedure;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\FileUploadResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileUploadController extends Controller
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
                'files' => FileUploadResource::collection($procedure->file_uploads),
            ]
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadRequest $request, Procedure $procedure)
    {
        try {
            $files = [];
            foreach ($request->file('attachments') as $file) {
                $name = trim(mb_strtoupper($file->getClientOriginalName()));
                $path = 'uploads/procedures/'.$procedure->id.'/attachments';
                $file_stored = $procedure->file_uploads()->where('filename', $name)->where('path', $path)->first();
                if ($file_stored) {
                    $files[] = $file_stored->setAttribute('success', false);
                } else {
                    $file->storeAs($path, $name);
                    $file = $procedure->file_uploads()->create([
                        'filename' => $name,
                        'path' => $path,
                        'user_id' => auth()->user()->id,
                    ]);
                    $files[] = $file->setAttribute('success', true);
                }
            }
            return [
                'message' => 'Archivos cargados correctamente',
                'payload' => [
                    'files' => $files,
                ],
            ];
        } catch(\Exception $e) {
            abort(500, 'Error al cargar los archivos');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function show(Procedure $procedure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FileUpload $fileUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure, FileUpload $file_upload)
    {
        if ($procedure->area_id != auth()->user()->area_id) abort(403, 'El trámite no se encuentra en su bandeja');
        $file = $file_upload->path . '/' . $file_upload->name;
        if (Storage::exists($file)) {
            Storage::delete($file);
        }
        $file_upload->delete();
        return [
            'message' => 'Archivo adjunto eliminado',
        ];
    }
}
