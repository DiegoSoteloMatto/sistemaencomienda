# Manual de Usuario – Sistema de Gestión y Trazabilidad de Encomiendas

**Versión:** 1.0  
**Fecha:** Noviembre 2026  
**Empresa:** —  

---

## Índice

1. [Acceso al Sistema](#1-acceso-al-sistema)
2. [Perfil de Administrador](#2-perfil-de-administrador)
3. [Perfil de Operador](#3-perfil-de-operador)
4. [Perfil de Repartidor](#4-perfil-de-repartidor)
5. [Portal Público de Trazabilidad](#5-portal-público-de-trazabilidad)
6. [Preguntas Frecuentes](#6-preguntas-frecuentes)

---

## 1. Acceso al Sistema

### 1.1 Inicio de Sesión

1. Abrir el navegador y acceder a `https://tudominio.com`
2. En la página de inicio, hacer clic en **"Iniciar Sesión"**
3. Ingresar el **correo electrónico** y **contraseña** asignados por el Administrador
4. Presionar **"Ingresar"**

> **Nota:** Si olvidó su contraseña, contacte al Administrador del sistema para que la restablezca.

### 1.2 Usuarios del Sistema

| Rol           | Acceso                                                        |
|---------------|---------------------------------------------------------------|
| Administrador | Acceso total: usuarios, clientes, encomiendas, reportes, auditoría |
| Operador      | Registro de clientes, admisión y gestión de encomiendas       |
| Repartidor    | Vista de encomiendas asignadas y actualización de estado      |

---

## 2. Perfil de Administrador

### 2.1 Dashboard

Al ingresar verá el **Dashboard** con:
- Tarjetas de resumen por estado de envíos
- Encomiendas recientes
- Alertas de paquetes sin movimiento

### 2.2 Gestión de Usuarios

**Ruta:** Menú → **Usuarios**

Para **crear** un usuario:
1. Hacer clic en **"+ Nuevo Usuario"**
2. Completar nombre, correo, contraseña y asignar rol
3. Presionar **"Guardar"**

Para **editar** o **desactivar** un usuario:
1. Hacer clic en el ícono de lápiz ✏️ junto al usuario
2. Modificar los campos necesarios
3. Para desactivar: desmarcar la casilla **"Activo"**
4. Presionar **"Guardar"**

### 2.3 Reportes Operativos

**Ruta:** Menú → **Reportes**

**Reporte de Volumen y Recaudación:**
1. Seleccionar el rango de fechas (Desde / Hasta)
2. Filtrar por Origen, Destino o Estado (opcional)
3. Los KPIs y la distribución se actualizan automáticamente
4. Para exportar: usar **"Exportar CSV"** (Excel) o **"Descargar PDF"**

**Análisis de Tiempos (Cuellos de Botella):**
1. Hacer clic en la pestaña **"Tiempos de Tránsito y Cuellos de Botella"**
2. Ver los tiempos promedio por tramo logístico
3. La tabla inferior muestra paquetes sin movimiento > 48 horas

### 2.4 Auditoría del Sistema

**Ruta:** Menú → **Auditoría**

Permite revisar todas las acciones realizadas por los usuarios: creaciones, modificaciones y cambios de estado, con fecha, hora e IP.

---

## 3. Perfil de Operador

### 3.1 Gestión de Clientes

**Ruta:** Menú → **Clientes**

Para **registrar un cliente**:
1. Hacer clic en **"+ Nuevo Cliente"**
2. Ingresar: nombre, apellido, tipo y número de documento, teléfono y dirección
3. Presionar **"Guardar"**

Para **buscar un cliente existente**:
- Usar el campo de búsqueda (nombre, apellido o documento)

> Los clientes eliminados se archivan (papelera) y pueden restaurarse.

### 3.2 Registro de Encomiendas (Admisión)

**Ruta:** Menú → **Encomiendas** → **"+ Nueva Encomienda"**

**Pasos:**
1. Buscar y seleccionar el **Cliente Emisor** (remitente)
2. Buscar y seleccionar el **Cliente Receptor** (destinatario)
3. Completar **Origen** y **Destino**
4. Ingresar descripción del paquete, **peso** (kg) y dimensiones
5. Ingresar el **costo del servicio** (Gs.)
6. Presionar **"Registrar Encomienda"**

El sistema generará automáticamente el **Tracking ID** con formato `ENC-YYYYMMDD-XXXXX`.

### 3.3 Etiqueta de Envío

Una vez registrada la encomienda:
1. Abrir el detalle de la encomienda
2. Hacer clic en **"🖨 Imprimir Etiqueta"**
3. Se abrirá una vista imprimible con el QR del Tracking ID

### 3.4 Gestión de Estados (Operador)

Desde el **detalle de la encomienda**, el Operador puede cambiar el estado a:
- En Clasificación
- En Tránsito / Despachado
- Arribado a Destino

Pasos:
1. Abrir la encomienda
2. Hacer clic en **"Cambiar Estado"**
3. Seleccionar el nuevo estado y agregar observaciones
4. Confirmar

---

## 4. Perfil de Repartidor

### 4.1 Vista de Repartos

**Ruta:** Menú → **Repartos**

Muestra todas las encomiendas **asignadas al repartidor** logueado en estado **"En Reparto"**.

### 4.2 Registrar Entrega

1. Localizar la encomienda en la lista
2. Hacer clic en **"✓ Marcar Entregado"**
3. Confirmar la acción (confirmación digital básica)
4. El sistema registra la entrega con fecha/hora y el nombre del repartidor

### 4.3 Reportar Incidencia

Si hay un problema al entregar:
1. Hacer clic en **"⚠ Reportar Incidencia"**
2. Escribir el motivo (ej: "Destinatario ausente, paquete retenido")
3. Confirmar

El estado cambia a **"Incidencia"** y queda visible para el Operador y Administrador.

---

## 5. Portal Público de Trazabilidad

Accesible **sin login** en: `https://tudominio.com/tracking`

**Consultar el estado de un envío:**
1. Ingresar el **Tracking ID** (ej: `ENC-20261122-00001`)
2. Presionar **"Rastrear"** o presionar Enter
3. Se mostrará:
   - Estado actual del paquete con ícono visual
   - Barra de progreso de etapas
   - Historial completo de movimientos con fecha y hora

> No se requiere cuenta ni contraseña. Esta información puede compartirse con los clientes.

---

## 6. Preguntas Frecuentes

**¿Cómo recupero mi contraseña?**  
Contactar al Administrador del sistema para que la restablezca desde el módulo de Usuarios.

**¿Puedo modificar una encomienda ya registrada?**  
Sí, siempre que esté en estado **"Recibido en Origen"**. Una vez en tránsito, solo se puede cambiar el estado.

**¿Qué pasa si ingreso un estado incorrecto?**  
Contacte al Administrador. Cada cambio queda registrado en el Historial y en la Auditoría, por lo que puede rastrearse y corregirse documentalmente.

**¿El sistema funciona en celulares?**  
Sí. La interfaz es responsiva y funciona correctamente en navegadores de smartphones (Chrome, Firefox, Safari).

**¿Dónde veo las encomiendas con problemas?**  
En **Reportes → Tiempos de Tránsito**, la tabla de "Cuellos de Botella" muestra todos los paquetes sin movimiento por más de 48 horas.

---

*Manual generado – Sistema de Gestión y Trazabilidad de Encomiendas v1.0*
