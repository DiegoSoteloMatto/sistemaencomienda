<?php

namespace App\Livewire\Repartidor;

use App\Models\Encomienda;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filtro = 'pendientes'; // 'pendientes', 'entregados', 'todos'

    // Modal Entrega
    public bool $showingEntregaModal = false;
    public ?int $encomiendaIdParaEntrega = null;
    public string $recibidoPorNombre = '';
    public string $recibidoPorCi = '';
    public string $observacionEntrega = '';

    // Modal Incidencia
    public bool $showingIncidenciaModal = false;
    public ?int $encomiendaIdParaIncidencia = null;
    public string $motivoIncidencia = 'Destinatario ausente';
    public string $detalleIncidencia = '';

    public function openEntregaModal(int $id): void
    {
        $encomienda = Encomienda::findOrFail($id);
        $this->encomiendaIdParaEntrega = $id;
        $this->recibidoPorNombre = $encomienda->receptor->nombre_completo;
        $this->recibidoPorCi = $encomienda->receptor->documento_numero;
        $this->observacionEntrega = 'Entrega realizada conforme';
        $this->showingEntregaModal = true;
    }

    public function registrarEntrega(): void
    {
        $this->validate([
            'recibidoPorNombre' => 'required|string|max:150',
            'recibidoPorCi' => 'required|string|max:50',
            'observacionEntrega' => 'nullable|string|max:500',
        ]);

        $encomienda = Encomienda::findOrFail($this->encomiendaIdParaEntrega);
        $obs = "Entregado a {$this->recibidoPorNombre} (Doc: {$this->recibidoPorCi}). " . $this->observacionEntrega;

        try {
            $encomienda->cambiarEstado('entregado', $obs, auth()->id());
            session()->flash('message', "Encomienda {$encomienda->tracking_id} marcada como ENTREGADA exitosamente.");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showingEntregaModal = false;
    }

    public function openIncidenciaModal(int $id): void
    {
        $this->encomiendaIdParaIncidencia = $id;
        $this->motivoIncidencia = 'Destinatario ausente';
        $this->detalleIncidencia = '';
        $this->showingIncidenciaModal = true;
    }

    public function registrarIncidencia(): void
    {
        $this->validate([
            'motivoIncidencia' => 'required|string',
            'detalleIncidencia' => 'nullable|string|max:500',
        ]);

        $encomienda = Encomienda::findOrFail($this->encomiendaIdParaIncidencia);
        $obs = "Incidencia: {$this->motivoIncidencia}. " . $this->detalleIncidencia;

        try {
            $encomienda->cambiarEstado('incidencia', $obs, auth()->id());
            session()->flash('message', "Incidencia registrada para la encomienda {$encomienda->tracking_id}.");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showingIncidenciaModal = false;
    }

    public function iniciarReparto(int $id): void
    {
        $encomienda = Encomienda::findOrFail($id);
        try {
            $encomienda->cambiarEstado('reparto', 'Repartidor en camino con el paquete', auth()->id());
            session()->flash('message', "Encomienda {$encomienda->tracking_id} puesta en reparto.");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $userId = auth()->id();
        $query = Encomienda::with(['emisor', 'receptor'])
            ->where(function ($q) use ($userId) {
                $q->where('repartidor_id', $userId)
                  ->orWhereNull('repartidor_id'); // si el admin o repartidor quiere ver asignables
            });

        if ($this->filtro === 'pendientes') {
            $query->whereIn('estado_actual', ['arribado', 'reparto', 'transito']);
        } elseif ($this->filtro === 'entregados') {
            $query->where('estado_actual', 'entregado');
        } elseif ($this->filtro === 'incidencia') {
            $query->where('estado_actual', 'incidencia');
        }

        $encomiendas = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.repartidor.index', [
            'encomiendas' => $encomiendas,
        ])->layout('layouts.app');
    }
}
