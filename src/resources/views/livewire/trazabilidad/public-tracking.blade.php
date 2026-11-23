<div class="min-h-screen bg-slate-50 flex flex-col justify-between">

    {{-- ══════════════════ NAVBAR SUPERIOR ══════════════════ --}}
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 group-hover:bg-indigo-700 transition flex items-center justify-center text-white shadow-sm shadow-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black tracking-tight text-slate-900 group-hover:text-indigo-600 transition">ENCOMIENDAS</span>
                    <span class="text-[10px] font-semibold tracking-wider uppercase text-slate-400 -mt-1">Trazabilidad en Vivo</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="/" class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-indigo-600 transition px-3 py-1.5 rounded-lg hover:bg-slate-50">
                    Inicio
                </a>
                <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition px-3.5 py-1.5 rounded-lg flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Ingresar</span>
                </a>
            </div>
        </div>
    </header>

    {{-- ══════════════════ SECCIÓN PRINCIPAL / BUSCADOR ══════════════════ --}}
    <main class="flex-grow py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            {{-- Encabezado & Buscador --}}
            <div class="text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100/80 text-indigo-700 text-xs font-bold mb-3">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    Portal Público de Consulta
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Consulta y Trazabilidad de Envíos
                </h1>
                <p class="mt-2 text-sm sm:text-base text-slate-500">
                    Ingresá el código de seguimiento (Tracking ID) impreso en tu guía de remisión o comprobante.
                </p>

                {{-- Formulario de Búsqueda --}}
                <div class="mt-6 bg-white p-2.5 sm:p-3 rounded-2xl shadow-sm border border-slate-200">
                    <form wire:submit.prevent="buscar" class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model="codigo"
                                placeholder="Ej: ENC-20261122-00001"
                                class="w-full pl-11 pr-4 py-3 text-sm sm:text-base font-mono font-bold tracking-wider uppercase bg-slate-50 hover:bg-slate-100/60 focus:bg-white border-0 rounded-xl focus:ring-2 focus:ring-indigo-600 transition placeholder-slate-400"
                                autocomplete="off"
                                autofocus
                            >
                        </div>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2 text-sm sm:text-base shrink-0 disabled:opacity-50">
                            <span wire:loading.remove wire:target="buscar">Rastrear Envío</span>
                            <span wire:loading wire:target="buscar" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <span>Buscando...</span>
                            </span>
                        </button>
                    </form>
                </div>
                <div class="mt-2 text-xs text-slate-400 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Formato sugerido: <strong class="font-mono text-slate-600 font-semibold">ENC-YYYYMMDD-XXXXX</strong></span>
                </div>
            </div>

            {{-- ══════════════════ RESULTADOS ══════════════════ --}}
            @if($buscado)
                @if($encomienda)
                    @php
                        $etapas = [
                            'recibido'      => ['label' => 'Recibido',        'desc' => 'En sucursal origen'],
                            'clasificacion' => ['label' => 'Clasificación',   'desc' => 'Preparando despacho'],
                            'transito'      => ['label' => 'En Tránsito',     'desc' => 'En viaje a destino'],
                            'arribado'      => ['label' => 'Arribado',        'desc' => 'En sucursal destino'],
                            'reparto'       => ['label' => 'En Reparto',      'desc' => 'En móvil de entrega'],
                            'entregado'     => ['label' => 'Entregado',       'desc' => 'Destinatario conforme'],
                        ];
                        $keys = array_keys($etapas);
                        $currentIdx = array_search($encomienda->estado_actual, $keys);
                        if ($currentIdx === false && $encomienda->estado_actual === 'incidencia') {
                            $currentIdx = 4; // Incidencia tras reparto
                        }

                        $badgeStyles = [
                            'recibido'      => 'bg-blue-50 text-blue-700 border-blue-200',
                            'clasificacion' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'transito'      => 'bg-amber-50 text-amber-700 border-amber-200',
                            'arribado'      => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                            'reparto'       => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'entregado'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'incidencia'    => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];

                        $dotColors = [
                            'recibido'      => 'bg-blue-600',
                            'clasificacion' => 'bg-purple-600',
                            'transito'      => 'bg-amber-600',
                            'arribado'      => 'bg-cyan-600',
                            'reparto'       => 'bg-indigo-600',
                            'entregado'     => 'bg-emerald-600',
                            'incidencia'    => 'bg-rose-600',
                        ];

                        $ultimoMovimiento = $encomienda->historialEstados->first();
                    @endphp

                    <div class="space-y-6">

                        {{-- ── 1. CABECERA RESUMEN DEL ENVÍO (CÓDIGO Y ESTADO BIEN ORDENADOS) ── --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-7">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                                
                                {{-- Bloque Tracking ID --}}
                                <div class="space-y-1.5" x-data="{ copied: false }">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Código de Seguimiento</span>
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText('{{ $encomienda->tracking_id }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span x-text="copied ? '¡Copiado!' : 'Copiar'"></span>
                                        </button>
                                    </div>
                                    {{-- Código sin partirse en renglones --}}
                                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-indigo-700 select-all whitespace-nowrap overflow-x-auto py-0.5">
                                        {{ $encomienda->tracking_id }}
                                    </div>
                                </div>

                                {{-- Bloque Estado Actual con Badge Grande --}}
                                <div class="md:text-right space-y-1.5">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Estado Actual</span>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm sm:text-base font-extrabold {{ $badgeStyles[$encomienda->estado_actual] ?? 'bg-slate-100 text-slate-800 border-slate-200' }}">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $dotColors[$encomienda->estado_actual] ?? 'bg-slate-500' }} {{ $encomienda->estado_actual === 'entregado' ? '' : 'animate-pulse' }}"></span>
                                        <span>{{ $encomienda->estado_label }}</span>
                                    </div>
                                    @if($ultimoMovimiento)
                                        <div class="text-[11px] text-slate-400">
                                            Última actualización: <strong class="text-slate-600">{{ $ultimoMovimiento->timestamp->format('d/m/Y H:i') }} hs</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Ruta del Envío (Origen -> Destino) --}}
                            <div class="pt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 block">Ciudad de Origen</span>
                                    <div class="font-extrabold text-slate-900 text-base sm:text-lg flex items-center gap-2 mt-0.5">
                                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $encomienda->origen }}</span>
                                    </div>
                                </div>

                                <div class="hidden sm:flex flex-col items-center justify-center text-slate-300">
                                    <span class="text-[11px] font-bold text-slate-400 mb-1">Trayecto</span>
                                    <div class="w-full flex items-center gap-1 px-4">
                                        <div class="h-0.5 flex-1 bg-slate-200"></div>
                                        <svg class="w-5 h-5 text-indigo-500 shrink-0 transform rotate-90 sm:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                        <div class="h-0.5 flex-1 bg-slate-200"></div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Ciudad de Destino</span>
                                    <div class="font-extrabold text-slate-900 text-base sm:text-lg flex items-center gap-2 mt-0.5">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $encomienda->destino }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── 2. BARRA DE PROGRESO DE ETAPAS (SIN ENCIMARSE EN PC NI EN CELULAR) ── --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 uppercase tracking-wider">
                                        Progreso del Envío
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Etapas obligatorias del flujo de distribución</p>
                                </div>
                                @if($encomienda->estado_actual === 'incidencia')
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        En Incidencia
                                    </span>
                                @endif
                            </div>

                            {{-- VERSIÓN PC Y TABLET (Pantallas medianas y grandes >= 640px) --}}
                            <div class="hidden sm:block">
                                <div class="relative py-4">
                                    {{-- Línea de progreso de fondo --}}
                                    <div class="absolute top-8 left-[8%] right-[8%] h-1 bg-slate-200 -translate-y-1/2 rounded-full">
                                        <div
                                            class="h-1 bg-indigo-600 rounded-full transition-all duration-700"
                                            style="width: {{ $currentIdx !== false ? max(0, min(100, ($currentIdx / (count($keys)-1)) * 100)) : 0 }}%">
                                        </div>
                                    </div>

                                    {{-- Grilla con las 6 etapas distribuidas de forma equitativa --}}
                                    <div class="grid grid-cols-6 relative z-10 gap-2">
                                        @foreach($etapas as $stKey => $step)
                                            @php
                                                $idx = array_search($stKey, $keys);
                                                $done = $currentIdx !== false && $idx <= $currentIdx;
                                                $active = $currentIdx !== false && $idx === $currentIdx;
                                            @endphp
                                            <div class="flex flex-col items-center text-center px-1">
                                                {{-- Círculo de la etapa --}}
                                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-300
                                                    {{ $done ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-slate-200 text-slate-500' }}
                                                    {{ $active ? 'ring-4 ring-indigo-100 scale-110' : '' }}">
                                                    @if($done && !$active)
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @elseif($active)
                                                        <span class="w-2.5 h-2.5 bg-white rounded-full animate-ping"></span>
                                                    @else
                                                        <span>{{ $idx + 1 }}</span>
                                                    @endif
                                                </div>

                                                {{-- Nombre de la etapa --}}
                                                <div class="mt-3">
                                                    <span class="text-xs font-bold block leading-tight {{ $active ? 'text-indigo-700 font-extrabold' : ($done ? 'text-slate-800' : 'text-slate-400') }}">
                                                        {{ $step['label'] }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 hidden lg:block mt-0.5">
                                                        {{ $step['desc'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- VERSIÓN CELULAR / MÓVIL (Stepper Vertical Limpio - Nunca se enciman) --}}
                            <div class="block sm:hidden space-y-0 pt-2">
                                @foreach($etapas as $stKey => $step)
                                    @php
                                        $idx = array_search($stKey, $keys);
                                        $done = $currentIdx !== false && $idx <= $currentIdx;
                                        $active = $currentIdx !== false && $idx === $currentIdx;
                                        $isLast = $idx === count($etapas) - 1;
                                    @endphp
                                    <div class="flex items-start gap-4 relative {{ !$isLast ? 'pb-6' : '' }}">
                                        {{-- Línea conectora vertical --}}
                                        @if(!$isLast)
                                            <div class="absolute left-4 top-8 bottom-0 w-0.5 {{ $done ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                        @endif

                                        {{-- Círculo --}}
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 z-10
                                            {{ $done ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200 text-slate-400' }}
                                            {{ $active ? 'ring-4 ring-indigo-100 scale-105' : '' }}">
                                            @if($done && !$active)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif($active)
                                                <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                                            @else
                                                <span>{{ $idx + 1 }}</span>
                                            @endif
                                        </div>

                                        {{-- Info de la etapa --}}
                                        <div class="flex-1 pt-0.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-bold {{ $active ? 'text-indigo-700 font-extrabold' : ($done ? 'text-slate-800' : 'text-slate-400') }}">
                                                    {{ $step['label'] }}
                                                </span>
                                                @if($active)
                                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                                        En curso
                                                    </span>
                                                @elseif($done)
                                                    <span class="text-[10px] font-semibold text-emerald-600">
                                                        Completado
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $step['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── 3. DETALLES Y HISTORIAL (DOS COLUMNAS EN PC) ── --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Columna 1: Información General del Envío --}}
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-7 space-y-5">
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Información de la Encomienda
                                </h3>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-400 block uppercase">Remitente</span>
                                        <span class="font-bold text-slate-800 text-sm mt-0.5 block truncate">
                                            {{ $encomienda->emisor->nombre_completo ?? 'No especificado' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-slate-400 block uppercase">Destinatario</span>
                                        <span class="font-bold text-slate-800 text-sm mt-0.5 block truncate">
                                            {{ $encomienda->receptor->nombre_completo ?? 'No especificado' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-slate-400 block uppercase">Fecha de Admisión</span>
                                        <span class="font-semibold text-slate-700 text-sm mt-0.5 block">
                                            {{ $encomienda->created_at->format('d/m/Y H:i') }} hs
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-slate-400 block uppercase">Peso Registrado</span>
                                        <span class="font-semibold text-slate-700 text-sm mt-0.5 block">
                                            {{ number_format($encomienda->peso_kg, 2) }} kg
                                        </span>
                                    </div>
                                </div>

                                @if($encomienda->descripcion_paquete)
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-[11px] font-bold text-slate-500 block uppercase">Contenido Declarado</span>
                                        <p class="text-xs text-slate-700 mt-1 leading-relaxed">
                                            {{ $encomienda->descripcion_paquete }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Columna 2: Historial Cronológico de Movimientos --}}
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-7 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Historial Cronológico de Movimientos
                                    </h3>
                                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                        {{ $encomienda->historialEstados->count() }} evento(s)
                                    </span>
                                </div>

                                <div class="space-y-0 pt-2">
                                    @forelse($encomienda->historialEstados as $index => $historial)
                                        @php
                                            $isFirst = $index === 0;
                                            $isLastHistorial = $index === $encomienda->historialEstados->count() - 1;
                                            $hColor = $dotColors[$historial->estado_nuevo] ?? 'bg-slate-400';
                                        @endphp
                                        <div class="flex gap-4 relative {{ !$isLastHistorial ? 'pb-6' : '' }}">
                                            {{-- Línea conectora --}}
                                            @if(!$isLastHistorial)
                                                <div class="absolute left-3 top-6 bottom-0 w-0.5 bg-slate-100"></div>
                                            @endif

                                            {{-- Punto indicador --}}
                                            <div class="w-6 h-6 rounded-full {{ $hColor }} shrink-0 mt-0.5 flex items-center justify-center z-10 {{ $isFirst ? 'ring-4 ring-indigo-100' : '' }}">
                                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                            </div>

                                            {{-- Contenido del evento --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">
                                                    <span class="text-sm font-bold text-slate-900">
                                                        {{ \App\Models\Encomienda::ESTADOS[$historial->estado_nuevo] ?? ucfirst($historial->estado_nuevo) }}
                                                    </span>
                                                    <span class="text-xs font-mono text-slate-400">
                                                        {{ $historial->timestamp->format('d/m/Y H:i') }} hs
                                                    </span>
                                                </div>

                                                @if($historial->observaciones)
                                                    <p class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-lg p-2.5 mt-1.5 leading-relaxed">
                                                        {{ $historial->observaciones }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 text-center py-4">No hay movimientos registrados todavía.</p>
                                    @endforelse
                                </div>
                            </div>

                        </div>

                    </div>
                @else
                    {{-- ── 4. MENSAJE CUANDO NO SE ENCUENTRA EL ENVÍO ── --}}
                    <div class="bg-white rounded-2xl border border-amber-200/80 shadow-sm p-8 text-center max-w-lg mx-auto">
                        <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No encontramos ningún envío con ese código</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            El código <strong class="font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $codigo }}</strong> no figura en nuestra base de datos.
                        </p>
                        <p class="text-xs text-slate-400 mt-3">
                            Por favor revisá los dígitos e intentá de nuevo, o ponete en contacto con la sucursal de origen.
                        </p>
                    </div>
                @endif
            @endif

        </div>
    </main>

    {{-- ══════════════════ FOOTER ══════════════════ --}}
    <footer class="bg-white border-t border-slate-200 py-6 px-4">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400 text-center sm:text-left">
            <div>
                &copy; {{ date('Y') }} <strong>Sistema de Encomiendas</strong> &bull; Trazabilidad y Logística Integral
            </div>
            <div class="flex items-center gap-4">
                <a href="/" class="hover:text-indigo-600 transition">Inicio</a>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-indigo-600 transition">Panel de Operadores</a>
            </div>
        </div>
    </footer>

</div>
