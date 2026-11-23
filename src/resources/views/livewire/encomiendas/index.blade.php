<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 mb-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestión de Encomiendas</h2>
                <p class="text-sm text-gray-500 mt-1">Admisión, seguimiento y control de envíos registrados.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('encomiendas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Encomienda (Admisión)
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Filtros de búsqueda -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Buscar Envío</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tracking ID, remitente, destinatario, ciudad..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estado</label>
                <select wire:model.live="estadoFilter" class="w-full py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="recibido">Recibido en Origen</option>
                    <option value="clasificacion">En Clasificación</option>
                    <option value="transito">En Tránsito</option>
                    <option value="arribado">Arribado a Destino</option>
                    <option value="reparto">En Reparto</option>
                    <option value="entregado">Entregado</option>
                    <option value="incidencia">Con Incidencia</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Fecha Registro</label>
                <input type="date" wire:model.live="fechaDesde" class="w-full py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <!-- Tabla de Encomiendas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Tracking ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Remitente</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Destinatario</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Ruta</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Peso / Costo</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Estado</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($encomiendas as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('encomiendas.show', $item->id) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ $item->tracking_id }}
                                    </a>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $item->emisor->nombre_completo }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->emisor->telefono }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $item->receptor->nombre_completo }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->receptor->telefono }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-xs text-gray-700">
                                        <span class="font-medium">{{ $item->origen }}</span>
                                        <svg class="w-3.5 h-3.5 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        <span class="font-medium">{{ $item->destino }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-900 font-semibold">{{ number_format($item->peso_kg, 2) }} kg</div>
                                    <div class="text-xs text-indigo-600 font-bold">Gs. {{ number_format($item->costo_servicio, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                        $badgeLabels = [
                                            'recibido' => 'Recibido',
                                            'clasificacion' => 'Clasificación',
                                            'transito' => 'En Tránsito',
                                            'arribado' => 'Arribado',
                                            'reparto' => 'En Reparto',
                                            'entregado' => 'Entregado',
                                            'incidencia' => 'Incidencia',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeStyles[$item->estado_actual] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        {{ $badgeLabels[$item->estado_actual] ?? ucfirst($item->estado_actual) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('encomiendas.show', $item->id) }}" class="text-gray-600 hover:text-gray-900">Ver</a>
                                    <a href="{{ route('encomiendas.etiqueta', $item->id) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-4 h-4 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Etiqueta
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron encomiendas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $encomiendas->links() }}
            </div>
        </div>
    </div>
</div>
