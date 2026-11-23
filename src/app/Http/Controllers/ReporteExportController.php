<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteExportController extends Controller
{
    private function buildQuery(Request $request)
    {
        return Encomienda::with(['emisor', 'receptor'])
            ->when($request->fecha_desde, function ($q, $v) {
                $q->whereDate('created_at', '>=', $v);
            })
            ->when($request->fecha_hasta, function ($q, $v) {
                $q->whereDate('created_at', '<=', $v);
            })
            ->when($request->origen, function ($q, $v) {
                $q->where('origen', $v);
            })
            ->when($request->destino, function ($q, $v) {
                $q->where('destino', $v);
            })
            ->when($request->estado, function ($q, $v) {
                $q->where('estado_actual', $v);
            })
            ->orderBy('id', 'desc');
    }

    public function exportarCsv(Request $request): StreamedResponse
    {
        $encomiendas = $this->buildQuery($request)->get();
        $fileName = 'reporte_encomiendas_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($encomiendas) {
            $handle = fopen('php://output', 'w');
            // Escribir BOM UTF-8 para apertura correcta en Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Encabezados
            fputcsv($handle, [
                'Tracking ID',
                'Fecha de Registro',
                'Remitente',
                'Doc. Remitente',
                'Destinatario',
                'Doc. Destinatario',
                'Origen',
                'Destino',
                'Peso (kg)',
                'Costo Servicio (Gs.)',
                'Estado Actual',
                'Descripción',
            ], ';');

            foreach ($encomiendas as $item) {
                fputcsv($handle, [
                    $item->tracking_id,
                    $item->created_at->format('d/m/Y H:i'),
                    $item->emisor->nombre_completo,
                    $item->emisor->documento_completo,
                    $item->receptor->nombre_completo,
                    $item->receptor->documento_completo,
                    $item->origen,
                    $item->destino,
                    number_format($item->peso_kg, 2, ',', '.'),
                    $item->costo_servicio,
                    $item->estado_label,
                    $item->descripcion_paquete ?? '',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportarPdf(Request $request)
    {
        $encomiendas = $this->buildQuery($request)->get();

        $resumen = [
            'total_envios' => $encomiendas->count(),
            'total_peso' => $encomiendas->sum('peso_kg'),
            'recaudacion_total' => $encomiendas->sum('costo_servicio'),
            'promedio_costo' => $encomiendas->count() > 0 ? $encomiendas->avg('costo_servicio') : 0,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'filtros' => [
                'desde' => $request->fecha_desde ?: 'Inicio',
                'hasta' => $request->fecha_hasta ?: 'Actualidad',
                'origen' => $request->origen ?: 'Todos',
                'destino' => $request->destino ?: 'Todos',
                'estado' => $request->estado ?: 'Todos',
            ],
        ];

        $pdf = Pdf::loadView('reportes.pdf', [
            'encomiendas' => $encomiendas,
            'resumen' => $resumen,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte_encomiendas_' . date('Ymd_His') . '.pdf');
    }
}
