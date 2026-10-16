@extends('ti.layout')

@section('title', 'Servicios')

@section('content')
    <header class="topbar">
        <div>
            <span class="brand">PCS Solutions TI · Recepción y seguimiento</span>
            <h1>Servicios técnicos</h1>
        </div>
        <div class="nav-actions">
            <span class="muted">{{ auth()->user()->name }}</span>
            <a class="button primary" href="{{ route('ti.servicios.crear') }}">Registrar servicio</a>
            <form method="POST" action="{{ route('ti.salir') }}">@csrf<button class="button" type="submit">Salir</button></form>
        </div>
    </header>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <section class="card">
        <div class="section-heading">
            <div>
                <h2>Carpeta de servicios · {{ $anio }}</h2>
                <p class="muted">Las hojas se organizan por año de recepción y dentro de cada año por cliente.</p>
            </div>
        </div>
        <form method="GET" action="{{ route('ti.servicios.index') }}" class="filters">
            <div class="field">
                <label for="anio">Año</label>
                <select id="anio" name="anio">
                    @foreach ($anios as $opcionAnio)
                        <option value="{{ $opcionAnio }}" @selected((int) $anio === $opcionAnio)>{{ $opcionAnio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="buscar">Buscar por folio o nombre del cliente</label>
                <input id="buscar" name="buscar" type="search" value="{{ $busqueda }}"
                    placeholder="Folio del servicio o nombre del cliente">
            </div>
            <input type="hidden" name="grupo" value="{{ $grupo }}">
            <button class="button primary" type="submit">Buscar</button>
            @if ($busqueda !== '')
                <a class="button" href="{{ route('ti.servicios.index', ['anio' => $anio, 'grupo' => $grupo]) }}">Limpiar búsqueda</a>
            @endif
        </form>
        <nav class="service-groups" aria-label="Filtrar servicios por etapa">
            <a class="button {{ $grupo === 'todos' ? 'primary' : '' }}"
                href="{{ route('ti.servicios.index', ['anio' => $anio, 'buscar' => $busqueda, 'grupo' => 'todos']) }}"
                @if ($grupo === 'todos') aria-current="page" @endif>
                Todos <span class="group-count">{{ $conteosGrupos['todos'] }}</span>
            </a>
            @foreach ($grupos as $clave => $datosGrupo)
                <a class="button {{ $grupo === $clave ? 'primary' : '' }}"
                    href="{{ route('ti.servicios.index', ['anio' => $anio, 'buscar' => $busqueda, 'grupo' => $clave]) }}"
                    @if ($grupo === $clave) aria-current="page" @endif>
                    {{ $datosGrupo['nombre'] }} <span class="group-count">{{ $conteosGrupos[$clave] }}</span>
                </a>
            @endforeach
        </nav>
        <p class="muted">
            @if ($busqueda !== '')
                Resultados para “{{ $busqueda }}” en {{ $anio }}:
            @else
                @if ($grupo === 'todos')
                    Servicios recibidos en {{ $anio }}:
                @else
                    {{ $grupos[$grupo]['nombre'] }} en {{ $anio }}:
                @endif
            @endif
            {{ $servicios->total() }}
        </p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Folio</th><th>Cliente</th><th>Equipo</th><th>Estado</th><th>Fecha</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($servicios as $servicio)
                        <tr>
                            <td><strong>{{ $servicio->folio }}</strong></td>
                            <td>{{ $servicio->cliente->nombre }}<br><span class="muted">{{ $servicio->cliente->telefono ?: 'Sin teléfono' }}</span></td>
                            <td>{{ $servicio->tipo_equipo }} · {{ $servicio->marca }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'status-complete' => $servicio->estado_servicio === 'Entregado',
                                    'status-review' => in_array($servicio->estado_servicio, ['En diagnóstico', 'Cotización pendiente', 'En autorización'], true),
                                    'status-cancelled' => $servicio->estado_servicio === 'Cancelado',
                                ])>{{ $servicio->estado_servicio }}</span>
                                @if ($servicio->autorizacion === 'Rechazado')
                                    <br><span class="badge status-rejected">No autorizada</span>
                                @endif
                            </td>
                            <td>{{ $servicio->fecha_recepcion->format('d/m/Y') }}</td>
                            <td><a href="{{ route('ti.servicios.mostrar', $servicio) }}">Abrir seguimiento</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No hay servicios que coincidan con el año y la búsqueda seleccionados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="actions">{{ $servicios->links() }}</div>
    </section>
@endsection
