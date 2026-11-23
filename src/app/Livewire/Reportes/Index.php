<?php

namespace App\Livewire\Reportes;

use App\Models\Encomienda;
use App\Models\HistorialEstado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $activeTab = 'volumen'; // 'volumen' o 'tiempos'

    // Filtros
    public string $fechaDesde = '';
    public string $fechaHasta = '';
    public string $origen = '';
    public string $destino = '';
    public string $estado = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        // Por defecto último mes
        $this->fechaDesde = now()->subDays(30)->format('Y-m-d');
        $this->fechaHasta = now()->format('Y-m-d');
    }

    public function updatingFechaDesde(): void
    {
        $this->resetPage();
    }

    public function updatingFechaHasta(): void
    {
        $this->resetPage();
    }

    public function updatingOrigen(): void
    {
        $this->resetPage();
    }

    public function updatingDestino(): void
    {
        $this->resetPage();
    }

    public function updatingEstado(): void
    {
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        return Encomienda::with(['emisor', 'receptor'])
            ->when($this->fechaDesde, function ($q) {
                $q->whereDate('created_at', '>=', $this->fechaDesde);
            })
            ->when($this->fechaHasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->fechaHasta);
            })
            ->when($this->origen, function ($q) {
                $q->where('origen', $this->origen);
            })
            ->when($this->destino, function ($q) {
                $q->where('destino', $this->destino);
            })
            ->when($this->estado, function ($q) {
                $q->where('estado_actual', $this->estado);
            });
    }

    public function render()
    {
        // Consulta base para agregados
        $query = $this->getFilteredQuery();

        // 1. Métricas de Volumen (RF-06.1)
        $totalEnvios = (clone $query)->count();
        $totalPeso = (clone $query)->sum('peso_kg');
        $recaudacionTotal = (clone $query)->sum('costo_servicio');
        $promedioCosto = $totalEnvios > 0 ? $recaudacionTotal / $totalEnvios : 0;

        // Distribución por estado
        $distribucionEstados = (clone $query)
            ->select('estado_actual', DB::raw('count(*) as total'))
            ->groupBy('estado_actual')
            ->pluck('total', 'estado_actual')
            ->toArray();

        // Top Rutas
        $topRutas = (clone $query)
            ->select('origen', 'destino', DB::raw('count(*) as total'))
            ->groupBy('origen', 'destino')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 2. Tiempos promedio de tránsito (RF-06.2)
        $tiempos = $this->calcularTiemposPromedio();

        // Encomiendas con riesgo o estancadas (> 48 horas sin cambio de estado y no entregadas)
        $limiteHoras = now()->subHours(48);
        $paquetesEstancados = Encomienda::whereNotIn('estado_actual', ['entregado'])
            ->where('updated_at', '<=', $limiteHoras)
            ->with(['emisor', 'receptor'])
            ->take(10)
            ->get();

        // Encomiendas paginadas para la tabla
        $encomiendas = (clone $query)->orderBy('id', 'desc')->paginate(10);

        return view('livewire.reportes.index', [
            'totalEnvios' => $totalEnvios,
            'totalPeso' => $totalPeso,
            'recaudacionTotal' => $recaudacionTotal,
            'promedioCosto' => $promedioCosto,
            'distribucionEstados' => $distribucionEstados,
            'topRutas' => $topRutas,
            'tiempos' => $tiempos,
            'paquetesEstancados' => $paquetesEstancados,
            'encomiendas' => $encomiendas,
        ])->layout('layouts.app');
    }

    private function calcularTiemposPromedio(): array
    {
        // Obtener encomiendas entregadas para analizar sus tiempos
        $entregadas = Encomienda::where('estado_actual', 'entregado')
            ->with(['historialEstados'])
            ->take(50)
            ->get();

        if ($entregadas->isEmpty()) {
            return [
                'promedio_total_horas' => 0,
                'recibido_a_clasificacion' => 0,
                'clasificacion_a_transito' => 0,
                'transito_a_arribado' => 0,
                'arribado_a_reparto' => 0,
                'reparto_a_entrega' => 0,
                'total_analizadas' => 0,
            ];
        }

        $duracionesTotales = [];
        $tramos = [
            'recibido_clasificacion' => [],
            'clasificacion_transito' => [],
            'transito_arribado' => [],
            'arribado_reparto' => [],
            'reparto_entrega' => [],
        ];

        foreach ($entregadas as $enc) {
            $historial = $enc->historialEstados->sortBy('timestamp');
            $primer = $historial->first();
            $ultimo = $historial->where('estado_nuevo', 'entregado')->first();

            if ($primer && $ultimo && $primer->timestamp && $ultimo->timestamp) {
                $duracionesTotales[] = $primer->timestamp->diffInHours($ultimo->timestamp);
            }

            // Analizar pares de etapas
            $prev = null;
            foreach ($historial as $h) {
                if ($prev && $prev->timestamp && $h->timestamp) {
                    $diff = $prev->timestamp->diffInMinutes($h->timestamp) / 60.0;
                    if ($prev->estado_nuevo === 'recibido' && $h->estado_nuevo === 'clasificacion') {
                        $tramos['recibido_clasificacion'][] = $diff;
                    } elseif ($prev->estado_nuevo === 'clasificacion' && $h->estado_nuevo === 'transito') {
                        $tramos['clasificacion_transito'][] = $diff;
                    } elseif ($prev->estado_nuevo === 'transito' && $h->estado_nuevo === 'arribado') {
                        $tramos['transito_arribado'][] = $diff;
                    } elseif ($prev->estado_nuevo === 'arribado' && $h->estado_nuevo === 'reparto') {
                        $tramos['arribado_reparto'][] = $diff;
                    } elseif ($prev->estado_nuevo === 'reparto' && $h->estado_nuevo === 'entregado') {
                        $tramos['reparto_entrega'][] = $diff;
                    }
                }
                $prev = $h;
            }
        }

        $avg = fn($arr) => count($arr) > 0 ? round(array_sum($arr) / count($arr), 1) : 0;

        return [
            'promedio_total_horas' => $avg($duracionesTotales),
            'recibido_a_clasificacion' => $avg($tramos['recibido_clasificacion']),
            'clasificacion_a_transito' => $avg($tramos['clasificacion_transito']),
            'transito_a_arribado' => $avg($tramos['transito_arribado']),
            'arribado_a_reparto' => $avg($tramos['arribado_reparto']),
            'reparto_a_entrega' => $avg($tramos['reparto_entrega']),
            'total_analizadas' => $entregadas->count(),
        ];
    }
}
