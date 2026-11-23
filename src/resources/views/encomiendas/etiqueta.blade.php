<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta {{ $encomienda->tracking_id }} - Sistema de Encomiendas</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            background-color: #f3f4f6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .no-print {
            margin-bottom: 20px;
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background: #4338ca;
        }
        /* Etiqueta Térmica / Estándar 10x15cm aprox */
        .etiqueta-container {
            width: 380px;
            background: #ffffff;
            border: 2px solid #000;
            padding: 16px;
            border-radius: 4px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .empresa {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .subempresa {
            font-size: 10px;
            color: #333;
        }
        .tracking-box {
            text-align: center;
            padding: 8px 0;
            background: #f9fafb;
            border: 1px dashed #000;
            margin-bottom: 12px;
        }
        .tracking-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .tracking-id {
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .qr-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .qr-section svg {
            width: 120px;
            height: 120px;
        }
        .ruta-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 6px 0;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: bold;
        }
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 12px;
        }
        .info-card {
            border: 1px solid #999;
            padding: 8px;
            border-radius: 4px;
        }
        .info-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }
        .info-name {
            font-size: 13px;
            font-weight: bold;
        }
        .info-detail {
            font-size: 11px;
            color: #222;
            margin-top: 2px;
        }
        .footer-pkg {
            border-top: 1px solid #000;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .etiqueta-container {
                border: 2px solid #000;
                width: 100%;
                max-width: 400px;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Imprimir Etiqueta</button>
    </div>

    <div class="etiqueta-container">
        <div class="header">
            <div class="empresa">SISTEMA DE ENCOMIENDAS</div>
            <div class="subempresa">Encarnación - Paraguay &bull; Guía de Transporte</div>
        </div>

        <div class="tracking-box">
            <div class="tracking-label">Código de Seguimiento (Tracking ID)</div>
            <div class="tracking-id">{{ $encomienda->tracking_id }}</div>
        </div>

        @if($qrCode)
            <div class="qr-section">
                {!! $qrCode !!}
            </div>
        @endif

        <div class="ruta-box">
            <div>ORIGEN: {{ strtoupper($encomienda->origen) }}</div>
            <div>&rarr;</div>
            <div>DESTINO: {{ strtoupper($encomienda->destino) }}</div>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-title">REMITENTE (EMISOR)</div>
                <div class="info-name">{{ $encomienda->emisor->nombre_completo }}</div>
                <div class="info-detail">{{ $encomienda->emisor->documento_completo }} &bull; Tel: {{ $encomienda->emisor->telefono }}</div>
                @if($encomienda->emisor->direccion)
                    <div class="info-detail">{{ $encomienda->emisor->direccion }}</div>
                @endif
            </div>

            <div class="info-card">
                <div class="info-title">DESTINATARIO (RECEPTOR)</div>
                <div class="info-name">{{ $encomienda->receptor->nombre_completo }}</div>
                <div class="info-detail">{{ $encomienda->receptor->documento_completo }} &bull; Tel: {{ $encomienda->receptor->telefono }}</div>
                @if($encomienda->receptor->direccion)
                    <div class="info-detail">{{ $encomienda->receptor->direccion }}</div>
                @endif
            </div>
        </div>

        <div class="footer-pkg">
            <div>
                <strong>Peso:</strong> {{ number_format($encomienda->peso_kg, 2) }} kg<br>
                <strong>Fecha:</strong> {{ $encomienda->created_at->format('d/m/Y H:i') }}
            </div>
            <div style="text-align: right;">
                <strong>Costo:</strong> Gs. {{ number_format($encomienda->costo_servicio, 0, ',', '.') }}<br>
                <strong>Op:</strong> {{ $encomienda->operador?->name ?? 'Sistema' }}
            </div>
        </div>
    </div>
</body>
</html>
