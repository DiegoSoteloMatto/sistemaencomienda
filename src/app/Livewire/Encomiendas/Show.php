<?php

namespace App\Livewire\Encomiendas;

use App\Models\Encomienda;
use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public Encomienda $encomienda;

    // Estado Modal Cambio de Estado
    public bool $showingStateModal = false;
    public string $nuevoEstado = '';
    public string $observaciones = '';
    public ?int $selectedRepartidorId = null;

    public function mount(int $id): void
    {
        $this->loadEncomienda($id);
    }

    public function loadEncomienda(int $id): void
    {
        $this->encomienda = Encomienda::with(['emisor', 'receptor', 'operador', 'repartidor', 'historialEstados.user'])
            ->findOrFail($id);
    }

    public function openStateModal(): void
    {
        $permitidos = $this->encomienda->transicionesPermitidas();
        $this->nuevoEstado = $permitidos[0] ?? '';
        $this->observaciones = '';
        $this->selectedRepartidorId = $this->encomienda->repartidor_id;
        $this->showingStateModal = true;
    }

    public function actualizarEstado(): void
    {
        $this->validate([
            'nuevoEstado' => 'required|string',
            'observaciones' => 'nullable|string|max:500',
            'selectedRepartidorId' => 'nullable|exists:users,id',
        ]);

        try {
            $this->encomienda->cambiarEstado(
                $this->nuevoEstado,
                $this->observaciones,
                auth()->id(),
                $this->selectedRepartidorId
            );

            session()->flash('message', "Estado actualizado a '{$this->encomienda->estado_label}' correctamente.");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showingStateModal = false;
        $this->loadEncomienda($this->encomienda->id);
    }

    public function render()
    {
        $repartidores = User::role('Repartidor')->where('activo', true)->get();
        $estadosPermitidos = $this->encomienda->transicionesPermitidas();

        return view('livewire.encomiendas.show', [
            'repartidores' => $repartidores,
            'estadosPermitidos' => $estadosPermitidos,
        ])->layout('layouts.app');
    }
}
