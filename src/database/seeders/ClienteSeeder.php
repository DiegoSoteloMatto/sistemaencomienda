<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'documento_tipo' => 'CI',
                'documento_numero' => '1234567',
                'telefono' => '0985123456',
                'direccion' => 'Avda. Caballero 124, Encarnación',
                'correo_electronico' => 'juan.perez@example.com',
            ],
            [
                'nombre' => 'María',
                'apellido' => 'Gómez',
                'documento_tipo' => 'CI',
                'documento_numero' => '2345678',
                'telefono' => '0971987654',
                'direccion' => 'Mariscal Estigarribia 850, Encarnación',
                'correo_electronico' => 'maria.gomez@example.com',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Benítez',
                'documento_tipo' => 'RUC',
                'documento_numero' => '80012345-6',
                'telefono' => '0981112233',
                'direccion' => 'Ruta 1 Km 5, San Juan del Paraná',
                'correo_electronico' => 'carlos.benitez@empresa.com',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'documento_tipo' => 'CI',
                'documento_numero' => '3456789',
                'telefono' => '0992334455',
                'direccion' => 'Palma 450, Asunción',
                'correo_electronico' => 'ana.martinez@example.com',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::firstOrCreate(
                ['documento_numero' => $cliente['documento_numero']],
                $cliente
            );
        }
    }
}
