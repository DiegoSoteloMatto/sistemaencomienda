<?php

namespace App\Livewire\Encomiendas;

use App\Models\Encomienda;
use Livewire\Component;

class Show extends Component
{
    public Encomienda $encomienda;

    public function mount(int $id): void
    {
        $this->encomienda = Encomienda::with(['emisor', 'receptor', 'operador', 'repartidor', 'historialEstados.user'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.encomiendas.show')->layout('layouts.app');
    }
}
