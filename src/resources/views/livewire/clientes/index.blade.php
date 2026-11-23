<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 mb-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestión de Clientes</h2>
                <p class="text-sm text-gray-500 mt-1">Registro y administración de clientes remitentes y destinatarios.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Cliente
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Búsqueda rápida -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Búsqueda rápida en tiempo real</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, apellido, CI/RUC, teléfono o correo..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Cliente</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Documento</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Teléfono</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Dirección</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Email</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $cliente->nombre_completo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ $cliente->documento_tipo }}: {{ $cliente->documento_numero }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $cliente->telefono }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $cliente->direccion ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">{{ $cliente->correo_electronico ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="openEditModal({{ $cliente->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                    <button wire:click="delete({{ $cliente->id }})" wire:confirm="¿Está seguro de eliminar este cliente?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron clientes registrados con ese criterio.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $clientes->links() }}
            </div>
        </div>

        <!-- Modal Crear/Editar Cliente -->
        @if($showingModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">{{ $editingClienteId ? 'Editar Cliente' : 'Nuevo Cliente' }}</h3>
                        <button wire:click="$set('showingModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="save" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                                <input type="text" wire:model="nombre" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @error('nombre') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Apellido *</label>
                                <input type="text" wire:model="apellido" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @error('apellido') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo Doc.</label>
                                <select wire:model="documento_tipo" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="CI">CI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="DNI">DNI</option>
                                    <option value="PASAPORTE">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Número de Documento *</label>
                                <input type="text" wire:model="documento_numero" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @error('documento_numero') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono / Celular *</label>
                            <input type="text" wire:model="telefono" placeholder="Ej: 0985123456" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('telefono') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" wire:model="direccion" placeholder="Calle, número, barrio, ciudad..." class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('direccion') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" wire:model="correo_electronico" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('correo_electronico') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showingModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm">Guardar Cliente</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
