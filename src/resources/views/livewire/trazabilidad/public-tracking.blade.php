<div class="min-h-screen bg-gray-50 flex flex-col justify-between py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full mx-auto space-y-8">
        <!-- Logo y Encabezado -->
        <div class="text-center">
            <a href="/" class="inline-flex items-center space-x-2">
                <span class="text-3xl font-black tracking-tight text-indigo-600">ENCOMIENDAS</span>
            </a>
            <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-gray-900">
                Consulta y Trazabilidad de Envíos
            </h1>
            <p class="mt-2 text-sm text-gray-600 max-w-md mx-auto">
                Ingrese el código de seguimiento (Tracking ID) impreso en su comprobante o guía de transporte.
            </p>
        </div>

        <!-- Buscador -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
            <form wire:submit.prevent="buscar" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model="codigo" placeholder="Ej: ENC-20261122-00001" class="w-full pl-11 pr-4 py-3.5 font-mono text-base font-bold uppercase tracking-wider border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner">
                </div>
                <button type="submit" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center text-sm">
                    Rastrear Envío
                </button>
            </form>
        </div>

        <!-- Resultados -->
        @if($buscado)
            @if($encomienda)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100 animate-fadeIn">
                    <!-- Resumen del Estado -->
                    <div class="p-6 sm:p-8 bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-indigo-100/60">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Código de Envío</span>
                                <div class="text-2xl font-black font-mono text-indigo-700">{{ $encomienda->tracking_id }}</div>
                            </div>
                            <div>
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
                                <span class="inline-flex px-4 py-1.5 rounded-full text-sm font-black uppercase tracking-wide {{ $badgeStyles[$encomienda->estado_actual] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $encomienda->estado_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Origen y Destino -->
                        <div class="grid grid-cols-2 gap-4 pt-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 block uppercase font-semibold">Origen</span>
                                <span class="font-bold text-gray-900 text-base">{{ $encomienda->origen }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500 block uppercase font-semibold">Destino</span>
                                <span class="font-bold text-gray-900 text-base">{{ $encomienda->destino }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Progreso de Etapas -->
                    @php
                        $etapas = [
                            'recibido' => 'Recibido',
                            'clasificacion' => 'Clasificación',
                            'transito' => 'En Tránsito',
                            'arribado' => 'Arribado',
                            'reparto' => 'En Reparto',
                            'entregado' => 'Entregado',
                        ];
                        $keys = array_keys($etapas);
                        $currentIdx = array_search($encomienda->estado_actual, $keys);
                        if ($currentIdx === false && $encomienda->estado_actual === 'incidencia') {
                            $currentIdx = 4; // Entre reparto y destino
                        }
                    @endphp
                    <div class="p-6 overflow-x-auto">
                        <div class="min-w-[500px]">
                            <div class="grid grid-cols-6 relative">
                                <div class="absolute top-3 left-6 right-6 h-1 bg-gray-200 -z-0">
                                    <div class="h-1 bg-indigo-600 transition-all duration-500" style="width: {{ max(0, min(100, ($currentIdx / (count($keys) - 1)) * 100)) }}%"></div>
                                </div>
                                @foreach($etapas as $stKey => $stName)
                                    @php
                                        $idx = array_search($stKey, $keys);
                                        $completed = $currentIdx !== false && $idx <= $currentIdx;
                                    @endphp
                                    <div class="flex flex-col items-center text-center relative z-10">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition {{ $completed ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                            @if($completed)
                                                &check;
                                            @else
                                                {{ $idx + 1 }}
                                            @endif
                                        </div>
                                        <span class="text-[11px] font-semibold mt-2 {{ $completed ? 'text-indigo-900 font-bold' : 'text-gray-400' }}">
                                            {{ $stName }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Línea de tiempo / Historial detallado -->
                    <div class="p-6 sm:p-8">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-6">
                            Historial Cronológico de Movimientos
                        </h3>
                        <div class="relative pl-6 border-l-2 border-indigo-200 space-y-6">
                            @foreach($encomienda->historialEstados as $index => $historial)
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white {{ $index === 0 ? 'bg-indigo-600 ring-4 ring-indigo-100' : 'bg-gray-400' }}"></span>
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ \App\Models\Encomienda::ESTADOS[$historial->estado_nuevo] ?? ucfirst($historial->estado_nuevo) }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $historial->timestamp->format('d/m/Y \a \l\a\s H:i:s') }}
                                    </div>
                                    @if($historial->observaciones)
                                        <div class="text-xs text-gray-600 mt-1 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                            {{ $historial->observaciones }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">No se encontró ningún paquete</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                        Verifique que el código <strong class="font-mono text-gray-700">{{ $codigo }}</strong> esté escrito correctamente, respetando el formato <span class="font-mono text-xs">ENC-YYYYMMDD-XXXXX</span>.
                    </p>
                </div>
            @endif
        @endif
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-gray-400 pt-8">
        &copy; {{ date('Y') }} Sistema de Información y Trazabilidad de Encomiendas &bull; Encarnación, Paraguay
    </div>
</div>
