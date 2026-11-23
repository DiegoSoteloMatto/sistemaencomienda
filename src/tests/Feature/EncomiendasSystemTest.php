<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Cliente;
use App\Models\Encomienda;
use App\Models\HistorialEstado;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
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

    public function test_fase5_flujo_completo_de_estados_logicos(): void
    {
        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $repartidor = User::where('email', 'repartidor@encomiendas.com')->first();
        $this->actingAs($operador);

        $emisor = Cliente::create([
            'nombre' => 'Ana',
            'apellido' => 'Vera',
            'documento_tipo' => 'CI',
            'documento_numero' => '5555555',
            'telefono' => '0985555555',
        ]);

        $receptor = Cliente::create([
            'nombre' => 'Pedro',
            'apellido' => 'Duarte',
            'documento_tipo' => 'CI',
            'documento_numero' => '6666666',
            'telefono' => '0986666666',
        ]);

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $emisor->id,
            'cliente_receptor_id' => $receptor->id,
            'origen' => 'Encarnación',
            'destino' => 'Asunción',
            'peso_kg' => 2.0,
            'costo_servicio' => 20000,
            'estado_actual' => 'recibido',
            'operador_id' => $operador->id,
        ]);

        // 1. Recibido -> Clasificación
        $encomienda->cambiarEstado('clasificacion', 'Paquete en depósito para clasificar');
        $this->assertEquals('clasificacion', $encomienda->fresh()->estado_actual);

        // 2. Clasificación -> Tránsito
        $encomienda->cambiarEstado('transito', 'Despachado en camión hacia destino');
        $this->assertEquals('transito', $encomienda->fresh()->estado_actual);

        // 3. Tránsito -> Arribado
        $encomienda->cambiarEstado('arribado', 'Llegada a sucursal de destino');
        $this->assertEquals('arribado', $encomienda->fresh()->estado_actual);

        // 4. Arribado -> Reparto (asignando repartidor)
        $encomienda->cambiarEstado('reparto', 'Asignado a móvil de reparto', null, $repartidor->id);
        $this->assertEquals('reparto', $encomienda->fresh()->estado_actual);
        $this->assertEquals($repartidor->id, $encomienda->fresh()->repartidor_id);

        // 5. Reparto -> Entregado
        $encomienda->cambiarEstado('entregado', 'Entregado a Pedro Duarte con CI 6666666');
        $this->assertEquals('entregado', $encomienda->fresh()->estado_actual);

        // Verificar que hay 6 registros en historial_estados (1 inicial + 5 transiciones)
        $this->assertCount(6, $encomienda->fresh()->historialEstados);
    }

    public function test_fase5_transicion_invalida_lanza_excepcion(): void
    {
        $operador = User::where('email', 'operador@encomiendas.com')->first();
        $this->actingAs($operador);

        $emisor = Cliente::create([
            'nombre' => 'Test',
            'apellido' => 'Test',
            'documento_tipo' => 'CI',
            'documento_numero' => '7777777',
            'telefono' => '0987777777',
        ]);

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $emisor->id,
            'cliente_receptor_id' => $emisor->id,
            'origen' => 'Encarnación',
            'destino' => 'Asunción',
            'peso_kg' => 1.0,
            'costo_servicio' => 15000,
            'estado_actual' => 'recibido',
            'operador_id' => $operador->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        // Intentar pasar directamente de recibido a entregado sin pasar por etapas intermedias
        $encomienda->cambiarEstado('entregado');
    }

    public function test_fase6_rastreo_publico_accesible_sin_login(): void
    {
        // Petición sin autenticar a /tracking
        $response = $this->get(route('tracking'));
        $response->assertStatus(200);
        $response->assertSee('Consulta y Trazabilidad de Envíos');
    }

    public function test_fase6_rastreo_publico_encuentra_paquete(): void
    {
        $emisor = Cliente::create([
            'nombre' => 'Publico',
            'apellido' => 'Emisor',
            'documento_tipo' => 'CI',
            'documento_numero' => '8888888',
            'telefono' => '0988888888',
        ]);

        $encomienda = Encomienda::create([
            'cliente_emisor_id' => $emisor->id,
            'cliente_receptor_id' => $emisor->id,
            'origen' => 'Encarnación',
            'destino' => 'Ciudad del Este',
            'peso_kg' => 1.2,
            'costo_servicio' => 18000,
            'estado_actual' => 'recibido',
        ]);

        $response = $this->get(route('tracking', ['codigo' => $encomienda->tracking_id]));
        $response->assertStatus(200);
        $response->assertSee($encomienda->tracking_id);
        $response->assertSee('Encarnación');
        $response->assertSee('Ciudad del Este');
        $response->assertSee('Historial Cronológico de Movimientos');
    }
}
