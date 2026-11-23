<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="pb-6 mb-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Módulo de Repartos</h2>
                <p class="text-sm text-gray-500 mt-1">Gestión de entregas de última milla y actualización de acuse de recibo.</p>
            </div>
            <!-- Filtros de Estado -->
            <div class="inline-flex rounded-xl bg-gray-100 p-1">
                <button wire:click="$set('filtro', 'pendientes')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filtro === 'pendientes' ? 'bg-white shadow text-indigo-700' : 'text-gray-600 hover:text-gray-900' }}">
                    Pendientes de Entrega
                </button>
                <button wire:click="$set('filtro', 'entregados')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filtro === 'entregados' ? 'bg-white shadow text-emerald-700' : 'text-gray-600 hover:text-gray-900' }}">
                    Entregados
                </button>
                <button wire:click="$set('filtro', 'incidencia')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filtro === 'incidencia' ? 'bg-white shadow text-rose-700' : 'text-gray-600 hover:text-gray-900' }}">
                    Con Incidencia
                </button>
                <button wire:click="$set('filtro', 'todos')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filtro === 'todos' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                    Todos
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Listado de Encomiendas asignadas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($encomiendas as $item)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                            <span class="font-mono font-bold text-indigo-600 text-base">{{ $item->tracking_id }}</span>
                            @php
                                $badgeStyles = [
                                    'recibido' => 'bg-blue-100 text-blue-800',
                                    'clasificacion' => 'bg-purple-100 text-purple-800',
                                    'transito' => 'bg-amber-100 text-amber-800',
                                    'arribado' => 'bg-cyan-100 text-cyan-800',
                                    'reparto' => 'bg-indigo-100 text-indigo-800',
                                    'entregado' => 'bg-emerald-100 text-emerald-800',
                                    'incidencia' => 'bg-rose-100 text-rose-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $badgeStyles[$item->estado_actual] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $item->estado_label }}
                            </span>
                        </div>

                        <!-- Info Destinatario y Dirección -->
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase block">Destinatario</span>
                                <span class="font-bold text-gray-900">{{ $item->receptor->nombre_completo }}</span>
                                <span class="text-gray-500 text-xs block">Tel: {{ $item->receptor->telefono }}</span>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase block">Dirección de Entrega</span>
                                <span class="text-gray-800 text-xs">{{ $item->receptor->direccion ?? 'Sin dirección' }} ({{ $item->destino }})</span>
                            </div>

                            @if($item->descripcion_paquete)
                                <div class="p-2 bg-gray-50 rounded-lg text-xs text-gray-600">
                                    <strong>Paquete:</strong> {{ $item->descripcion_paquete }} ({{ number_format($item->peso_kg, 1) }} kg)
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-4 border-t border-gray-100 mt-4 flex items-center justify-end gap-2">
                        @if($item->estado_actual === 'arribado')
                            <button wire:click="iniciarReparto({{ $item->id }})" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-lg transition">
                                Salir a Reparto
                            </button>
                        @endif

                        @if(in_array($item->estado_actual, ['reparto', 'arribado']))
                            <button wire:click="openIncidenciaModal({{ $item->id }})" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-medium text-xs rounded-lg transition">
                                Incidencia
                            </button>
                            <button wire:click="openEntregaModal({{ $item->id }})" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                                Registrar Entrega
                            </button>
                        @endif

                        <a href="{{ route('encomiendas.show', $item->id) }}" class="px-3 py-1.5 border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium text-xs rounded-lg transition">
                            Detalles
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-2xl p-12 text-center text-gray-400 border border-gray-100">
                    No se encontraron encomiendas en esta categoría.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $encomiendas->links() }}
        </div>

        <!-- Modal Registrar Entrega -->
        @if($showingEntregaModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                    <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-emerald-900">Confirmación de Entrega (Acuse de Recibo)</h3>
                        <button wire:click="$set('showingEntregaModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="registrarEntrega" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nombre de quien recibe *</label>
                            <input type="text" wire:model="recibidoPorNombre" class="w-full text-sm border-gray-300 rounded-lg">
                            @error('recibidoPorNombre') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">C.I. / Documento de quien recibe *</label>
                            <input type="text" wire:model="recibidoPorCi" class="w-full text-sm border-gray-300 rounded-lg">
                            @error('recibidoPorCi') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Observaciones</label>
                            <textarea wire:model="observacionEntrega" rows="2" class="w-full text-sm border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showingEntregaModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Cancelar</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-sm">Confirmar Entrega</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Modal Reportar Incidencia -->
        @if($showingIncidenciaModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                    <div class="px-6 py-4 bg-rose-50 border-b border-rose-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-rose-900">Reportar Incidencia en Reparto</h3>
                        <button wire:click="$set('showingIncidenciaModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="registrarIncidencia" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Motivo Principal *</label>
                            <select wire:model="motivoIncidencia" class="w-full text-sm border-gray-300 rounded-lg">
                                <option value="Destinatario ausente">Destinatario ausente</option>
                                <option value="Dirección incorrecta o no localizada">Dirección incorrecta o no localizada</option>
                                <option value="Rechazado por destinatario">Rechazado por destinatario</option>
                                <option value="Paquete dañado en trayecto">Paquete dañado en trayecto</option>
                                <option value="Zona no accesible / Clima adverso">Zona no accesible / Clima adverso</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Detalles adicionales</label>
                            <textarea wire:model="detalleIncidencia" rows="3" placeholder="Indique número de llamadas realizadas, persona que atendió, etc..." class="w-full text-sm border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showingIncidenciaModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Cancelar</button>
                            <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-bold shadow-sm">Reportar Incidencia</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
