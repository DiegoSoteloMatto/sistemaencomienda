<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Panel de Control Logístico
            </h2>
            <a href="{{ route('encomiendas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Admisión
            </a>
        </div>
    </x-slot>

    @php
        $totalEncomiendas = \App\Models\Encomienda::count();
        $enTransito = \App\Models\Encomienda::whereIn('estado_actual', ['recibido', 'clasificacion', 'transito', 'arribado', 'reparto'])->count();
        $entregadas = \App\Models\Encomienda::where('estado_actual', 'entregado')->count();
        $totalClientes = \App\Models\Cliente::count();
        $recientes = \App\Models\Encomienda::with(['emisor', 'receptor'])->latest()->take(5)->get();
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Métricas KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-500">Total Envíos</span>
                        <div class="text-3xl font-black text-gray-900 mt-1">{{ $totalEncomiendas }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-amber-600">En Flujo / Tránsito</span>
                        <div class="text-3xl font-black text-amber-600 mt-1">{{ $enTransito }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-emerald-600">Entregados</span>
                        <div class="text-3xl font-black text-emerald-600 mt-1">{{ $entregadas }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-blue-600">Clientes Activos</span>
                        <div class="text-3xl font-black text-blue-600 mt-1">{{ $totalClientes }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Accesos Directos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('encomiendas.create') }}" class="p-5 bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl text-white shadow-md hover:shadow-lg transition flex items-center justify-between group">
                    <div>
                        <div class="text-lg font-bold">Registrar Encomienda</div>
                        <p class="text-xs text-indigo-100 mt-1">Generar tracking id e imprimir etiquetas.</p>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:translate-x-1 transition">&rarr;</span>
                </a>

                <a href="{{ route('encomiendas.index') }}" class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between group">
                    <div>
                        <div class="text-lg font-bold text-gray-900">Control de Envíos</div>
                        <p class="text-xs text-gray-500 mt-1">Buscar y filtrar todos los paquetes.</p>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center group-hover:translate-x-1 transition">&rarr;</span>
                </a>

                <a href="{{ route('clientes.index') }}" class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between group">
                    <div>
                        <div class="text-lg font-bold text-gray-900">Módulo Clientes</div>
                        <p class="text-xs text-gray-500 mt-1">Administrar remitentes y destinatarios.</p>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center group-hover:translate-x-1 transition">&rarr;</span>
                </a>
            </div>

            <!-- Envíos Recientes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Últimas Encomiendas Registradas</h3>
                    <a href="{{ route('encomiendas.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Ver todas &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Tracking ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Remitente</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Destinatario</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Ruta</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($recientes as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('encomiendas.show', $item->id) }}" class="font-mono font-bold text-indigo-600 hover:text-indigo-800">
                                            {{ $item->tracking_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $item->emisor->nombre_completo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $item->receptor->nombre_completo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">{{ $item->origen }} &rarr; {{ $item->destino }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                            {{ ucfirst($item->estado_actual) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                        No hay encomiendas registradas todavía. ¡Comience creando la primera!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
