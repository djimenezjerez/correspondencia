<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Procedure;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReportRequest $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->startOfDay();
        $query = Procedure::has('procedure_flows', '>', 1)->whereDate('updated_at', '>=', $start_date)->whereDate('updated_at', '<=', $end_date);
        if ($user->hasRole('ADMINISTRADOR')) {
            if ($request->has('area_id')) {
                $query->where('area_id', $request->area_id);
            }
        } else {
            $query->where('area_id', $user->area_id);
        }
        if ($request->has('search_by')) {
            switch ($request->search_by) {
                case 'pending':
                    $type = 'PENDIENTES';
                    $query->where('pending', true)->where('archived', false);
                    break;
                case 'received':
                    $type = 'RECEPCIONADOS';
                    $query->where('pending', false)->where('archived', false);
                    break;
                case 'archived':
                    $type = 'ARCHIVADOS';
                    $query->where('pending', false)->where('archived', true);
                    break;
            }
        }
        if ($request->has('procedure_type_id')) {
            $query->where('procedure_type_id', $request->procedure_type_id);
        }
        $procedures = $query->with('latest_flow', 'latest_flow.flowed_from_area', 'latest_flow.flowed_to_area', 'latest_flow.user', 'procedure_type', 'user', 'area')->get();
        if ($procedures->count() == 0) {
            return response()->json([
                'message' => 'No se encontraron resultados',
            ], 404);
        }
        $list = [];
        try {
            foreach ($procedures as $procedure) {
                // $list = array_fill(0, 40, [
                $list[] = [
                    'counter' => $procedure->counter,
                    'code' => array_slice(explode('\n', wordwrap($procedure->code, 12, '\n')), 0, 2),
                    'procedure_type' => array_slice(explode('\n', wordwrap($procedure->procedure_type->name, 12, '\n')), 0, 2),
                    'origin' => array_slice(explode('\n', wordwrap($procedure->origin, 30, '\n')), 0, 2),
                    'detail' => array_slice(explode('\n', wordwrap($procedure->detail, 30, '\n')), 0, 2),
                    'from' => array_slice(explode('\n', wordwrap(mb_convert_case($procedure->latest_flow->user->full_name, MB_CASE_TITLE, 'UTF-8') . ' / ' . $procedure->latest_flow->flowed_from_area->name, 30, '\n')), 0, 2),
                    'current' => array_slice(explode('\n', wordwrap(mb_convert_case($procedure->user ? $procedure->user->full_name : 'Pendiente', MB_CASE_TITLE, 'UTF-8') . ' / ' . $procedure->area->name, 30, '\n')), 0, 2),
                ];
                // ]);
            }
            $data = [
                'filename' => 'REPORTE ' . $type . ' - ' . Carbon::now()->format('d-m-Y H-i') . '.pdf',
                'logo' => 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/logo_low.png'))),
                'type' => $type,
                'dates' => [$start_date->format('d/m/Y'), $end_date->format('d/m/Y')],
                'procedures' => $list,
            ];
            $pdf = App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('pdf.report', $data);
            $pdf->setPaper('letter', 'landscape');
            return [
                'message' => 'PDF generado',
                'payload' => [
                    'file' => [
                        'content' => base64_encode($pdf->output()),
                        'name' => $data['filename'],
                    ],
                ],
            ];
        } catch(\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el PDF',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procedure  $procedure
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
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Procedure $procedure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure)
    {
        //
    }
}
