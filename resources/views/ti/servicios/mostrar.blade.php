@extends('ti.layout')

@section('title', 'Servicio '.$servicio->folio)

@section('content')
    <header class="topbar">
        <div>
            <span class="brand">Panel técnico</span>
            <h1>Servicio {{ $servicio->folio }}</h1>
            <span class="muted">{{ $servicio->cliente->nombre }} · {{ $servicio->tipo_equipo }} {{ $servicio->marca }} {{ $servicio->modelo }}</span>
        </div>
        <div class="nav-actions">
            <a class="button" href="{{ route('ti.servicios.index') }}">Volver a servicios</a>
            <a class="button primary" href="{{ route('ti.servicios.imprimir', $servicio) }}">Imprimir hoja de servicio</a>
        </div>
    </header>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert error-box">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif

    <section class="card">
        <h2>Enlace privado del cliente</h2>
        <p class="help">Este enlace muestra únicamente este servicio. Compártelo solo con el cliente.</p>
        <div class="link-box" id="portalUrl">{{ $urlCliente }}</div>
        <div class="actions">
            <button class="button" type="button" onclick="navigator.clipboard.writeText(document.getElementById('portalUrl').textContent.trim())">Copiar enlace</button>
            @if ($whatsappPortalUrl)
                <a class="button primary" href="{{ $whatsappPortalUrl }}" target="_blank" rel="noopener noreferrer">Enviar enlace por WhatsApp</a>
            @endif
            @if ($whatsappUrl)
                <a class="button primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">WhatsApp: pedir opinión</a>
            @elseif ($servicio->estado_servicio === 'Entregado')
                <span class="help">Configura GOOGLE_REVIEW_URL o STORE_REVIEW_URL en .env para habilitar el mensaje de opinión.</span>
            @endif
        </div>
        @if (! $whatsappPortalUrl)
            <p class="help">Agrega el teléfono del cliente para enviarle el enlace de seguimiento por WhatsApp.</p>
        @endif
        @if ($servicio->estado_servicio === 'Entregado' && ! $servicio->cliente->telefono)
            <p class="help">Agrega el teléfono del cliente para preparar el mensaje de WhatsApp.</p>
        @endif
        @if ($googleReviewConfigured || $storeReviewConfigured)
            <p class="help">El botón abre WhatsApp con un mensaje listo; TI debe revisar y pulsar Enviar.</p>
        @endif
    </section>

    <section class="card">
        <h2>Solicitud del cliente</h2>
        <p><strong>Servicios solicitados:</strong> {{ $servicio->observaciones ?: 'Sin detalle registrado.' }}</p>
        <p><strong>Falla que reporta:</strong> {{ $servicio->falla_reportada ?: 'El cliente no reportó una falla específica.' }}</p>
        @if ($servicio->observaciones && str_contains(mb_strtolower($servicio->observaciones), 'diagn'))
            <p class="help"><strong>Importante:</strong> considera revisar primero la falla descrita por el cliente antes de confirmar la cotización.</p>
        @endif
        <p><strong>Accesorios:</strong> {{ $servicio->accesorios ?: 'Sin accesorios' }}</p>
    </section>

    <section class="card">
        <h2>Actualizar seguimiento del servicio</h2>
        <form method="POST" action="{{ route('ti.servicios.actualizar', $servicio) }}">
            @csrf
            @method('PATCH')
            <div class="grid">
                <div class="field">
                    <label for="estado_servicio">Estado del servicio</label>
                    <select id="estado_servicio" name="estado_servicio" required>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}" @selected(old('estado_servicio', $servicio->estado_servicio) === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($servicio->autorizacion === 'Rechazado')
                    <div class="field wide">
                        <p class="help">
                            @if ($servicio->estado_servicio === 'Pendiente de recolección')
                                El cliente no autorizó la cotización. Solo puedes mantenerlo pendiente de recolección o marcarlo como entregado.
                            @else
                                El cliente no autorizó la cotización y el equipo ya fue entregado.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="field">
                        <label for="precio">Precio de cotización (MXN)</label>
                        <input id="precio" name="precio" type="number" min="0" step="0.01" value="{{ old('precio', $servicio->precio) }}">
                        <p class="help">Déjalo vacío mientras el precio siga pendiente.</p>
                    </div>
                    @if (in_array($servicio->estado_servicio, ['En diagnóstico', 'Cotización pendiente', 'En autorización'], true))
                        <div class="field wide">
                            <label for="diagnostico">Diagnóstico para el cliente</label>
                            <textarea id="diagnostico" name="diagnostico" maxlength="5000">{{ old('diagnostico', $servicio->diagnostico) }}</textarea>
                        </div>
                    @endif
                    <div class="field wide">
                        <label for="comentario">Nota para el historial (opcional)</label>
                        <textarea id="comentario" name="comentario" maxlength="2000">{{ old('comentario') }}</textarea>
                    </div>
                @endif
            </div>
            <p class="help">
                @if ($servicio->autorizacion === 'Aprobado')
                    El precio fue aceptado por el cliente. No se volverá a solicitar autorización durante el seguimiento.
                @elseif ($servicio->autorizacion === 'Rechazado')
                    El cliente rechazó la cotización. No se cobrará el servicio y el equipo quedó pendiente de recolección.
                @elseif ($servicio->estado_servicio === 'Pendiente de recolección')
                    El cliente rechazó la cotización. El equipo está pendiente de recolección y no se cobrará el servicio.
                @elseif ($servicio->estado_servicio === 'En diagnóstico')
                    Registra el diagnóstico técnico y la cotización para enviarla al cliente desde su enlace.
                @elseif ($servicio->estado_servicio === 'Cotización pendiente')
                    Registra el precio y guarda; se enviará al cliente para que lo autorice desde su enlace.
                @elseif ($servicio->estado_servicio === 'En autorización')
                    El cliente debe revisar la cotización y responder desde su enlace antes de iniciar la reparación.
                @else
                    Continúa actualizando el seguimiento. Una autorización ya registrada no se solicitará de nuevo.
                @endif
            </p>
            <div class="actions"><button class="button primary" type="submit">Guardar cambios</button></div>
        </form>
        <p>
            <strong>Estado:</strong>
            <span @class([
                'badge',
                'status-complete' => $servicio->estado_servicio === 'Entregado',
                'status-review' => in_array($servicio->estado_servicio, ['En diagnóstico', 'Cotización pendiente', 'En autorización'], true),
                'status-cancelled' => $servicio->estado_servicio === 'Cancelado',
            ])>{{ $servicio->estado_servicio }}</span>
            <strong>Autorización:</strong>
            <span @class([
                'badge',
                'status-approved' => $servicio->autorizacion === 'Aprobado',
                'status-pending' => $servicio->autorizacion === 'Pendiente',
                'status-rejected' => $servicio->autorizacion === 'Rechazado',
            ])>{{ $servicio->autorizacion }}</span>
        </p>
    </section>

    <section class="card">
        <h2>Historial del servicio</h2>
        <div class="timeline">
            @forelse ($servicio->historial as $evento)
                <div class="timeline-item">
                    <strong>{{ $evento->estado_nuevo }}</strong>
                    <span class="muted"> · {{ $evento->created_at->format('d/m/Y H:i') }}</span>
                    @if ($evento->comentario)<div>{{ $evento->comentario }}</div>@endif
                </div>
            @empty
                <p class="muted">Sin avances registrados.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <h2>Preguntas y mensajes</h2>
        <div class="thread">
            @forelse ($servicio->mensajes as $mensaje)
                <article class="message {{ $mensaje->remitente === 'cliente' ? 'client' : '' }}">
                    <strong>{{ $mensaje->remitente === 'cliente' ? 'Cliente' : 'TI · '.($mensaje->usuario->name ?? 'Técnico') }}</strong>
                    <span class="muted"> · {{ $mensaje->created_at->format('d/m/Y H:i') }}</span>
                    <p>{{ $mensaje->mensaje }}</p>
                </article>
            @empty
                <p class="muted">Todavía no hay mensajes.</p>
            @endforelse
        </div>
        <form method="POST" action="{{ route('ti.servicios.responder', $servicio) }}">
            @csrf
            <div class="field" style="margin-top: 16px">
                <label for="mensaje">Responder al cliente</label>
                <textarea id="mensaje" name="mensaje" maxlength="2000" required></textarea>
            </div>
            <div class="actions"><button class="button primary" type="submit">Enviar respuesta</button></div>
        </form>
    </section>
@endsection
