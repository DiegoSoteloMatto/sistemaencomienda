<?php

namespace App\Livewire\Encomiendas;

use App\Models\Encomienda;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $estadoFilter = '';
    public string $fechaDesde = '';
    public string $fechaHasta = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEstadoFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $encomiendas = Encomienda::with(['emisor', 'receptor', 'operador'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('tracking_id', 'ilike', "%{$this->search}%")
                        ->orWhere('origen', 'ilike', "%{$this->search}%")
                        ->orWhere('destino', 'ilike', "%{$this->search}%")
                        ->orWhereHas('emisor', function ($e) {
                            $e->where('nombre', 'ilike', "%{$this->search}%")
                              ->orWhere('apellido', 'ilike', "%{$this->search}%")
                              ->orWhere('documento_numero', 'ilike', "%{$this->search}%");
                        })
                        ->orWhereHas('receptor', function ($r) {
                            $r->where('nombre', 'ilike', "%{$this->search}%")
                              ->orWhere('apellido', 'ilike', "%{$this->search}%")
                              ->orWhere('documento_numero', 'ilike', "%{$this->search}%");
                        });
                });
            })
            ->when($this->estadoFilter, function ($q) {
                $q->where('estado_actual', $this->estadoFilter);
            })
            ->when($this->fechaDesde, function ($q) {
                $q->whereDate('created_at', '>=', $this->fechaDesde);
            })
            ->when($this->fechaHasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->fechaHasta);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.encomiendas.index', [
            'encomiendas' => $encomiendas,
        ])->layout('layouts.app');
    }
}
