<?php

namespace App\Livewire\Encomiendas;

use App\Models\AuditLog;
use App\Models\Cliente;
use App\Models\Encomienda;
use Livewire\Component;

class Create extends Component
{
    // Remitente / Emisor
    public string $searchEmisor = '';
    public ?int $cliente_emisor_id = null;
    public ?Cliente $emisorSeleccionado = null;

    // Destinatario / Receptor
    public string $searchReceptor = '';
    public ?int $cliente_receptor_id = null;
    public ?Cliente $receptorSeleccionado = null;

    // Datos del Envío
    public string $origen = 'Encarnación';
    public string $destino = 'Asunción';
    public string $descripcion_paquete = '';
    public float $peso_kg = 1.0;
    public ?float $dimensiones_largo = null;
    public ?float $dimensiones_ancho = null;
    public ?float $dimensiones_alto = null;
    public float $costo_servicio = 20000;

    // Modal Crear Cliente Rápido
    public bool $showingQuickClientModal = false;
    public string $quickClientTarget = 'emisor'; // 'emisor' o 'receptor'
    public string $quick_nombre = '';
    public string $quick_apellido = '';
    public string $quick_documento_tipo = 'CI';
    public string $quick_documento_numero = '';
    public string $quick_telefono = '';
    public string $quick_direccion = '';
    public string $quick_correo = '';

    protected function rules(): array
    {
        return [
            'cliente_emisor_id' => 'required|exists:clientes,id',
            'cliente_receptor_id' => 'required|different:cliente_emisor_id|exists:clientes,id',
            'origen' => 'required|string|max:100',
            'destino' => 'required|string|max:100',
            'descripcion_paquete' => 'nullable|string|max:500',
            'peso_kg' => 'required|numeric|min:0.1|max:1000',
            'dimensiones_largo' => 'nullable|numeric|min:1',
            'dimensiones_ancho' => 'nullable|numeric|min:1',
            'dimensiones_alto' => 'nullable|numeric|min:1',
            'costo_servicio' => 'required|numeric|min:0',
        ];
    }

    public function mount(): void
    {
        $this->calcularTarifa();
    }

    public function updatedPesoKg(): void
    {
        $this->calcularTarifa();
    }

    public function calcularTarifa(): void
    {
        // Tarifa base en Guaraníes: 15.000 Gs + 5.000 Gs por kg adicional tras el primer kilo
        $peso = max(0.1, floatval($this->peso_kg));
        $base = 15000;
        if ($peso > 1) {
            $extra = ceil($peso - 1) * 5000;
            $this->costo_servicio = $base + $extra;
        } else {
            $this->costo_servicio = $base;
        }
    }

    public function seleccionarEmisor(int $id): void
    {
        $this->cliente_emisor_id = $id;
        $this->emisorSeleccionado = Cliente::find($id);
        $this->searchEmisor = '';
    }

    public function deseleccionarEmisor(): void
    {
        $this->cliente_emisor_id = null;
        $this->emisorSeleccionado = null;
    }

    public function seleccionarReceptor(int $id): void
    {
        $this->cliente_receptor_id = $id;
        $this->receptorSeleccionado = Cliente::find($id);
        $this->searchReceptor = '';
    }

    public function deseleccionarReceptor(): void
    {
        $this->cliente_receptor_id = null;
        $this->receptorSeleccionado = null;
    }

    public function openQuickClientModal(string $target): void
    {
        $this->quickClientTarget = $target;
        $this->reset([
            'quick_nombre',
            'quick_apellido',
            'quick_documento_numero',
            'quick_telefono',
            'quick_direccion',
            'quick_correo',
        ]);
        $this->quick_documento_tipo = 'CI';
        $this->showingQuickClientModal = true;
    }

    public function saveQuickClient(): void
    {
        $this->validate([
            'quick_nombre' => 'required|string|max:100',
            'quick_apellido' => 'required|string|max:100',
            'quick_documento_tipo' => 'required|in:CI,RUC,DNI,PASAPORTE',
            'quick_documento_numero' => 'required|string|max:30|unique:clientes,documento_numero,NULL,id,deleted_at,NULL',
            'quick_telefono' => 'required|string|max:30',
            'quick_direccion' => 'nullable|string|max:255',
            'quick_correo' => 'nullable|email|max:150',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->quick_nombre,
            'apellido' => $this->quick_apellido,
            'documento_tipo' => $this->quick_documento_tipo,
            'documento_numero' => $this->quick_documento_numero,
            'telefono' => $this->quick_telefono,
            'direccion' => $this->quick_direccion ?: null,
            'correo_electronico' => $this->quick_correo ?: null,
        ]);

        AuditLog::log('crear_cliente_rapido', 'Cliente', $cliente->id, "Cliente rápido registrado: {$cliente->nombre_completo}");

        if ($this->quickClientTarget === 'emisor') {
            $this->seleccionarEmisor($cliente->id);
        } else {
            $this->seleccionarReceptor($cliente->id);
        }

        $this->showingQuickClientModal = false;
    }

    public function save()
    {
        $this->validate();

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $this->cliente_emisor_id,
            'cliente_receptor_id' => $this->cliente_receptor_id,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'descripcion_paquete' => $this->descripcion_paquete,
            'peso_kg' => $this->peso_kg,
            'dimensiones_largo' => $this->dimensiones_largo,
            'dimensiones_ancho' => $this->dimensiones_ancho,
            'dimensiones_alto' => $this->dimensiones_alto,
            'costo_servicio' => $this->costo_servicio,
            'estado_actual' => 'recibido',
            'operador_id' => auth()->id(),
        ]);

        AuditLog::log('admision_encomienda', 'Encomienda', $encomienda->id, "Admisión de encomienda registrada: {$encomienda->tracking_id}");

        session()->flash('message', "¡Encomienda admitida exitosamente con Tracking ID: {$encomienda->tracking_id}!");

        return redirect()->route('encomiendas.show', $encomienda->id);
    }

    public function render()
    {
        $emisores = [];
        if (strlen($this->searchEmisor) >= 2) {
            $emisores = Cliente::where('nombre', 'ilike', "%{$this->searchEmisor}%")
                ->orWhere('apellido', 'ilike', "%{$this->searchEmisor}%")
                ->orWhere('documento_numero', 'ilike', "%{$this->searchEmisor}%")
                ->take(6)
                ->get();
        }

        $receptores = [];
        if (strlen($this->searchReceptor) >= 2) {
            $receptores = Cliente::where('nombre', 'ilike', "%{$this->searchReceptor}%")
                ->orWhere('apellido', 'ilike', "%{$this->searchReceptor}%")
                ->orWhere('documento_numero', 'ilike', "%{$this->searchReceptor}%")
                ->take(6)
                ->get();
        }

        return view('livewire.encomiendas.create', [
            'emisores' => $emisores,
            'receptores' => $receptores,
        ])->layout('layouts.app');
    }
}
