<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="pb-6 mb-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Informes y Reportes Operativos</h2>
                <p class="text-sm text-gray-500 mt-1">Análisis de volumen de envíos, recaudación y tiempos promedio de tránsito logístico.</p>
            </div>
            <!-- Acciones Exportar -->
            <div class="flex items-center gap-2">
                @php
                    $exportParams = http_build_query([
                        'fecha_desde' => $fechaDesde,
                        'fecha_hasta' => $fechaHasta,
                        'origen' => $origen,
                        'destino' => $destino,
                        'estado' => $estado,
                    ]);
                @endphp
                <a href="{{ route('reportes.csv') }}?{{ $exportParams }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Exportar CSV
                </a>
                <a href="{{ route('reportes.pdf') }}?{{ $exportParams }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Descargar PDF
                </a>
            </div>
        </div>

        <!-- Pestañas -->
        <div class="flex border-b border-gray-200 mb-6">
            <button wire:click="$set('activeTab', 'volumen')" class="pb-3 px-4 text-sm font-bold border-b-2 transition {{ $activeTab === 'volumen' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Volumen y Recaudación (RF-06.1)
            </button>
            <button wire:click="$set('activeTab', 'tiempos')" class="pb-3 px-4 text-sm font-bold border-b-2 transition {{ $activeTab === 'tiempos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Tiempos de Tránsito y Cuellos de Botella (RF-06.2)
            </button>
        </div>

        @if($activeTab === 'volumen')
            <!-- Filtros de Reporte -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Desde</label>
                    <input type="date" wire:model.live="fechaDesde" class="w-full text-sm border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hasta</label>
                    <input type="date" wire:model.live="fechaHasta" class="w-full text-sm border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Origen</label>
                    <select wire:model.live="origen" class="w-full text-sm border-gray-300 rounded-lg">
                        <option value="">Todos los orígenes</option>
                        <option value="Encarnación">Encarnación</option>
                        <option value="Asunción">Asunción</option>
                        <option value="Ciudad del Este">Ciudad del Este</option>
                        <option value="Coronel Oviedo">Coronel Oviedo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Destino</label>
                    <select wire:model.live="destino" class="w-full text-sm border-gray-300 rounded-lg">
                        <option value="">Todos los destinos</option>
                        <option value="Asunción">Asunción</option>
                        <option value="Encarnación">Encarnación</option>
                        <option value="Ciudad del Este">Ciudad del Este</option>
                        <option value="Coronel Oviedo">Coronel Oviedo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estado</label>
                    <select wire:model.live="estado" class="w-full text-sm border-gray-300 rounded-lg">
                        <option value="">Todos los estados</option>
                        <option value="recibido">Recibido</option>
                        <option value="clasificacion">Clasificación</option>
                        <option value="transito">En Tránsito</option>
                        <option value="arribado">Arribado</option>
                        <option value="reparto">En Reparto</option>
                        <option value="entregado">Entregado</option>
                        <option value="incidencia">Incidencia</option>
                    </select>
                </div>
            </div>

            <!-- KPIs Consolidados -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase">Volumen de Envíos</span>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalEnvios) }}</div>
                    <span class="text-xs text-gray-500">paquetes registrados</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase">Peso Total Despachado</span>
                    <div class="text-2xl font-black text-indigo-600 mt-1">{{ number_format($totalPeso, 2, ',', '.') }} kg</div>
                    <span class="text-xs text-gray-500">carga transportada</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase">Recaudación Total</span>
                    <div class="text-2xl font-black text-emerald-600 mt-1">Gs. {{ number_format($recaudacionTotal, 0, ',', '.') }}</div>
                    <span class="text-xs text-emerald-700/80">ingresos brutos</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase">Tarifa Promedio</span>
                    <div class="text-2xl font-black text-gray-800 mt-1">Gs. {{ number_format($promedioCosto, 0, ',', '.') }}</div>
                    <span class="text-xs text-gray-500">por encomienda</span>
                </div>
            </div>

            <!-- Gráficos de Distribución -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Distribución por Estados -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Distribución por Estados
                    </h3>
                    <div class="space-y-3">
                        @forelse($distribucionEstados as $st => $count)
                            @php
                                $pct = $totalEnvios > 0 ? round(($count / $totalEnvios) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-gray-700">{{ \App\Models\Encomienda::ESTADOS[$st] ?? ucfirst($st) }}</span>
                                    <span class="text-gray-900">{{ $count }} ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Sin datos para los filtros seleccionados.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Rutas Más Frecuentes -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Top Rutas Más Frecuentes
                    </h3>
                    <div class="space-y-3">
                        @forelse($topRutas as $ruta)
                            @php
                                $pct = $totalEnvios > 0 ? round(($ruta->total / $totalEnvios) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-gray-700">{{ $ruta->origen }} &rarr; {{ $ruta->destino }}</span>
                                    <span class="text-gray-900 font-bold">{{ $ruta->total }} envíos</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Sin datos de rutas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tabla de Encomiendas Filtradas -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Listado Consolidado del Periodo</h3>
                    <span class="text-xs text-gray-500">Mostrando {{ $encomiendas->count() }} de {{ $encomiendas->total() }} registros</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Tracking ID</th>
                                <th class="px-6 py-3 text-left">Fecha</th>
                                <th class="px-6 py-3 text-left">Remitente</th>
                                <th class="px-6 py-3 text-left">Destinatario</th>
                                <th class="px-6 py-3 text-left">Ruta</th>
                                <th class="px-6 py-3 text-left">Peso</th>
                                <th class="px-6 py-3 text-left">Costo</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($encomiendas as $item)
                                <tr class="hover:bg-gray-50 text-xs">
                                    <td class="px-6 py-3 font-mono font-bold text-indigo-600">
                                        <a href="{{ route('encomiendas.show', $item->id) }}" class="hover:underline">
                                            {{ $item->tracking_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $item->emisor->nombre_completo }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $item->receptor->nombre_completo }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $item->origen }} &rarr; {{ $item->destino }}</td>
                                    <td class="px-6 py-3 font-semibold">{{ number_format($item->peso_kg, 2) }} kg</td>
                                    <td class="px-6 py-3 font-bold text-emerald-600">Gs. {{ number_format($item->costo_servicio, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-gray-100 text-gray-800">
                                            {{ $item->estado_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-400">No hay envíos registrados en el filtro.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $encomiendas->links() }}
                </div>
            </div>

        @elseif($activeTab === 'tiempos')
            <!-- Análisis de Tiempos y Cuellos de Botella (RF-06.2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Tiempo Promedio Total</span>
                        <div class="text-3xl font-black text-indigo-600 mt-2">{{ $tiempos['promedio_total_horas'] }} hs</div>
                        <p class="text-xs text-gray-500 mt-2">Desde recepción hasta entrega efectiva (basado en {{ $tiempos['total_analizadas'] }} entregas).</p>
                    </div>
                </div>

                <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Tiempos Promedio por Tramo Logístico
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-center">
                        <div class="p-3 bg-blue-50/60 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-blue-700 block">Recepción &rarr; Clasif.</span>
                            <span class="text-lg font-black text-gray-900 block mt-1">{{ $tiempos['recibido_a_clasificacion'] }} hs</span>
                        </div>
                        <div class="p-3 bg-purple-50/60 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-purple-700 block">Clasif. &rarr; Tránsito</span>
                            <span class="text-lg font-black text-gray-900 block mt-1">{{ $tiempos['clasificacion_a_transito'] }} hs</span>
                        </div>
                        <div class="p-3 bg-amber-50/60 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-amber-700 block">Tránsito &rarr; Arribo</span>
                            <span class="text-lg font-black text-gray-900 block mt-1">{{ $tiempos['transito_a_arribado'] }} hs</span>
                        </div>
                        <div class="p-3 bg-cyan-50/60 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-cyan-700 block">Arribo &rarr; Reparto</span>
                            <span class="text-lg font-black text-gray-900 block mt-1">{{ $tiempos['arribado_a_reparto'] }} hs</span>
                        </div>
                        <div class="p-3 bg-emerald-50/60 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-emerald-700 block">Reparto &rarr; Entrega</span>
                            <span class="text-lg font-black text-gray-900 block mt-1">{{ $tiempos['reparto_a_entrega'] }} hs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detección de Cuellos de Botella y Paquetes Estancados -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-rose-50/40">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <h3 class="text-sm font-bold text-gray-900">Alertas de Cuellos de Botella (&gt; 48 Horas Sin Movimiento)</h3>
                    </div>
                    <span class="text-xs font-semibold text-rose-700">{{ $paquetesEstancados->count() }} paquetes detectados</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Tracking ID</th>
                                <th class="px-6 py-3 text-left">Estado Estancado</th>
                                <th class="px-6 py-3 text-left">Última Actualización</th>
                                <th class="px-6 py-3 text-left">Tiempo Inactivo</th>
                                <th class="px-6 py-3 text-left">Ruta</th>
                                <th class="px-6 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($paquetesEstancados as $est)
                                <tr class="hover:bg-gray-50 text-xs">
                                    <td class="px-6 py-3 font-mono font-bold text-indigo-600">
                                        <a href="{{ route('encomiendas.show', $est->id) }}" class="hover:underline">
                                            {{ $est->tracking_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">
                                            {{ $est->estado_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $est->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3 font-bold text-rose-600">
                                        {{ round($est->updated_at->diffInHours(now())) }} horas
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $est->origen }} &rarr; {{ $est->destino }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('encomiendas.show', $est->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                            Gestionar &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">
                                        &check; Excelente: No se detectan paquetes estancados o con cuellos de botella en este momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
