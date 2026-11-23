<?php

namespace App\Livewire\Trazabilidad;

use App\Models\Encomienda;
use Livewire\Component;

class PublicTracking extends Component
{
    public string $codigo = '';
    public ?Encomienda $encomienda = null;
    public bool $buscado = false;

    public function mount(?string $codigo = null): void
    {
        if ($codigo) {
            $this->codigo = trim($codigo);
            $this->buscar();
        } elseif (request()->has('codigo')) {
            $this->codigo = trim(request('codigo'));
            $this->buscar();
        }
    }

    public function buscar(): void
    {
        $this->codigo = trim($this->codigo);
        $this->buscado = true;

        if (empty($this->codigo)) {
            $this->encomienda = null;
            return;
        }

        $this->encomienda = Encomienda::with(['historialEstados', 'emisor', 'receptor'])
            ->whereRaw('LOWER(tracking_id) = ?', [strtolower($this->codigo)])
            ->first();
    }

    public function render()
    {
        return view('livewire.trazabilidad.public-tracking')
            ->layout('layouts.tracking');
    }
}
