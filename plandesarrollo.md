# Plan de Desarrollo por Fases
## Sistema de Información para la Gestión y Trazabilidad de Envíos
### Empresa de Encomiendas – Ciudad de Encarnación, Paraguay

---

## Información del Proyecto

| Parámetro            | Valor                                                     |
|----------------------|-----------------------------------------------------------|
| **Proyecto**         | Sistema de Gestión y Trazabilidad de Encomiendas          |
| **Base de datos**    | `encomiendabd`                                            |
| **Usuario BD**       | `postgres`                                                |
| **Contraseña BD**    | `master`                                                  |
| **Stack tecnológico**| TALL Stack: Tailwind CSS + Alpine.js + Livewire + Laravel |
| **Motor de BD**      | PostgreSQL                                                |
| **Metodología**      | Prototipado Evolutivo                                     |

---

## Resumen Ejecutivo

El sistema centralizará la administración de usuarios, clientes, paquetes y estados logísticos para optimizar la gestión de envíos en empresas de encomiendas de Encarnación. Se eliminarán los registros manuales, se reducirán errores de captura en ≥80% y se ofrecerá consulta de trazabilidad pública en tiempo real.

---

## Fases de Desarrollo

---

## FASE 1 – Configuración del Entorno y Estructura Base del Proyecto
**Duración estimada:** 1 semana  
**Objetivo:** Preparar el entorno de desarrollo, la base de datos y el scaffolding inicial del proyecto Laravel.

### Tareas

#### 1.1 Configuración del Entorno de Desarrollo
- [x] Instalar PHP 8.3+, Composer, Node.js y NPM
- [x] Instalar y configurar PostgreSQL con la base de datos `encomiendabd`
- [x] Crear el proyecto Laravel con TALL Stack (`laravel/laravel`)
- [x] Instalar dependencias clave:
  - `livewire/livewire`
  - `laravel/breeze` (con Livewire/Alpine)
  - `spatie/laravel-permission` (RBAC)
- [x] Instalar y configurar Tailwind CSS

#### 1.2 Configuración de la Base de Datos
- [x] Configurar `.env` con los parámetros de conexión a PostgreSQL:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=encomiendabd
  DB_USERNAME=postgres
  DB_PASSWORD=master
  ```
- [x] Verificar conexión con `php artisan db:show`

#### 1.3 Estructura y Convenciones del Proyecto
- [x] Definir estructura de carpetas para módulos (Clientes, Encomiendas, Trazabilidad, Reportes)
- [x] Configurar el sistema de rutas base (web.php, auth)
- [x] Configurar plantilla base (layout principal con Tailwind CSS)
- [x] Configurar sistema de auditoría (Spatie Activity Log o similar)

### Entregables de la Fase 1
- Proyecto Laravel funcional conectado a PostgreSQL `encomiendabd`
- Layout base con Tailwind CSS
- Repositorio Git inicializado con `.gitignore` configurado

---

## FASE 2 – Autenticación, Roles y Gestión de Usuarios (RF-01)
**Duración estimada:** 1 semana  
**Objetivo:** Implementar el sistema de control de acceso por roles (RBAC) y el registro de auditoría.

### Tareas

#### 2.1 Sistema de Autenticación
- [x] Implementar login/logout con Laravel Breeze o Fortify
- [x] Protección de rutas mediante middleware `auth`
- [x] Manejo de sesiones seguras y protección CSRF

#### 2.2 Gestión de Roles y Permisos (RBAC)
Definir los siguientes roles con `spatie/laravel-permission`:

| Rol                    | Permisos Principales                                                        |
|------------------------|-----------------------------------------------------------------------------|
| **Administrador**      | Gestión global: usuarios, roles, parámetros, auditorías, informes           |
| **Operador**           | Registrar encomiendas, clientes, cambio de estado (recepción/despacho), etiquetas |
| **Repartidor**         | Ver encomiendas asignadas, actualizar estado (En tránsito, Entregado), acuse de recibo |
| **Cliente** (público)  | Consulta pública de trazabilidad por código de seguimiento (sin login)      |

- [x] Crear seeders para roles y permisos iniciales
- [x] Crear pantalla de administración de usuarios (CRUD) para el rol Administrador
- [x] Aplicar middleware de roles en todas las rutas protegidas

#### 2.3 Registro de Auditoría (RF-01.2)
- [x] Registrar logs para acciones críticas: creación, modificación, cambio de estado
- [x] Almacenar: usuario responsable, acción, fecha/hora, entidad afectada
- [x] Pantalla de consulta de logs de auditoría para el Administrador

### Migración de Base de Datos
```sql
-- Tablas gestionadas por Spatie Permission
roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

-- Tabla de usuarios
users (id, name, email, password, role_id, activo, created_at, updated_at)

-- Tabla de logs de auditoría
audit_logs (id, user_id, action, entity_type, entity_id, description, ip_address, created_at)
```

### Entregables de la Fase 2
- Sistema de login funcional con control de acceso por roles
- Panel de administración de usuarios
- Registro de auditoría operativo

---

## FASE 3 – Gestión de Clientes (RF-02)
**Duración estimada:** 3–4 días  
**Objetivo:** Implementar el módulo CRUD de clientes (emisores y receptores).

### Tareas

#### 3.1 Módulo de Clientes
- [x] Migración de tabla `clientes`:
  ```sql
  clientes (
    id, nombre, apellido, documento_tipo (CI/RUC), documento_numero,
    telefono, direccion, correo_electronico,
    created_at, updated_at, deleted_at
  )
  ```
- [x] Modelo `Cliente` con SoftDeletes
- [x] Componente Livewire para listado de clientes con búsqueda en tiempo real (RF-02.2)
- [x] Formulario de creación/edición de clientes (validations Laravel)
- [x] Funcionalidad de búsqueda rápida para usar al momento de registrar un envío

#### 3.2 Interfaz de Usuario
- [x] Tabla responsiva con Tailwind CSS + Alpine.js para filtros
- [x] Formulario modal o página dedicada para alta/edición
- [x] Confirmación de eliminación lógica (SoftDelete)

### Entregables de la Fase 3
- Módulo CRUD de clientes completamente funcional
- Búsqueda rápida de clientes integrable en formulario de encomiendas

---

## FASE 4 – Gestión de Encomiendas y Admisión (RF-03)
**Duración estimada:** 1–2 semanas  
**Objetivo:** Implementar el registro de encomiendas con generación de Tracking ID y emisión de etiquetas.

### Tareas

#### 4.1 Módulo de Encomiendas
- [x] Migración de tabla `encomiendas`:
  ```sql
  encomiendas (
    id, tracking_id (único, auto-generado),
    cliente_emisor_id (FK clientes), cliente_receptor_id (FK clientes),
    origen, destino,
    descripcion_paquete, peso_kg, dimensiones (largo, ancho, alto),
    costo_servicio,
    estado_actual (ENUM: recibido, clasificacion, transito, arribado, reparto, entregado, incidencia),
    operador_id (FK users), created_at, updated_at
  )
  ```
- [x] Generación automática del `tracking_id` único (RF-03.2): formato `ENC-YYYYMMDD-XXXXX`
- [x] Componente Livewire para búsqueda y selección de cliente emisor/receptor
- [x] Formulario de admisión de encomienda con validaciones completas
- [x] Listado de encomiendas con filtros por estado, fecha, origen/destino

#### 4.2 Etiquetas (RF-03.3)
- [x] Generación de etiqueta imprimible (PDF o vista HTML) con:
  - Código de seguimiento (Tracking ID)
  - Datos del emisor y receptor
  - Origen y destino
  - Código de barras o QR del Tracking ID (librería `simplesoftwareio/simple-qrcode`)

### Entregables de la Fase 4
- Registro de encomiendas funcional con Tracking ID generado automáticamente
- Etiqueta imprimible por encomienda

---

## FASE 5 – Control Logístico y Flujo de Estados (RF-04)
**Duración estimada:** 1 semana  
**Objetivo:** Implementar la gestión del ciclo de vida de los envíos con historial de cambios de estado.

### Tareas

#### 5.1 Gestión de Estados Logísticos
Flujo de estados definido (RF-04.1):
```
Recibido en Origen → En Clasificación → En Tránsito / Despachado
    → Arribado a Destino → En Reparto → Entregado
                                      ↘ Incidencia (Devuelto / Retenido)
```

- [x] Migración de tabla `historial_estados`:
  ```sql
  historial_estados (
    id, encomienda_id (FK), estado_anterior, estado_nuevo,
    observaciones, user_id (FK), timestamp (fecha y hora),
    created_at
  )
  ```
- [x] Lógica de negocio para validar transiciones de estado permitidas
- [x] Componente Livewire para cambio de estado con campo de observaciones
- [x] Restricción de estados según rol del usuario (Operador vs. Repartidor)

#### 5.2 Vista para Repartidor
- [x] Listado de encomiendas asignadas al repartidor logueado
- [x] Botones de acción: "Marcar en Tránsito", "Registrar Entrega", "Reportar Incidencia"
- [x] Registro de acuse de recibo (firma o confirmación digital básica)

### Entregables de la Fase 5
- Flujo de estados completo con historial y timestamps
- Vista funcional para repartidores

---

## FASE 6 – Módulo de Trazabilidad Pública (RF-05)
**Duración estimada:** 3–4 días  
**Objetivo:** Implementar la interfaz pública de consulta de estado de envíos sin necesidad de login.

### Tareas

#### 6.1 Portal de Consulta Pública
- [x] Ruta pública `/tracking` accesible sin autenticación (RF-05.1)
- [x] Formulario de búsqueda con campo Tracking ID o número de guía
- [x] Componente Livewire para actualización dinámica de resultados

#### 6.2 Vista de Resultado del Seguimiento (RF-05.2)
- [x] Mostrar estado actual del paquete con ícono visual
- [x] Línea de tiempo (timeline) del historial completo de estados
- [x] Informar: estado actual, ubicación aproximada / centro de acopio, fecha de última actualización
- [x] Diseño responsivo amigable para móviles con Tailwind CSS

### Entregables de la Fase 6
- Portal público de trazabilidad operativo
- Respuesta de consulta en menos de 5 segundos (RNF objetivo)

---

## FASE 7 – Informes y Reportes Operativos (RF-06)
**Duración estimada:** 3–4 días  
**Objetivo:** Implementar los reportes operativos para el rol Administrador.

### Tareas

#### 7.1 Reportes
- [x] Reporte de volumen de encomiendas por periodo (RF-06.1):
  - Filtros: rango de fechas, origen, destino
  - Gráfico de barras o línea (puede usar Chart.js via Alpine.js)
  - Exportación a PDF o CSV

- [x] Reporte de tiempos promedio de tránsito entre estados (RF-06.2):
  - Análisis de tiempos entre `historial_estados`
  - Identificación de cuellos de botella en el flujo logístico

#### 7.2 Dashboard Principal (Administrador)
- [x] Resumen: total de encomiendas por estado (tarjetas KPI)
- [x] Encomiendas recientes
- [x] Alertas de encomiendas con incidencias o sin actualización reciente

### Entregables de la Fase 7
- Módulo de reportes con filtros y exportación
- Dashboard con métricas clave en tiempo real

---

## FASE 8 – Seguridad, Pruebas y Ajustes Finales (RNF)
**Duración estimada:** 1 semana  
**Objetivo:** Garantizar la seguridad, rendimiento y calidad del sistema antes del despliegue.

### Tareas

#### 8.1 Hardening de Seguridad (RNF-04)
- [x] Verificar encriptación de contraseñas con `bcrypt` (Laravel por defecto)
- [x] Revisión de protección CSRF en todos los formularios
- [x] Verificar protección contra SQLi mediante Eloquent ORM (queries parametrizadas)
- [x] Sanitización de inputs para prevenir XSS
- [x] Revisión de integridad referencial en BD (Foreign Keys activas en PostgreSQL)

#### 8.2 Pruebas Funcionales
- [x] Pruebas unitarias por módulo (PHPUnit):
  - Registro de usuarios y roles
  - Admisión de encomiendas y generación de Tracking ID
  - Cambio de estados y validación de flujo
  - Consulta de trazabilidad pública
- [x] Pruebas de integración del flujo end-to-end: recepción → entrega
- [x] Pruebas de rendimiento: tiempo de respuesta < 2 segundos (RNF-01)

#### 8.3 Ajustes de UI/UX
- [x] Optimizar formularios para ingreso rápido por teclado (RNF-03)
- [x] Verificar responsividad en dispositivos móviles
- [x] Revisión de accesibilidad básica

### Entregables de la Fase 8
- Sistema sin vulnerabilidades críticas conocidas
- Suite de pruebas automatizadas pasando
- Rendimiento dentro de los parámetros RNF

---

## FASE 9 – Despliegue y Prueba Piloto
**Duración estimada:** 1–2 semanas  
**Objetivo:** Desplegar el sistema en entorno real y medir el impacto frente a la línea base.

### Tareas

#### 9.1 Despliegue
- [x] Configurar servidor de producción (puede ser VPS con Nginx/Apache + PHP-FPM)
- [x] Configurar PostgreSQL en producción con usuario y contraseña seguros
- [x] Ejecutar migraciones y seeders iniciales en producción
- [x] Configurar respaldos automáticos de la base de datos

#### 9.2 Capacitación
- [x] Capacitar a operadores en registro de clientes y encomiendas
- [x] Capacitar a repartidores en uso del módulo de actualización de estados
- [x] Entregar manual de usuario básico

#### 9.3 Medición de KPIs (Post-Implementación)
Medir los siguientes indicadores y comparar con la línea base:

| Indicador                        | Meta Esperada                                 |
|----------------------------------|-----------------------------------------------|
| Tiempo promedio de registro      | ≥ 50% de reducción vs. proceso manual         |
| Actualización de estado          | En tiempo real o en pocos minutos             |
| Tiempo de consulta de trazabilidad | < 5 segundos vía web                        |
| Tasa de errores / inconsistencias | ≥ 80% de reducción vs. proceso manual       |

### Entregables de la Fase 9
- Sistema desplegado en producción
- Personal capacitado
- Informe de KPIs post-implementación

---

## Cronograma Resumen

| Fase | Módulo                                          | Duración Estimada |
|------|-------------------------------------------------|-------------------|
| 1    | Configuración del Entorno y Estructura Base     | 1 semana          |
| 2    | Autenticación, Roles y Auditoría               | 1 semana          |
| 3    | Gestión de Clientes                             | 3–4 días          |
| 4    | Gestión de Encomiendas y Admisión               | 1–2 semanas       |
| 5    | Control Logístico y Flujo de Estados            | 1 semana          |
| 6    | Módulo de Trazabilidad Pública                  | 3–4 días          |
| 7    | Informes y Reportes Operativos                  | 3–4 días          |
| 8    | Seguridad, Pruebas y Ajustes Finales            | 1 semana          |
| 9    | Despliegue y Prueba Piloto                      | 1–2 semanas       |
| **TOTAL** | —                                         | **~8–10 semanas** |

---

## Diagrama de Flujo de Estados Logísticos

```
[RECIBIDO EN ORIGEN]
        │
        ▼
[EN CLASIFICACIÓN]
        │
        ▼
[EN TRÁNSITO / DESPACHADO]
        │
        ▼
[ARRIBADO A DESTINO]
        │
        ▼
  [EN REPARTO]
        │
   ┌────┴────┐
   ▼         ▼
[ENTREGADO] [INCIDENCIA]
            (Devuelto / Retenido)
```

---

## Esquema de Base de Datos Principal (`encomiendabd`)

```sql
-- Usuarios del sistema
users (id, name, email, password, activo, created_at, updated_at)

-- Roles y permisos (Spatie Permission)
roles, permissions, model_has_roles, model_has_permissions, role_has_permissions

-- Clientes (emisores y receptores)
clientes (id, nombre, apellido, documento_tipo, documento_numero,
          telefono, direccion, correo_electronico, created_at, updated_at, deleted_at)

-- Encomiendas
encomiendas (id, tracking_id, cliente_emisor_id, cliente_receptor_id,
             origen, destino, descripcion_paquete, peso_kg,
             dimensiones_largo, dimensiones_ancho, dimensiones_alto,
             costo_servicio, estado_actual, operador_id, created_at, updated_at)

-- Historial de cambios de estado
historial_estados (id, encomienda_id, estado_anterior, estado_nuevo,
                   observaciones, user_id, timestamp, created_at)

-- Logs de auditoría
audit_logs (id, user_id, action, entity_type, entity_id,
            description, ip_address, created_at)
```

---

*Documento generado el 25/10/2026 – Basado en el PRD del Sistema de Información para la Gestión y Trazabilidad de Envíos.*
