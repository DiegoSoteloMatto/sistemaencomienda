<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('encomiendas', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->foreignId('cliente_emisor_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('cliente_receptor_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('origen');
            $table->string('destino');
            $table->text('descripcion_paquete')->nullable();
            $table->decimal('peso_kg', 8, 2);
            $table->decimal('dimensiones_largo', 8, 2)->nullable();
            $table->decimal('dimensiones_ancho', 8, 2)->nullable();
            $table->decimal('dimensiones_alto', 8, 2)->nullable();
            $table->decimal('costo_servicio', 12, 2);
            $table->string('estado_actual')->default('recibido');
            $table->foreignId('operador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('repartidor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomiendas');
    }
};
