<div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="pb-6 mb-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('encomiendas.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center mb-1">
                    &larr; Volver a encomiendas
                </a>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-black font-mono text-gray-900">{{ $encomienda->tracking_id }}</h2>
                    @php
                        $badgeStyles = [
                            'recibido' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'clasificacion' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'transito' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'arribado' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                            'reparto' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'entregado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'incidencia' => 'bg-rose-100 text-rose-800 border-rose-200',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $badgeStyles[$encomienda->estado_actual] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $encomienda->estado_label }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Registrado el {{ $encomienda->created_at->format('d/m/Y \a \l\a\s H:i') }} por {{ $encomienda->operador?->name ?? 'Operador' }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if(count($estadosPermitidos) > 0)
                    <button wire:click="openStateModal" class="inline-flex items-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Cambiar Estado
                    </button>
                @endif

                <a href="{{ route('encomiendas.etiqueta', $encomienda->id) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir Etiqueta
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Columna Izquierda: Información de Emisor, Receptor y Paquete -->
            <div class="md:col-span-2 space-y-6">
                <!-- Remitente y Destinatario -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 block mb-2">Remitente (Origen: {{ $encomienda->origen }})</span>
                        <div class="text-base font-bold text-gray-900">{{ $encomienda->emisor->nombre_completo }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ $encomienda->emisor->documento_completo }}</div>
                        <div class="text-xs text-gray-600">Tel: {{ $encomienda->emisor->telefono }}</div>
                        <div class="text-xs text-gray-500 mt-2">{{ $encomienda->emisor->direccion ?? 'Sin dirección' }}</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-600 block mb-2">Destinatario (Destino: {{ $encomienda->destino }})</span>
                        <div class="text-base font-bold text-gray-900">{{ $encomienda->receptor->nombre_completo }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ $encomienda->receptor->documento_completo }}</div>
                        <div class="text-xs text-gray-600">Tel: {{ $encomienda->receptor->telefono }}</div>
                        <div class="text-xs text-gray-500 mt-2">{{ $encomienda->receptor->direccion ?? 'Sin dirección' }}</div>
                    </div>
                </div>

                <!-- Detalles Físicos y Tarifas -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Características del Paquete</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        <div>
                            <span class="text-xs text-gray-500 block">Peso</span>
                            <span class="text-base font-bold text-gray-900">{{ number_format($encomienda->peso_kg, 2) }} kg</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Dimensiones</span>
                            <span class="text-sm font-semibold text-gray-800">
                                {{ $encomienda->dimensiones_largo ? "{$encomienda->dimensiones_largo}x{$encomienda->dimensiones_ancho}x{$encomienda->dimensiones_alto} cm" : 'No especificado' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Costo de Envío</span>
                            <span class="text-base font-black text-emerald-600">Gs. {{ number_format($encomienda->costo_servicio, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Repartidor Asignado</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $encomienda->repartidor?->name ?? 'Sin asignar' }}</span>
                        </div>
                    </div>

                    @if($encomienda->descripcion_paquete)
                        <div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-700">
                            <span class="font-bold text-gray-900 block mb-1">Descripción:</span>
                            {{ $encomienda->descripcion_paquete }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Trazabilidad / Historial de Estados -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                    Historial de Trazabilidad
                </h3>

                <div class="relative pl-6 border-l-2 border-indigo-200 space-y-6">
                    @forelse($encomienda->historialEstados as $index => $historial)
                        <div class="relative">
                            <!-- Bullet -->
                            <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white {{ $index === 0 ? 'bg-indigo-600 ring-4 ring-indigo-100' : 'bg-gray-400' }}"></span>
                            
                            <div class="text-xs font-bold uppercase text-indigo-700">
                                {{ \App\Models\Encomienda::ESTADOS[$historial->estado_nuevo] ?? ucfirst($historial->estado_nuevo) }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $historial->timestamp->format('d/m/Y H:i:s') }}</div>
                            <div class="text-xs text-gray-700 mt-1">{{ $historial->observaciones }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Por: {{ $historial->user?->name ?? 'Sistema' }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">Sin historial registrado.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modal Cambiar Estado -->
        @if($showingStateModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Actualizar Estado Logístico</h3>
                        <button wire:click="$set('showingStateModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="actualizarEstado" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estado Actual</label>
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-lg text-sm font-bold">
                                {{ $encomienda->estado_label }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nuevo Estado *</label>
                            <select wire:model="nuevoEstado" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($estadosPermitidos as $ep)
                                    <option value="{{ $ep }}">{{ \App\Models\Encomienda::ESTADOS[$ep] ?? ucfirst($ep) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Asignar Repartidor (Opcional)</label>
                            <select wire:model="selectedRepartidorId" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sin asignar / Mantener actual</option>
                                @foreach($repartidores as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Observaciones / Motivo</label>
                            <textarea wire:model="observaciones" rows="3" placeholder="Detalles de la ubicación, transporte o entrega..." class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showingStateModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm">Confirmar Cambio</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
