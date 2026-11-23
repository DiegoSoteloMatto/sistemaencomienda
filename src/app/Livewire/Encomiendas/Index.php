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
                    $sub->where('tracking_id', 'like', "%{$this->search}%")
                        ->orWhere('origen', 'like', "%{$this->search}%")
                        ->orWhere('destino', 'like', "%{$this->search}%")
                        ->orWhereHas('emisor', function ($e) {
                            $e->where('nombre', 'like', "%{$this->search}%")
                              ->orWhere('apellido', 'like', "%{$this->search}%")
                              ->orWhere('documento_numero', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('receptor', function ($r) {
                            $r->where('nombre', 'like', "%{$this->search}%")
                              ->orWhere('apellido', 'like', "%{$this->search}%")
                              ->orWhere('documento_numero', 'like', "%{$this->search}%");
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
