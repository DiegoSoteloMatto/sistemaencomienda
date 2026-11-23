<?php

namespace App\Livewire\Clientes;

use App\Models\AuditLog;
use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal Form State
    public bool $showingModal = false;
    public ?int $editingClienteId = null;

    public string $nombre = '';
    public string $apellido = '';
    public string $documento_tipo = 'CI';
    public string $documento_numero = '';
    public string $telefono = '';
    public string $direccion = '';
    public string $correo_electronico = '';

    protected function rules(): array
    {
        $id = $this->editingClienteId;
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento_tipo' => 'required|in:CI,RUC,DNI,PASAPORTE',
            'documento_numero' => 'required|string|max:30|unique:clientes,documento_numero,' . ($id ?: 'NULL') . ',id,deleted_at,NULL',
            'telefono' => 'required|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'correo_electronico' => 'nullable|email|max:150',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingClienteId',
            'nombre',
            'apellido',
            'documento_numero',
            'telefono',
            'direccion',
            'correo_electronico',
        ]);
        $this->documento_tipo = 'CI';
        $this->resetValidation();
        $this->showingModal = true;
    }

    public function openEditModal(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->editingClienteId = $cliente->id;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
        $this->documento_tipo = $cliente->documento_tipo;
        $this->documento_numero = $cliente->documento_numero;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion ?? '';
        $this->correo_electronico = $cliente->correo_electronico ?? '';
        $this->resetValidation();
        $this->showingModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento_tipo' => $this->documento_tipo,
            'documento_numero' => $this->documento_numero,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion ?: null,
            'correo_electronico' => $this->correo_electronico ?: null,
        ];

        if ($this->editingClienteId) {
            $cliente = Cliente::findOrFail($this->editingClienteId);
            $cliente->update($data);

            AuditLog::log('actualizar_cliente', 'Cliente', $cliente->id, "Cliente actualizado: {$cliente->nombre_completo} ({$cliente->documento_completo})");
            session()->flash('message', 'Cliente actualizado exitosamente.');
        } else {
            $cliente = Cliente::create($data);

            AuditLog::log('crear_cliente', 'Cliente', $cliente->id, "Nuevo cliente registrado: {$cliente->nombre_completo} ({$cliente->documento_completo})");
            session()->flash('message', 'Cliente registrado exitosamente.');
        }

        $this->showingModal = false;
    }

    public function delete(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $nombre = $cliente->nombre_completo;
        $cliente->delete();

        AuditLog::log('eliminar_cliente', 'Cliente', $id, "Cliente eliminado lógicamente: {$nombre}");
        session()->flash('message', "Cliente '{$nombre}' eliminado correctamente.");
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->search}%")
                        ->orWhere('apellido', 'like', "%{$this->search}%")
                        ->orWhere('documento_numero', 'like', "%{$this->search}%")
                        ->orWhere('telefono', 'like', "%{$this->search}%")
                        ->orWhere('correo_electronico', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.clientes.index', [
            'clientes' => $clientes,
        ])->layout('layouts.app');
    }
}
