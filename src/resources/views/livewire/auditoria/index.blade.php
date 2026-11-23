<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="pb-6 mb-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Registro de Auditoría (Logs)</h2>
            <p class="text-sm text-gray-500 mt-1">Trazabilidad de operaciones críticas, accesos y modificaciones en el sistema.</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Buscar en Logs</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por usuario, acción, descripción o IP..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filtrar por Acción</label>
                <select wire:model.live="actionFilter" class="w-full py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Todas las acciones</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Fecha y Hora</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Usuario</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Acción</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Entidad</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Descripción</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $log->user?->name ?? 'Sistema' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user?->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $log->entity_type ? "{$log->entity_type} #{$log->entity_id}" : '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No se registran eventos de auditoría todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
