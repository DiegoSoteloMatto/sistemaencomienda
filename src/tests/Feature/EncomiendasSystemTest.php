<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Cliente;
use App\Models\Encomienda;
use App\Models\HistorialEstado;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EncomiendasSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_fase2_roles_y_usuarios_asignados_correctamente(): void
    {
        $admin = User::where('email', 'admin@encomiendas.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('Administrador'));

        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $this->assertNotNull($operador);
        $this->assertTrue($operador->hasRole('Operador'));

        $repartidor = User::where('email', 'repartidor@encomiendas.com')->first();
        $this->assertNotNull($repartidor);
        $this->assertTrue($repartidor->hasRole('Repartidor'));
    }

    public function test_fase2_admin_puede_acceder_a_usuarios_y_auditoria(): void
    {
        $admin = User::where('email', 'admin@encomiendas.com')->first();

        $responseUsuarios = $this->actingAs($admin)->get(route('usuarios.index'));
        $responseUsuarios->assertStatus(200);

        $responseAuditoria = $this->actingAs($admin)->get(route('auditoria.index'));
        $responseAuditoria->assertStatus(200);
    }

    public function test_fase2_registro_de_auditoria_helper(): void
    {
        $admin = User::where('email', 'admin@encomiendas.com')->first();
        $this->actingAs($admin);

        $log = AuditLog::log('prueba_auditoria', 'Sistema', 1, 'Registro de auditoría de prueba');

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'action' => 'prueba_auditoria',
            'user_id' => $admin->id,
        ]);
    }

    public function test_fase3_creacion_y_busqueda_de_cliente(): void
    {
        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $this->actingAs($operador);

        $cliente = Cliente::create([
            'nombre' => 'Carlos',
            'apellido' => 'Sosa',
            'documento_tipo' => 'CI',
            'documento_numero' => '4567890',
            'telefono' => '0981223344',
            'direccion' => 'Encarnación',
            'correo_electronico' => 'carlos.sosa@example.com',
        ]);

        $this->assertDatabaseHas('clientes', [
            'documento_numero' => '4567890',
            'nombre' => 'Carlos',
        ]);

        $this->assertEquals('Carlos Sosa', $cliente->nombre_completo);
        $this->assertEquals('CI: 4567890', $cliente->documento_completo);

        // Soft delete
        $cliente->delete();
        $this->assertSoftDeleted('clientes', ['id' => $cliente->id]);
    }

    public function test_fase4_creacion_encomienda_genera_tracking_e_historial(): void
    {
        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $this->actingAs($operador);

        $emisor = Cliente::create([
            'nombre' => 'Remitente',
            'apellido' => 'Uno',
            'documento_tipo' => 'CI',
            'documento_numero' => '1111111',
            'telefono' => '0981111111',
        ]);

        $receptor = Cliente::create([
            'nombre' => 'Destinatario',
            'apellido' => 'Dos',
            'documento_tipo' => 'CI',
            'documento_numero' => '2222222',
            'telefono' => '0982222222',
        ]);

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $emisor->id,
            'cliente_receptor_id' => $receptor->id,
            'origen' => 'Encarnación',
            'destino' => 'Asunción',
            'descripcion_paquete' => 'Caja con repuestos',
            'peso_kg' => 3.5,
            'dimensiones_largo' => 30,
            'dimensiones_ancho' => 20,
            'dimensiones_alto' => 15,
            'costo_servicio' => 30000,
            'estado_actual' => 'recibido',
            'operador_id' => $operador->id,
        ]);

        // Verificar formato Tracking ID ENC-YYYYMMDD-XXXXX
        $today = date('Ymd');
        $this->assertMatchesRegularExpression("/^ENC-{$today}-\\d{5}$/", $encomienda->tracking_id);

        // Verificar que se crea el historial de estado automáticamente
        $this->assertDatabaseHas('historial_estados', [
            'encomienda_id' => $encomienda->id,
            'estado_nuevo' => 'recibido',
            'user_id' => $operador->id,
        ]);
    }

    public function test_fase4_impresion_de_etiqueta_renderiza_con_exito(): void
    {
        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $this->actingAs($operador);

        $emisor = Cliente::create([
            'nombre' => 'Mario',
            'apellido' => 'Britez',
            'documento_tipo' => 'CI',
            'documento_numero' => '3333333',
            'telefono' => '0983333333',
        ]);

        $receptor = Cliente::create([
            'nombre' => 'Laura',
            'apellido' => 'Aquino',
            'documento_tipo' => 'CI',
            'documento_numero' => '4444444',
            'telefono' => '0984444444',
        ]);

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $emisor->id,
            'cliente_receptor_id' => $receptor->id,
            'origen' => 'Encarnación',
            'destino' => 'Ciudad del Este',
            'descripcion_paquete' => 'Documentos urgentes',
            'peso_kg' => 0.5,
            'costo_servicio' => 15000,
            'operador_id' => $operador->id,
        ]);

        $response = $this->get(route('encomiendas.etiqueta', $encomienda->id));
        $response->assertStatus(200);
        $response->assertSee($encomienda->tracking_id);
        $response->assertSee('Mario Britez');
        $response->assertSee('Laura Aquino');
    }
}
