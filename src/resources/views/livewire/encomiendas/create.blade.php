<div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="pb-6 mb-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <a href="{{ route('encomiendas.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center mb-1">
                    &larr; Volver a encomiendas
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Admisión de Encomienda (Nuevo Envío)</h2>
                <p class="text-sm text-gray-500 mt-1">Registre los datos del remitente, destinatario y las características del paquete.</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Sección 1: Clientes (Emisor y Receptor) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente Emisor (Remitente) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase bg-blue-50 text-blue-700">
                                Remitente (Emisor)
                            </span>
                            <button type="button" wire:click="openQuickClientModal('emisor')" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                + Nuevo Cliente
                            </button>
                        </div>

                        @if($emisorSeleccionado)
                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 relative">
                                <button type="button" wire:click="deseleccionarEmisor" class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <div class="font-bold text-gray-900">{{ $emisorSeleccionado->nombre_completo }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $emisorSeleccionado->documento_completo }}</div>
                                <div class="text-xs text-gray-600">Tel: {{ $emisorSeleccionado->telefono }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $emisorSeleccionado->direccion ?? 'Sin dirección especificada' }}</div>
                            </div>
                        @else
                            <div class="relative">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Buscar remitente *</label>
                                <input type="text" wire:model.live.debounce.250ms="searchEmisor" placeholder="Escriba nombre o CI/RUC..." class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                
                                @if(!empty($emisores))
                                    <div class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden divide-y divide-gray-100">
                                        @foreach($emisores as $c)
                                            <div wire:click="seleccionarEmisor({{ $c->id }})" class="p-3 hover:bg-indigo-50 cursor-pointer transition text-xs">
                                                <div class="font-semibold text-gray-900">{{ $c->nombre_completo }}</div>
                                                <div class="text-gray-500">{{ $c->documento_completo }} - Tel: {{ $c->telefono }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @error('cliente_emisor_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>

                <!-- Cliente Receptor (Destinatario) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase bg-purple-50 text-purple-700">
                                Destinatario (Receptor)
                            </span>
                            <button type="button" wire:click="openQuickClientModal('receptor')" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                + Nuevo Cliente
                            </button>
                        </div>

                        @if($receptorSeleccionado)
                            <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100 relative">
                                <button type="button" wire:click="deseleccionarReceptor" class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <div class="font-bold text-gray-900">{{ $receptorSeleccionado->nombre_completo }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $receptorSeleccionado->documento_completo }}</div>
                                <div class="text-xs text-gray-600">Tel: {{ $receptorSeleccionado->telefono }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $receptorSeleccionado->direccion ?? 'Sin dirección especificada' }}</div>
                            </div>
                        @else
                            <div class="relative">
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Buscar destinatario *</label>
                                <input type="text" wire:model.live.debounce.250ms="searchReceptor" placeholder="Escriba nombre o CI/RUC..." class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">

                                @if(!empty($receptores))
                                    <div class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden divide-y divide-gray-100">
                                        @foreach($receptores as $r)
                                            <div wire:click="seleccionarReceptor({{ $r->id }})" class="p-3 hover:bg-purple-50 cursor-pointer transition text-xs">
                                                <div class="font-semibold text-gray-900">{{ $r->nombre_completo }}</div>
                                                <div class="text-gray-500">{{ $r->documento_completo }} - Tel: {{ $r->telefono }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @error('cliente_receptor_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sección 2: Ruta y Datos Logísticos -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Ruta del Envío</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Ciudad de Origen *</label>
                        <select wire:model="origen" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Encarnación">Encarnación</option>
                            <option value="Asunción">Asunción</option>
                            <option value="Ciudad del Este">Ciudad del Este</option>
                            <option value="Coronel Oviedo">Coronel Oviedo</option>
                            <option value="Villarrica">Villarrica</option>
                            <option value="Concepción">Concepción</option>
                            <option value="Pedro Juan Caballero">Pedro Juan Caballero</option>
                            <option value="San Ignacio">San Ignacio</option>
                        </select>
                        @error('origen') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Ciudad de Destino *</label>
                        <select wire:model="destino" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Asunción">Asunción</option>
                            <option value="Encarnación">Encarnación</option>
                            <option value="Ciudad del Este">Ciudad del Este</option>
                            <option value="Coronel Oviedo">Coronel Oviedo</option>
                            <option value="Villarrica">Villarrica</option>
                            <option value="Concepción">Concepción</option>
                            <option value="Pedro Juan Caballero">Pedro Juan Caballero</option>
                            <option value="San Ignacio">San Ignacio</option>
                        </select>
                        @error('destino') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Sección 3: Datos del Paquete y Costo -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Detalles del Paquete y Costo</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Peso (kg) *</label>
                        <input type="number" step="0.1" wire:model.live.debounce.300ms="peso_kg" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('peso_kg') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Largo (cm)</label>
                        <input type="number" step="1" wire:model="dimensiones_largo" placeholder="Opcional" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Ancho (cm)</label>
                        <input type="number" step="1" wire:model="dimensiones_ancho" placeholder="Opcional" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Alto (cm)</label>
                        <input type="number" step="1" wire:model="dimensiones_alto" placeholder="Opcional" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Descripción del Contenido</label>
                        <textarea wire:model="descripcion_paquete" rows="2" placeholder="Documentos, repuestos, indumentaria, electrónicos..." class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 flex flex-col justify-between">
                        <label class="block text-xs font-bold text-emerald-800 uppercase">Costo del Servicio (Gs.)</label>
                        <div class="relative mt-1">
                            <input type="number" step="500" wire:model="costo_servicio" class="w-full text-lg font-bold text-emerald-700 bg-white border-emerald-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <span class="text-xs text-emerald-600 mt-1">Calculado automáticamente según peso. Modificable.</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('encomiendas.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-md transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Registrar Admisión y Generar Tracking
                </button>
            </div>
        </form>

        <!-- Modal Crear Cliente Rápido -->
        @if($showingQuickClientModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">
                            Alta Rápida de Cliente ({{ $quickClientTarget === 'emisor' ? 'Remitente' : 'Destinatario' }})
                        </h3>
                        <button wire:click="$set('showingQuickClientModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="saveQuickClient" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                                <input type="text" wire:model="quick_nombre" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                @error('quick_nombre') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Apellido *</label>
                                <input type="text" wire:model="quick_apellido" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                @error('quick_apellido') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select wire:model="quick_documento_tipo" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                    <option value="CI">CI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="DNI">DNI</option>
                                    <option value="PASAPORTE">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">N° Documento *</label>
                                <input type="text" wire:model="quick_documento_numero" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                @error('quick_documento_numero') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono *</label>
                            <input type="text" wire:model="quick_telefono" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            @error('quick_telefono') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" wire:model="quick_direccion" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showingQuickClientModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">Guardar y Seleccionar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
