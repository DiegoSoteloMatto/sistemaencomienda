<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos del sistema
        $permisos = [
            'gestionar usuarios',
            'gestionar roles',
            'ver auditoria',
            'gestionar clientes',
            'registrar encomiendas',
            'editar encomiendas',
            'imprimir etiquetas',
            'cambiar estado operador',
            'ver encomiendas asignadas',
            'cambiar estado repartidor',
            'ver reportes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 1. Rol Administrador
        $rolAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $rolAdmin->syncPermissions(Permission::all());

        // 2. Rol Operador
        $rolOperador = Role::firstOrCreate(['name' => 'Operador']);
        $rolOperador->syncPermissions([
            'gestionar clientes',
            'registrar encomiendas',
            'editar encomiendas',
            'imprimir etiquetas',
            'cambiar estado operador',
        ]);

        // 3. Rol Repartidor
        $rolRepartidor = Role::firstOrCreate(['name' => 'Repartidor']);
        $rolRepartidor->syncPermissions([
            'ver encomiendas asignadas',
            'cambiar estado repartidor',
        ]);

        // Crear usuarios iniciales con cada rol
        $admin = User::firstOrCreate(
            ['email' => 'admin@encomiendas.com'],
            [
                'name' => 'Administrador del Sistema',
                'password' => Hash::make('password'),
                'activo' => true,
            ]
        );
        $admin->syncRoles([$rolAdmin]);

        $operador = User::firstOrCreate(
            ['email' => 'operador@encomiendas.com'],
            [
                'name' => 'Operador de Sucursal',
                'password' => Hash::make('password'),
                'activo' => true,
            ]
        );
        $operador->syncRoles([$rolOperador]);

        $repartidor = User::firstOrCreate(
            ['email' => 'repartidor@encomiendas.com'],
            [
                'name' => 'Repartidor Móvil',
                'password' => Hash::make('password'),
                'activo' => true,
            ]
        );
        $repartidor->syncRoles([$rolRepartidor]);
    }
}
