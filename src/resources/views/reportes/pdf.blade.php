<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Operativo de Encomiendas</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            padding: 24px;
            font-size: 11px;
            color: #1f2937;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: table;
            width: 100%;
        }
        .header-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        .meta {
            text-align: right;
            font-size: 10px;
            color: #4b5563;
        }
        /* Tarjetas de Resumen */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-spacing: 8px 0;
        }
        .summary-card {
            display: table-cell;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            border-radius: 6px;
            width: 25%;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #6b7280;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #111827;
            margin-top: 4px;
        }
        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e0e7ff;
            color: #3730a3;
        }
        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-cell">
            <div class="title">Sistema de Información de Encomiendas</div>
            <div class="subtitle">Reporte Operativo y Consolidado de Envíos &bull; Encarnación, Paraguay</div>
        </div>
        <div class="header-cell meta">
            <div><strong>Generado:</strong> {{ $resumen['fecha_generacion'] }}</div>
            <div><strong>Filtro Fechas:</strong> {{ $resumen['filtros']['desde'] }} al {{ $resumen['filtros']['hasta'] }}</div>
            <div><strong>Ruta:</strong> {{ $resumen['filtros']['origen'] }} &rarr; {{ $resumen['filtros']['destino'] }}</div>
        </div>
    </div>

    <!-- Resumen KPIs -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Envíos</div>
            <div class="summary-value">{{ number_format($resumen['total_envios']) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Peso Total Transportado</div>
            <div class="summary-value">{{ number_format($resumen['total_peso'], 2, ',', '.') }} kg</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Recaudación Total</div>
            <div class="summary-value" style="color: #059669;">Gs. {{ number_format($resumen['recaudacion_total'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Promedio por Envío</div>
            <div class="summary-value">Gs. {{ number_format($resumen['promedio_costo'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabla Detallada -->
    <table>
        <thead>
            <tr>
                <th>Tracking ID</th>
                <th>Fecha</th>
                <th>Remitente</th>
                <th>Destinatario</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Peso</th>
                <th>Costo (Gs.)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($encomiendas as $item)
                <tr>
                    <td style="font-family: monospace; font-weight: bold;">{{ $item->tracking_id }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->emisor->nombre_completo }}</td>
                    <td>{{ $item->receptor->nombre_completo }}</td>
                    <td>{{ $item->origen }}</td>
                    <td>{{ $item->destino }}</td>
                    <td>{{ number_format($item->peso_kg, 2) }} kg</td>
                    <td style="font-weight: bold; text-align: right;">{{ number_format($item->costo_servicio, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge">{{ $item->estado_label }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 16px; color: #9ca3af;">
                        No se registraron encomiendas en el periodo seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión y Trazabilidad de Encomiendas.
    </div>
</body>
</html>
