<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hoja de servicio {{ $servicio->folio }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 9mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #102f46;
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin: 12px auto;
            width: min(100% - 24px, 195mm);
        }

        .button {
            display: inline-block;
            padding: 9px 14px;
            border: 1px solid #8eaaba;
            border-radius: 5px;
            background: #fff;
            color: #063b60;
            font: inherit;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .button.primary {
            border-color: #063b60;
            background: #063b60;
            color: #fff;
        }

        .page {
            display: flex;
            flex-direction: column;
            width: 195mm;
            min-height: 260mm;
            margin: 12px auto;
            padding: 8mm;
            background: #fff;
            box-shadow: 0 2px 12px rgb(16 47 70 / 15%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td,
        th {
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .brand {
            color: #0874b6;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: .4px;
        }

        .subbrand {
            margin: 3px 0;
            font-size: 9px;
            letter-spacing: 1.4px;
        }

        .header-right {
            width: 34%;
            padding-left: 8px;
            color: #496779;
            text-align: right;
        }

        .title {
            margin-top: 10px;
            background: #063b60;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .title td {
            padding: 8px;
            vertical-align: middle;
        }

        .title .folio {
            width: 31%;
            border: 3px solid #063b60;
            background: white;
            color: #063b60;
            text-align: center;
        }

        .date {
            padding: 9px 2px;
        }

        .columns {
            margin-top: 3px;
        }

        .column {
            width: 49%;
            padding: 0;
        }

        .spacer {
            width: 2%;
        }

        .box {
            min-height: 36mm;
            border: 1px solid #8eaaba;
        }

        .section {
            min-height: 8mm;
            padding: 6px 8px;
            background: #145174;
            color: white;
            font-size: 9px;
            font-weight: bold;
            line-height: 1.35;
        }

        .content {
            padding: 7px 9px;
            line-height: 1.5;
        }

        .data {
            display: table;
            width: 100%;
            padding: 4px 0;
            border-bottom: 1px solid #d5e0e7;
            table-layout: fixed;
        }

        .label {
            display: table-cell;
            width: 82px;
            padding-right: 6px;
            font-weight: bold;
            vertical-align: top;
        }

        .value {
            display: table-cell;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        .gap {
            height: 8px;
        }

        .description {
            min-height: 38mm;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .description-value {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .costs {
            margin-top: 4px;
        }

        .costs th,
        .costs td {
            padding: 7px 8px;
            border: 1px solid #b9cbd6;
            vertical-align: middle;
        }

        .costs th {
            background: #e8f0f5;
            color: #063b60;
            font-size: 9px;
            text-align: left;
        }

        .concept {
            width: 28%;
        }

        .amount {
            width: 23%;
            text-align: right;
        }

        .total {
            background: #063b60;
            color: white;
            font-weight: bold;
            text-align: right;
        }

        .state {
            margin-top: 9px;
            border: 1px solid #8eaaba;
        }

        .state-info {
            width: 70%;
        }

        .stamp {
            width: 30%;
            height: 58px;
            border: 1px dashed #8fa8b8;
            color: #718a9b;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
            vertical-align: middle;
        }

        .footer {
            margin-top: auto;
            padding: 9px;
            background: #063b60;
            color: white;
            text-align: center;
            font-size: 9px;
        }

        .pdf-document .footer {
            position: fixed;
            right: 0;
            bottom: 0;
            left: 0;
            margin: 0;
        }

        @media screen and (max-width: 700px) {

            .toolbar,
            .page {
                width: calc(100% - 20px);
            }

            .page {
                min-height: 0;
                margin: 10px auto;
                padding: 18px;
            }

            .columns,
            .columns tbody,
            .columns tr {
                display: block;
                width: 100%;
            }

            .columns tr {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 10px;
            }

            .columns td.column {
                display: block;
                width: 100%;
                padding: 0;
            }

            .columns td.spacer {
                display: none;
            }

            .columns .box {
                height: auto;
            }
        }

        @media print {

            html,
            body {
                width: 100%;
                min-height: 0;
                background: white;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: 100%;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .page {
                min-height: 261mm;
            }

            .footer {
                position: fixed;
                right: 0;
                bottom: 0;
                left: 0;
                margin: 0;
            }

            .box,
            .costs,
            .state,
            .title {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body @class(['pdf-document' => $pdf ?? false])>
    @unless ($pdf ?? false)
        <nav class="toolbar" aria-label="Acciones de impresión">
            <a class="button" href="{{ route('ti.servicios.mostrar', $servicio) }}">Volver al seguimiento</a>
            <button class="button primary" type="button" onclick="window.print()">Imprimir hoja</button>
        </nav>
    @endunless

    <main class="page">
        <table>
            <tr>
                <td>
                    <div class="brand">PCS SOLUTIONS TI</div>
                    <div class="subbrand">SERVICIO TÉCNICO ESPECIALIZADO</div>
                    <div>Computadoras · Laptops · Impresoras · Software</div>
                </td>
                <td class="header-right">HOJA DE SERVICIO<br>Control de recepción y seguimiento</td>
            </tr>
        </table>

        <table class="title">
            <tr>
                <td>HOJA DE SERVICIO / RECEPCIÓN DE EQUIPO</td>
                <td class="folio">Folio: {{ $servicio->folio }}</td>
            </tr>
        </table>

        <div class="date">
            <strong>Fecha de recepción:</strong>
            {{ $servicio->fecha_recepcion->copy()->timezone('America/Mexico_City')->format('d/m/Y H:i') }}
            &nbsp;&nbsp;&nbsp; <strong>ID del cliente:</strong> {{ $servicio->cliente->id }}
        </div>

        <table class="columns">
            <tr>
                <td class="column">
                    <div class="box">
                        <div class="section">1. DATOS DEL CLIENTE</div>
                        <div class="content">
                            <div class="data"><span class="label">Nombre:</span><span
                                    class="value">{{ $servicio->cliente->nombre }}</span></div>
                            <div class="data"><span class="label">Teléfono:</span><span
                                    class="value">{{ $servicio->cliente->telefono ?: '—' }}</span></div>
                            <div class="data"><span class="label">Correo:</span><span
                                    class="value">{{ $servicio->cliente->correo ?: '—' }}</span></div>
                        </div>
                    </div>
                </td>
                <td class="spacer"></td>
                <td class="column">
                    <div class="box equipment">
                        <div class="section">2. DATOS DEL EQUIPO</div>
                        <div class="content">
                            <div class="data"><span class="label">Tipo:</span><span
                                    class="value">{{ $servicio->tipo_equipo }}</span></div>
                            <div class="data"><span class="label">Marca:</span><span
                                    class="value">{{ $servicio->marca ?: '—' }}</span></div>
                            <div class="data"><span class="label">Modelo:</span><span
                                    class="value">{{ $servicio->modelo ?: '—' }}</span></div>
                            <div class="data"><span class="label">Serie:</span><span
                                    class="value">{{ $servicio->serie ?: '—' }}</span></div>
                            <div class="data"><span class="label">Estado físico:</span><span
                                    class="value">{{ $servicio->estado_fisico }}</span></div>
                            <div class="data"><span class="label">Accesorios:</span><span
                                    class="value">{{ $servicio->accesorios ?: 'Sin accesorios' }}</span></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="gap"></div>

        <table class="columns">
            <tr>
                <td class="column">
                    <div class="box">
                        <div class="section">3. SERVICIO SOLICITADO / FALLA REPORTADA</div>
                        <div class="content description">
                            <strong>Servicio(s):</strong>
                            <span class="description-value">{{ $servicio->observaciones ?: 'Pendiente de revisión' }}</span>
                            <br><br>
                            <strong>Falla que reporta el cliente:</strong><br>
                            <span class="description-value">{{ $servicio->falla_reportada ?: 'No reportó una falla específica.' }}</span>
                        </div>
                    </div>
                </td>
                <td class="spacer"></td>
                <td class="column">
                    <div class="box">
                        <div class="section">4. DIAGNÓSTICO / OBSERVACIONES</div>
                        <div class="content description"><span class="description-value">{{ $servicio->diagnostico ?: 'Pendiente de revisión.' }}</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="gap"></div>

        <div class="section">5. COTIZACIÓN</div>
        <table class="costs">
            <thead>
                <tr>
                    <th class="concept">CONCEPTO</th>
                    <th>DESCRIPCIÓN</th>
                    <th class="amount">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Costo del servicio</td>
                    <td>{{ $servicio->autorizacion === 'Rechazado' ? 'Cotización no autorizada' : ($servicio->precio === null ? ($servicio->estado_servicio === 'Cotización pendiente' ? 'Pendiente de cotización' : 'Pendiente de diagnóstico') : 'Servicio técnico solicitado') }}
                    </td>
                    <td class="amount">
                        {{ $servicio->autorizacion === 'Rechazado' ? 'Sin cobro' : ($servicio->precio === null ? 'Pendiente' : '$ ' . number_format((float) $servicio->precio, 2)) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="total">
                        {{ $servicio->autorizacion === 'Rechazado' ? 'TOTAL' : ($servicio->saldo === null ? 'TOTAL PENDIENTE DE DIAGNÓSTICO' : 'SALDO PENDIENTE') }}
                    </td>
                    <td class="total">
                        {{ $servicio->autorizacion === 'Rechazado' ? 'Sin cobro' : ($servicio->saldo === null ? 'Pendiente' : '$ ' . number_format((float) $servicio->saldo, 2)) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="state">
            <tr>
                <td class="state-info">
                    <div class="section">6. ESTADO DEL SERVICIO</div>
                    <div class="content">{{ $servicio->estado_servicio }} · Autorización:
                        {{ $servicio->autorizacion }}</div>
                </td>
                <td class="stamp">SELLO DE LA EMPRESA</td>
            </tr>
        </table>

        <div class="footer">Gracias por confiar en PCS Solutions TI · Tu equipo en buenas manos</div>
    </main>
</body>

</html>
