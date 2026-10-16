<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento de servicios · PCS Solutions TI</title>
    <style>
        :root {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1e293b;
            background: #f1f5f9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
        }

        .page {
            width: min(100% - 28px, 900px);
            margin: 28px auto;
        }

        .header,
        .card {
            padding: clamp(20px, 4vw, 30px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 30px rgb(15 23 42 / 5%);
        }

        .header {
            background: linear-gradient(135deg, #0f766e, #134e4a);
            color: white;
        }

        .eyebrow {
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            opacity: .85;
        }

        h1 {
            margin: 8px 0;
            font-size: clamp(1.6rem, 5vw, 2.15rem);
        }

        h2 {
            margin: 0 0 17px;
            color: #0f172a;
            font-size: 1.12rem;
        }

        .header p {
            margin: 5px 0;
            opacity: .9;
        }

        .folio {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 11px;
            border: 1px solid rgb(255 255 255 / 45%);
            border-radius: 999px;
            font-weight: 750;
        }

        .card {
            margin-top: 16px;
        }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 999px;
            background: #ccfbf1;
            color: #115e59;
            font-weight: 800;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .label {
            display: block;
            margin-bottom: 4px;
            color: #64748b;
            font-size: .75rem;
            font-weight: 750;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .value {
            overflow-wrap: anywhere;
        }

        .service-picker {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .service-picker label {
            margin: 0;
            color: #334155;
            font-size: .88rem;
            font-weight: 750;
        }

        .service-picker select {
            flex: 1 1 280px;
            min-width: 0;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            color: #0f172a;
            font: inherit;
        }

        .service-hero {
            display: grid;
            grid-template-columns: 190px 1fr;
            align-items: center;
            gap: 24px;
        }

        .device-art {
            display: grid;
            min-height: 150px;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(145deg, #ecfeff, #f0fdfa);
            color: #0f766e;
        }

        .device-art svg {
            width: 142px;
            height: 112px;
        }

        .device-caption {
            margin-top: 6px;
            color: #64748b;
            font-size: .8rem;
        }

        .progress-track {
            display: flex;
            align-items: flex-start;
            margin: 23px 0 8px;
        }

        .progress-step {
            position: relative;
            display: grid;
            flex: 1 1 0;
            justify-items: center;
            min-width: 64px;
            min-height: 0;
            margin: 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .progress-step:focus-visible {
            outline: 3px solid #14b8a6;
            outline-offset: 3px;
        }

        .progress-step:not(:last-child)::after {
            position: absolute;
            z-index: 0;
            top: 14px;
            left: calc(50% + 15px);
            width: calc(100% - 30px);
            height: 3px;
            background: #e2e8f0;
            content: "";
        }

        .progress-step.done:not(:last-child)::after {
            background: #5eead4;
        }

        .step-dot {
            position: relative;
            z-index: 1;
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            background: white;
            font-size: .75rem;
            font-weight: 800;
        }

        .progress-step.done .step-dot {
            border-color: #0f766e;
            background: #0f766e;
            color: white;
        }

        .progress-step.current .step-dot {
            outline: 4px solid #99f6e4;
        }

        .progress-step[aria-pressed="true"] .step-dot {
            box-shadow: 0 0 0 3px #ccfbf1;
        }

        .step-name {
            max-width: 100px;
            margin-top: 8px;
            font-size: .72rem;
            line-height: 1.25;
            text-align: center;
        }

        .progress-step.current .step-name,
        .progress-step[aria-pressed="true"] .step-name {
            color: #0f766e;
            font-weight: 800;
        }

        .progress-detail {
            margin-top: 17px;
            padding: 15px;
            border: 1px solid #ccfbf1;
            border-radius: 10px;
            background: #f0fdfa;
        }

        .progress-detail p {
            margin: 5px 0 0;
            color: #475569;
            line-height: 1.5;
        }

        .timeline {
            display: grid;
            gap: 0;
        }

        .timeline-item {
            padding: 0 0 15px 17px;
            border-left: 2px solid #99f6e4;
        }

        .timeline-item:last-child {
            border-left-color: transparent;
        }

        .muted {
            color: #64748b;
        }

        .thread {
            display: grid;
            gap: 10px;
        }

        .message {
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
        }

        .message.ti {
            border-color: #99f6e4;
            background: #f0fdfa;
        }

        .message p {
            margin: 6px 0 0;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 700;
        }

        textarea {
            width: 100%;
            min-height: 90px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            resize: vertical;
        }

        button,
        .review-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            margin: 10px 8px 0 0;
            padding: 0 15px;
            border: 0;
            border-radius: 8px;
            background: #0f766e;
            color: white;
            font: inherit;
            font-weight: 750;
            text-decoration: none;
            cursor: pointer;
        }

        .button-secondary {
            background: #475569;
        }

        .alert {
            margin-bottom: 14px;
            padding: 12px 14px;
            border: 1px solid #a7f3d0;
            border-radius: 9px;
            background: #ecfdf5;
            color: #065f46;
        }

        .alert.error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .footer {
            margin: 22px 0;
            color: #64748b;
            font-size: .82rem;
            text-align: center;
        }

        .live-status {
            margin: 12px 0;
            color: #64748b;
            font-size: .82rem;
        }

        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .service-hero {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .device-art {
                min-height: 125px;
            }

            .progress-track {
                overflow-x: auto;
                padding: 5px 2px 9px;
            }

            .progress-step {
                flex: 0 0 78px;
            }

            .step-name {
                font-size: .68rem;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <header class="header">
            <span class="eyebrow">PCS Solutions TI · Portal de cliente</span>
            <h1>Hola, {{ $cliente->nombre }}</h1>
            <p>Consulta el avance de tus equipos y comunícate directamente con nuestro equipo técnico.</p>
            <span class="folio">{{ $servicios->count() }}
                {{ \Illuminate\Support\Str::plural('servicio', $servicios->count()) }} registrados</span>
        </header>

        @if (session('success'))
            <div class="alert" style="margin-top: 14px">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error" style="margin-top: 14px">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if ($servicios->isNotEmpty())
            <section class="card">
                <form class="service-picker" method="GET" action="{{ route('portal.servicio', $token) }}">
                    <label for="servicio">Elige el servicio que quieres consultar</label>
                    <select id="servicio" name="servicio" onchange="this.form.submit()">
                        @foreach ($servicios as $opcion)
                            <option value="{{ $opcion->id }}" @selected($servicio?->id === $opcion->id)>
                                {{ $opcion->folio }} · {{ $opcion->tipo_equipo }} {{ $opcion->marca }}
                                {{ $opcion->modelo }} · {{ $opcion->estado_servicio }}
                            </option>
                        @endforeach
                    </select>
                    <noscript><button type="submit">Ver servicio</button></noscript>
                </form>
            </section>
        @endif

        @if ($servicio)
            <p id="liveRefreshStatus" class="live-status" role="status">Los cambios se actualizan automáticamente cada 8 segundos.</p>
        @endif

        @if ($servicio)
            @php
                $pasos = [
                    'En diagnóstico',
                    'Cotización pendiente',
                    'En autorización',
                    'En reparación',
                    'Seguimiento final',
                    'Listo para entregar',
                    'Entregado',
                ];
                $pasoActual = array_search($servicio->estado_servicio, $pasos, true);
                $pasoActual = $pasoActual === false ? -1 : $pasoActual;
                $tipoEquipoVisual = mb_strtolower($servicio->tipo_equipo);
                $historialSeguimiento = $servicio->historial->map(fn($evento) => [
                    'estado' => $evento->estado_nuevo,
                    'fecha' => $evento->created_at->format('d/m/Y H:i'),
                    'comentario' => $evento->comentario,
                ])->values();
            @endphp
            <article id="servicio-{{ $servicio->id }}" data-current-stage="{{ $pasoActual }}"
                data-history='@json($historialSeguimiento)'>
                <header class="card">
                    <div class="service-hero">
                        <div class="device-art">
                            <svg viewBox="0 0 180 130" role="img"
                                aria-label="Ilustración de {{ $servicio->tipo_equipo }}">
                                @if (str_contains($tipoEquipoVisual, 'impresora'))
                                    <path
                                        d="M55 20h70v28H55zM45 48h90a12 12 0 0 1 12 12v36h-24v18H57V96H33V60a12 12 0 0 1 12-12Z"
                                        fill="white" stroke="currentColor" stroke-width="5" stroke-linejoin="round" />
                                    <path d="M58 83h64v29H58zM64 29h52v19M67 93h46M67 102h38" fill="none"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                    <circle cx="137" cy="65" r="3" fill="#14b8a6" />
                                @elseif (str_contains($tipoEquipoVisual, 'monitor') || str_contains($tipoEquipoVisual, 'pantalla'))
                                    <rect x="30" y="16" width="120" height="77" rx="8" fill="white"
                                        stroke="currentColor" stroke-width="5" />
                                    <path d="M90 94v17M65 114h50M40 29h100v53H40z" fill="none" stroke="currentColor"
                                        stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="90" cy="87" r="2.5" fill="#14b8a6" />
                                @elseif (str_contains($tipoEquipoVisual, 'servidor'))
                                    <rect x="52" y="9" width="76" height="112" rx="8" fill="white"
                                        stroke="currentColor" stroke-width="5" />
                                    <path d="M63 25h54v23H63zM63 56h54v23H63zM63 87h54v23H63z" fill="#ecfeff"
                                        stroke="currentColor" stroke-width="3" />
                                    <circle cx="72" cy="36" r="3" fill="#14b8a6" />
                                    <circle cx="72" cy="67" r="3" fill="#14b8a6" />
                                    <circle cx="72" cy="98" r="3" fill="#14b8a6" />
                                    <path d="M83 36h25M83 67h25M83 98h25" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" />
                                @elseif (str_contains($tipoEquipoVisual, 'escritorio') ||
                                        str_contains($tipoEquipoVisual, 'desktop') ||
                                        str_contains($tipoEquipoVisual, 'mini'))
                                    <rect x="19" y="17" width="112" height="76" rx="7" fill="white"
                                        stroke="currentColor" stroke-width="5" />
                                    <path d="M31 29h88v50H31zM75 94v15M55 112h40" fill="none" stroke="currentColor"
                                        stroke-width="4" stroke-linejoin="round" stroke-linecap="round" />
                                    <rect x="139" y="34" width="25" height="78" rx="5" fill="white"
                                        stroke="currentColor" stroke-width="4" />
                                    <circle cx="151.5" cy="48" r="3" fill="#14b8a6" />
                                    <path d="M146 62h11M146 70h11" stroke="currentColor" stroke-width="2" />
                                @elseif (str_contains($tipoEquipoVisual, 'laptop') || str_contains($tipoEquipoVisual, 'port'))
                                    <rect x="37" y="13" width="106" height="75" rx="7" fill="white"
                                        stroke="currentColor" stroke-width="5" />
                                    <path d="M47 24h86v53H47zM22 95h136l-10 15H32z" fill="none"
                                        stroke="currentColor" stroke-width="4" stroke-linejoin="round" />
                                    <path d="M73 99h34" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" />
                                    <circle cx="90" cy="82" r="2.5" fill="#14b8a6" />
                                @else
                                    <rect x="31" y="19" width="118" height="74" rx="8" fill="white"
                                        stroke="currentColor" stroke-width="5" />
                                    <path d="M43 31h94v50H43zM90 94v16M65 113h50" fill="none" stroke="currentColor"
                                        stroke-width="4" stroke-linecap="round" />
                                @endif
                            </svg>
                        </div>
                        <div>
                            <span class="eyebrow" style="color: #0f766e">Seguimiento del servicio</span>
                            <h2 style="margin: 8px 0">Folio {{ $servicio->folio }}</h2>
                            <p style="margin: 4px 0;font-size:1.08rem;font-weight:750">{{ $servicio->tipo_equipo }}
                                {{ $servicio->marca }} {{ $servicio->modelo }}</p>
                            <span class="muted">Recibido el {{ $servicio->fecha_recepcion->format('d/m/Y') }}</span>
                            <div class="device-caption">Vista ilustrativa del equipo recibido</div>
                        </div>
                    </div>
                </header>

                <section class="card">
                    <h2>Avance de tu servicio</h2>
                    <span class="status">{{ $servicio->estado_servicio }}</span>
                    @if ($servicio->estado_servicio === 'Pendiente de recolección')
                        <div class="progress-detail" role="status">
                            <strong>Tu equipo está pendiente de recolección</strong>
                            <p>No autorizaste la cotización. No se realizará la reparación ni se cobrará el servicio;
                                puedes pasar por tu equipo.</p>
                        </div>
                    @elseif ($servicio->estado_servicio === 'Cancelado')
                        <p>El servicio fue cancelado. Si tienes dudas, envíanos un mensaje en este portal.</p>
                    @else
                        <div class="progress-track" role="group" aria-label="Etapas del servicio">
                            @foreach ($pasos as $indice => $paso)
                                <button
                                    class="progress-step {{ $indice <= $pasoActual ? 'done' : '' }} {{ $indice === $pasoActual ? 'current' : '' }}"
                                    type="button" data-stage="{{ $indice }}"
                                    aria-pressed="{{ $indice === $pasoActual ? 'true' : 'false' }}">
                                    <span class="step-dot">{{ $indice < $pasoActual ? '✓' : $indice + 1 }}</span>
                                    <span class="step-name">{{ $paso }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div id="progressDetail" class="progress-detail" aria-live="polite">
                            <strong id="progressTitle">{{ $servicio->estado_servicio }}</strong>
                            <p id="progressDescription"></p>
                        </div>
                    @endif
                </section>

                <section class="card">
                    <h2>Tu equipo y cotización</h2>
                    <div class="grid">
                        <div><span class="label">Equipo</span><span class="value">{{ $servicio->tipo_equipo }} ·
                                {{ $servicio->marca }} {{ $servicio->modelo }}</span></div>
                        <div><span class="label">Número de serie</span><span
                                class="value">{{ $servicio->serie ?: 'No proporcionado' }}</span></div>
                        <div><span class="label">Servicios solicitados</span><span
                                class="value">{{ $servicio->observaciones ?: 'Pendiente de revisión' }}</span></div>
                        <div><span class="label">Falla que reporta el cliente</span><span
                                class="value">{{ $servicio->falla_reportada ?: 'No reportó una falla específica' }}</span>
                        </div>
                        <div><span class="label">Accesorios</span><span
                                class="value">{{ $servicio->accesorios ?: 'Sin accesorios' }}</span></div>
                        <div><span class="label">Diagnóstico técnico</span><span
                                class="value">{{ $servicio->diagnostico ?: 'Pendiente de revisión' }}</span></div>
                        <div><span class="label">Cotización</span><span
                                class="value">{{ $servicio->autorizacion === 'Rechazado' ? 'Sin cobro' : ($servicio->precio === null ? ($servicio->estado_servicio === 'Cotización pendiente' ? 'Pendiente de cotización' : 'Pendiente de diagnóstico') : '$' . number_format((float) $servicio->precio, 2) . ' MXN') }}</span>
                        </div>
                        <div><span class="label">Saldo</span><span
                                class="value">{{ $servicio->autorizacion === 'Rechazado' ? 'Sin cobro' : ($servicio->saldo === null ? 'Pendiente de cotización' : '$' . number_format((float) $servicio->saldo, 2) . ' MXN') }}</span>
                        </div>
                    </div>
                </section>

                @if ($servicio->estado_servicio === 'En autorización' && $servicio->autorizacion === 'Pendiente')
                    <section class="card">
                        <h2>Tu autorización es necesaria</h2>
                        <p>Revisa la cotización. La reparación no comenzará hasta que registres tu decisión.</p>
                        <strong>Autorización actual: {{ $servicio->autorizacion }}</strong>
                        <form method="POST"
                            action="{{ route('portal.autorizacion', ['token' => $token, 'servicio' => $servicio]) }}">
                            @csrf
                            <button type="submit" name="decision" value="Aprobado">Autorizar reparación</button>
                            <button class="button-secondary" type="submit" name="decision" value="Rechazado">No
                                autorizar</button>
                        </form>
                    </section>
                @elseif ($servicio->autorizacion !== 'Pendiente')
                    <section class="card">
                        <h2>Decisión de cotización</h2>
                        <p>
                            Tu decisión quedó registrada:
                            <strong>{{ $servicio->autorizacion }}</strong>.
                            @if ($servicio->autorizacion === 'Rechazado')
                                El equipo está pendiente de recolección. No se realizará la reparación ni se cobrará el
                                servicio; puedes pasar por tu equipo.
                            @endif
                        </p>
                    </section>
                @endif

                <section class="card">
                    <h2>Historial de avances</h2>
                    <div class="timeline">
                        @forelse ($servicio->historial as $evento)
                            <div class="timeline-item">
                                <strong>{{ $evento->estado_nuevo }}</strong>
                                <span class="muted"> · {{ $evento->created_at->format('d/m/Y H:i') }}</span>
                                @if ($evento->comentario)
                                    <div>{{ $evento->comentario }}</div>
                                @endif
                            </div>
                        @empty
                            <p class="muted">Aún no hay actualizaciones.</p>
                        @endforelse
                    </div>
                </section>

                <section class="card">
                    <h2>Preguntas y mensajes</h2>
                    @if ($whatsappBusinessUrl)
                        <p>Si lo prefieres, también puedes contactarnos directamente por WhatsApp.</p>
                        <a class="review-link" href="{{ $whatsappBusinessUrl }}" target="_blank"
                            rel="noopener noreferrer">Contactar por WhatsApp</a>
                    @endif
                    <div class="thread">
                        @forelse ($servicio->mensajes as $mensaje)
                            <article class="message {{ $mensaje->remitente === 'ti' ? 'ti' : '' }}">
                                <strong>{{ $mensaje->remitente === 'ti' ? 'PCS Solutions TI' : 'Tú' }}</strong>
                                <span class="muted"> · {{ $mensaje->created_at->format('d/m/Y H:i') }}</span>
                                <p>{{ $mensaje->mensaje }}</p>
                            </article>
                        @empty
                            <p class="muted">Todavía no hay mensajes. Puedes escribirnos si tienes alguna pregunta.
                            </p>
                        @endforelse
                    </div>
                    <form method="POST"
                        action="{{ route('portal.mensajes', ['token' => $token, 'servicio' => $servicio]) }}">
                        @csrf
                        <label for="mensaje">Escribe tu pregunta o comentario</label>
                        <textarea id="mensaje" name="mensaje" maxlength="2000" required>{{ old('mensaje') }}</textarea>
                        <button type="submit">Enviar mensaje</button>
                    </form>
                </section>

                @if ($servicio->estado_servicio === 'Entregado' && ($googleReviewUrl || $storeReviewUrl))
                    <section class="card">
                        <h2>¿Nos compartes tu opinión?</h2>
                        <p>Gracias por confiar en nosotros. Tu opinión nos ayuda a mejorar.</p>
                        @if ($googleReviewUrl)
                            <a class="review-link" href="{{ $googleReviewUrl }}" target="_blank"
                                rel="noopener noreferrer">Opinar en Google Maps</a>
                        @endif
                        @if ($storeReviewUrl)
                            <a class="review-link" href="{{ $storeReviewUrl }}" target="_blank"
                                rel="noopener noreferrer">Opinar en nuestra tienda</a>
                        @endif
                    </section>
                @endif
            </article>
        @elseif ($servicios->isEmpty())
            <section class="card">
                <p class="muted">No hay servicios registrados para este cliente.</p>
            </section>
        @endif

        <footer class="footer">Este enlace es privado y muestra únicamente los servicios asociados a tu cuenta. No lo
            compartas públicamente.</footer>
    </main>
    @if ($servicio)
        <script>
            const stageDescriptions = [
                'Nuestro equipo está revisando el equipo para identificar la falla.',
                'TI revisará el equipo y registrará una cotización para que puedas decidir.',
                'El diagnóstico y la cotización están listos. Esperamos tu decisión antes de iniciar la reparación.',
                'La reparación autorizada está en proceso.',
                'La reparación terminó y el equipo pasa por una revisión final.',
                'El equipo está listo para que lo recojas.',
                'El servicio fue completado y el equipo ya fue entregado.'
            ];
            function showStage(article, index) {
                const progressSteps = [...article.querySelectorAll('.progress-step')];
                const progressTitle = article.querySelector('#progressTitle');
                const progressDescription = article.querySelector('#progressDescription');
                const currentStage = Number(article.dataset.currentStage);
                const serviceHistory = JSON.parse(article.dataset.history || '[]');
                const step = progressSteps[index];
                if (!step || !progressTitle || !progressDescription) return;

                const label = step.querySelector('.step-name').textContent;
                const historyEntry = [...serviceHistory].reverse().find((event) => event.estado === label);
                progressTitle.textContent = label + (index === currentStage ? ' · Estado actual' : '');
                progressDescription.textContent = index > currentStage ?
                    'Esta etapa todavía no inicia; te mostraremos el avance cuando el equipo técnico actualice el servicio.' :
                    historyEntry ?
                    `${historyEntry.fecha}${historyEntry.comentario ? ' · ' + historyEntry.comentario : ''}` :
                    stageDescriptions[index];
                progressSteps.forEach((item, itemIndex) => {
                    item.setAttribute('aria-pressed', itemIndex === index ? 'true' : 'false');
                });
            }

            document.addEventListener('click', (event) => {
                const step = event.target.closest('.progress-step');
                const article = step?.closest('article[id^="servicio-"]');
                if (step && article) showStage(article, Number(step.dataset.stage));
            });

            const currentArticle = document.querySelector('article[id^="servicio-"]');
            if (currentArticle && Number(currentArticle.dataset.currentStage) >= 0) {
                showStage(currentArticle, Number(currentArticle.dataset.currentStage));
            }

            const status = document.getElementById('liveRefreshStatus');
            let checking = false;

            function saveFormState(container) {
                const fields = [...container.querySelectorAll('input[name], select[name], textarea[name]')];
                return {
                    fields: fields.map((field) => ({
                        name: field.name,
                        value: field.value,
                        checked: field.checked,
                    })),
                    activeIndex: fields.indexOf(document.activeElement),
                };
            }

            function restoreFormState(container, state) {
                const fields = [...container.querySelectorAll('input[name], select[name], textarea[name]')];
                fields.forEach((field, index) => {
                    const saved = state.fields[index];
                    if (!saved || saved.name !== field.name) return;

                    if (field instanceof HTMLInputElement && ['checkbox', 'radio'].includes(field.type)) {
                        field.checked = saved.checked;
                    } else {
                        field.value = saved.value;
                    }
                });

                const active = fields[state.activeIndex];
                if (active) active.focus({ preventScroll: true });
            }

            async function refreshService() {
                if (checking || document.hidden) return;
                checking = true;

                try {
                    const response = await fetch(window.location.href, {
                        cache: 'no-store',
                        credentials: 'same-origin',
                        headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    const html = await response.text();
                    const refreshedDocument = new DOMParser().parseFromString(html, 'text/html');
                    const current = document.querySelector('article[id^="servicio-"]');
                    const refreshed = current && refreshedDocument.getElementById(current.id);
                    if (!current || !refreshed) throw new Error('No se encontró el seguimiento actualizado.');

                    const currentPicker = document.querySelector('.service-picker select');
                    const refreshedPicker = refreshedDocument.querySelector('.service-picker select');
                    if (currentPicker && refreshedPicker) {
                        const selectedService = currentPicker.value;
                        currentPicker.innerHTML = refreshedPicker.innerHTML;
                        currentPicker.value = selectedService;
                    }

                    if (current.outerHTML !== refreshed.outerHTML) {
                        const formState = saveFormState(current);
                        current.replaceWith(refreshed);
                        restoreFormState(refreshed, formState);

                        if (Number(refreshed.dataset.currentStage) >= 0) {
                            showStage(refreshed, Number(refreshed.dataset.currentStage));
                        }
                    }
                    status.textContent = 'Los cambios se actualizan automáticamente cada 8 segundos.';
                } catch (error) {
                    status.textContent = 'No se pudo actualizar automáticamente. Verifica tu conexión.';
                } finally {
                    checking = false;
                }
            }

            window.setInterval(refreshService, 8000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refreshService();
            });
        </script>
    @endif
</body>

</html>
