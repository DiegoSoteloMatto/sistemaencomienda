<?php

use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\ReporteExportController;
use App\Livewire\Auditoria\Index as AuditoriaIndex;
use App\Livewire\Clientes\Index as ClientesIndex;
use App\Livewire\Encomiendas\Create as EncomiendaCreate;
use App\Livewire\Encomiendas\Index as EncomiendasIndex;
use App\Livewire\Encomiendas\Show as EncomiendaShow;
use App\Livewire\Repartidor\Index as RepartidorIndex;
use App\Livewire\Reportes\Index as ReportesIndex;
use App\Livewire\Trazabilidad\PublicTracking;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use Illuminate\Support\Facades\Route;

// Rutas Públicas (Fase 6)
Route::view('/', 'welcome');
Route::get('tracking/{codigo?}', PublicTracking::class)->name('tracking');

// Rutas Protegidas (Fases 2, 3, 4, 5)
Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Clientes (Fase 3)
    Route::get('clientes', ClientesIndex::class)->name('clientes.index');

    // Encomiendas (Fase 4 y 5)
    Route::get('encomiendas', EncomiendasIndex::class)->name('encomiendas.index');
    Route::get('encomiendas/crear', EncomiendaCreate::class)->name('encomiendas.create');
    Route::get('encomiendas/{id}', EncomiendaShow::class)->name('encomiendas.show');
    Route::get('encomiendas/{id}/etiqueta', [EtiquetaController::class, 'show'])->name('encomiendas.etiqueta');

    // Módulo Repartidor (Fase 5)
    Route::get('repartos', RepartidorIndex::class)->name('repartos.index');

    // Administración y Auditoría (Fase 2)
    Route::get('usuarios', UsuariosIndex::class)->name('usuarios.index');
    Route::get('auditoria', AuditoriaIndex::class)->name('auditoria.index');

    // Reportes Operativos (Fase 7)
    Route::get('reportes', ReportesIndex::class)->name('reportes.index');
    Route::get('reportes/exportar-csv', [ReporteExportController::class, 'exportarCsv'])->name('reportes.csv');
    Route::get('reportes/exportar-pdf', [ReporteExportController::class, 'exportarPdf'])->name('reportes.pdf');
});

require __DIR__.'/auth.php';
