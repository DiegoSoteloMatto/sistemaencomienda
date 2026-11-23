<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Encomiendas - Encarnación, Paraguay</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800,900&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md">
                    E
                </div>
                <div>
                    <span class="text-xl font-black text-gray-900 tracking-tight">ENCOMIENDAS</span>
                    <span class="text-[10px] text-gray-400 block -mt-1 font-semibold uppercase tracking-wider">Encarnación &bull; Logística</span>
                </div>
            </div>

            <nav class="flex items-center space-x-3 sm:space-x-4">
                <a href="{{ route('tracking') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 px-3 py-2 rounded-lg hover:bg-indigo-50 transition">
                    Rastrear Envío
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition">
                            Ir al Panel &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 px-3 py-2">
                            Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main>
        <section class="relative py-16 sm:py-24 bg-gradient-to-b from-indigo-50/70 via-white to-gray-50 overflow-hidden">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wider mb-6">
                    Logística Inteligente y Trazabilidad en Tiempo Real
                </span>
                
                <h1 class="text-4xl sm:text-6xl font-black text-gray-900 tracking-tight leading-tight">
                    Gestión y Seguimiento de Encomiendas en Encarnación
                </h1>
                
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Consulte el estado de su paquete al instante con su código de guía o administre envíos empresariales con control de ruta, código QR y acuse de recibo.
                </p>

                <!-- Cuadro de Rastreo Rápido -->
                <div class="mt-10 max-w-xl mx-auto bg-white p-4 sm:p-5 rounded-3xl shadow-xl border border-gray-100">
                    <form action="{{ route('tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <input type="text" name="codigo" placeholder="Ingrese Tracking ID (ej: ENC-20261122-00001)" required class="w-full px-4 py-3.5 text-sm sm:text-base font-mono font-bold uppercase rounded-2xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner">
                        </div>
                        <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-2xl shadow-md transition flex items-center justify-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Consultar
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Características -->
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Admisión con Código QR</h3>
                    <p class="text-sm text-gray-600">Etiquetas generadas al instante con QR para lectura rápida en centros de acopio y vehículos de transporte.</p>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Trazabilidad en Tiempo Real</h3>
                    <p class="text-sm text-gray-600">Flujo completo de estados: recepción, clasificación, despacho en tránsito, arribo y última milla.</p>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Acuse de Recibo Digital</h3>
                    <p class="text-sm text-gray-600">Registro de entrega validado con nombre y documento del receptor final para máxima seguridad.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-8 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Sistema de Información para la Gestión y Trazabilidad de Encomiendas &bull; Encarnación, Paraguay
    </footer>
</body>
</html>
