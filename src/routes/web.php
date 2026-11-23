<?php

use App\Http\Controllers\EtiquetaController;
use App\Livewire\Auditoria\Index as AuditoriaIndex;
use App\Livewire\Clientes\Index as ClientesIndex;
use App\Livewire\Encomiendas\Create as EncomiendaCreate;
use App\Livewire\Encomiendas\Index as EncomiendasIndex;
use App\Livewire\Encomiendas\Show as EncomiendaShow;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Clientes (Fase 3)
    Route::get('clientes', ClientesIndex::class)->name('clientes.index');

    // Encomiendas (Fase 4)
    Route::get('encomiendas', EncomiendasIndex::class)->name('encomiendas.index');
    Route::get('encomiendas/crear', EncomiendaCreate::class)->name('encomiendas.create');
    Route::get('encomiendas/{id}', EncomiendaShow::class)->name('encomiendas.show');
    Route::get('encomiendas/{id}/etiqueta', [EtiquetaController::class, 'show'])->name('encomiendas.etiqueta');

    // Administración y Auditoría (Fase 2)
    Route::get('usuarios', UsuariosIndex::class)->name('usuarios.index');
    Route::get('auditoria', AuditoriaIndex::class)->name('auditoria.index');
});

require __DIR__.'/auth.php';
