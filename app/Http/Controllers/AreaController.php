<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'message' => 'Lista de áreas',
            'payload' => [
                'areas' => Area::orderBy('order', 'ASC')->orderBy('name', 'ASC')->get(),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($user->area_id == $area->id || $user->can('LEER ÁREA')) {
            return [
                'message' => 'Datos de área',
                'payload' => [
                    'area' => $area,
                ],
            ];
        }
        abort(403, 'Prohibido');
    }
}
