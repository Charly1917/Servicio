<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar servicio técnico</title>
    <style>
        :root {
            color-scheme: light;
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

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .page {
            width: min(100% - 32px, 1080px);
            margin: 40px auto;
        }

        .heading {
            margin-bottom: 24px;
        }

        .heading-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .eyebrow {
            color: #0f766e;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0;
            color: #0f172a;
            font-size: clamp(1.7rem, 4vw, 2.25rem);
        }

        .intro {
            margin: 0;
            color: #64748b;
        }

        .card {
            margin-top: 20px;
            padding: clamp(20px, 4vw, 32px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 12px 35px rgb(15 23 42 / 6%);
        }

        .section-title {
            margin: 0 0 20px;
            color: #0f172a;
            font-size: 1.15rem;
        }

        .section+.section {
            margin-top: 32px;
            padding-top: 28px;
            border-top: 1px solid #e2e8f0;
        }

        .fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field {
            min-width: 0;
        }

        .wide {
            grid-column: 1 / -1;
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .section-heading .section-title {
            margin: 0;
        }

        .number {
            display: grid;
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            place-items: center;
            border-radius: 50%;
            background: #ccfbf1;
            color: #0f766e;
            font-weight: 800;
        }

        label,
        legend {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: .9rem;
            font-weight: 650;
        }

        .required {
            color: #b91c1c;
        }

        input,
        select,
        textarea {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: #0f172a;
        }

        textarea {
            min-height: 84px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #0f766e;
            outline: 3px solid rgb(15 118 110 / 14%);
        }

        .help {
            margin: 6px 0 0;
            color: #64748b;
            font-size: .82rem;
        }

        .error {
            margin: 6px 0 0;
            color: #b91c1c;
            font-size: .85rem;
        }

        .alert {
            margin-bottom: 20px;
            padding: 14px 16px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            background: #fef2f2;
            color: #991b1b;
        }

        .alert ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }

        fieldset {
            min-width: 0;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .checks {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #334155;
            font-size: .92rem;
        }

        .check input {
            width: 17px;
            min-height: 17px;
            height: 17px;
            margin: 0;
            accent-color: #0f766e;
        }

        .check:has(input:checked) {
            border-color: #0f766e;
            background: #f0fdfa;
        }

        textarea:disabled {
            cursor: not-allowed;
            background: #f1f5f9;
            color: #64748b;
        }

        .empty-services {
            grid-column: 1 / -1;
            padding: 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
            text-align: center;
        }

        .note {
            margin: 18px 0 0;
            color: #64748b;
            font-size: .85rem;
        }

        .price-input {
            display: flex;
            align-items: center;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .price-input span {
            padding: 0 12px;
            color: #64748b;
        }

        .price-input input {
            border: 0;
            border-radius: 0;
        }

        .price-input input:focus {
            outline: 0;
        }

        .price-input:focus-within {
            border-color: #0f766e;
            outline: 3px solid rgb(15 118 110 / 14%);
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
        }

        .button {
            min-height: 46px;
            padding: 0 20px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #fff;
            color: #334155;
            font-weight: 750;
            cursor: pointer;
        }

        .button.primary {
            border-color: #0f766e;
            background: #0f766e;
            color: white;
        }

        .button.primary:hover {
            background: #115e59;
        }

        .button:hover {
            background: #f8fafc;
        }

        .footer {
            margin-top: 24px;
            color: #64748b;
            font-size: .82rem;
            text-align: center;
        }

        @media (max-width: 640px) {
            .page {
                width: min(100% - 20px, 1080px);
                margin: 24px auto;
            }

            .heading-row {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .fields,
            .checks {
                grid-template-columns: 1fr;
            }

            .wide {
                grid-column: auto;
            }

            .actions {
                flex-direction: column-reverse;
            }

            .actions .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <header class="heading">
            <div class="heading-row">
                <div>
                    <span class="eyebrow">Recepción de equipos</span>
                    <h1>Recepción de Servicio TI</h1>
                    <p class="intro">Registro de equipos y servicios técnicos.</p>
                </div>
                <div class="field">
                    <label for="fechaRecepcion">Fecha y hora de recepción</label>
                    <input id="fechaRecepcion" type="text" readonly aria-describedby="fecha-ayuda">
                    <p class="help" id="fecha-ayuda">Hora actual del sistema.</p>
                </div>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert" role="alert">
                <strong>Revisa los datos del formulario:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formServicio" method="POST" action="{{ route('ti.servicios.guardar') }}">
            @csrf

            <section class="card section" aria-labelledby="cliente-heading">
                <div class="section-heading">
                    <span class="number" aria-hidden="true">1</span>
                    <h2 class="section-title" id="cliente-heading">Datos del cliente</h2>
                </div>
                <div class="fields">
                    <div class="field wide">
                        <label for="nombre">Nombre completo <span class="required">*</span></label>
                        <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" maxlength="255"
                            autocomplete="name" required>
                        @error('nombre')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}"
                            maxlength="30" autocomplete="tel">
                        @error('telefono')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="correo">Correo electrónico</label>
                        <input id="correo" name="correo" type="email" value="{{ old('correo') }}" maxlength="255"
                            autocomplete="email">
                        @error('correo')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="card section" aria-labelledby="equipo-heading">
                <div class="section-heading">
                    <span class="number" aria-hidden="true">2</span>
                    <h2 class="section-title" id="equipo-heading">Datos del equipo</h2>
                </div>
                <div class="fields">
                    <div class="field">
                        <label for="tipo_equipo">Tipo de equipo <span class="required">*</span></label>
                        <select id="tipo_equipo" name="tipo_equipo" required>
                            <option value="">Seleccionar equipo</option>
                            <option value="Impresora" data-equipo="impresora" @selected(old('tipo_equipo') === 'Impresora')>Impresora
                            </option>
                            <option value="Laptop" data-equipo="laptop" @selected(old('tipo_equipo') === 'Laptop')>Laptop</option>
                            <option value="Computadora de escritorio" data-equipo="desktop"
                                @selected(old('tipo_equipo') === 'Computadora de escritorio')>Computadora de escritorio</option>
                            <option value="Monitor" data-equipo="monitor" @selected(old('tipo_equipo') === 'Monitor')>Monitor</option>
                            <option value="Servidor" data-equipo="servidor" @selected(old('tipo_equipo') === 'Servidor')>Servidor
                            </option>
                            <option value="Mini CPU" data-equipo="minicpu" @selected(old('tipo_equipo') === 'Mini CPU')>Mini CPU
                            </option>
                            <option value="Otro" data-equipo="otro" @selected(old('tipo_equipo') === 'Otro')>Otro</option>
                        </select>
                        @error('tipo_equipo')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="marca">Marca <span class="required">*</span></label>
                        <select id="marca" name="marca" required disabled>
                            <option value="">Primero selecciona el equipo</option>
                        </select>
                        @error('marca')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="modelo">Modelo</label>
                        <input id="modelo" name="modelo" type="text" value="{{ old('modelo') }}"
                            maxlength="150">
                        @error('modelo')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="serie">Número de serie</label>
                        <input id="serie" name="serie" type="text" value="{{ old('serie') }}"
                            maxlength="150">
                        @error('serie')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="estado_fisico">Estado físico <span class="required">*</span></label>
                        <select id="estado_fisico" name="estado_fisico" required>
                            @foreach (['Bueno', 'Regular', 'Dañado'] as $estado)
                                <option value="{{ $estado }}" @selected(old('estado_fisico', 'Bueno') === $estado)>{{ $estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado_fisico')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field wide">
                        <label for="accesorios">Accesorios entregados</label>
                        <textarea id="accesorios" name="accesorios" placeholder="Cargador, cable, mouse, teclado, etc.">{{ old('accesorios') }}</textarea>
                        <p class="help">Al marcar “Sin accesorios”, este campo se desactiva.</p>
                        @error('accesorios')
                            <p class="error">{{ $message }}</p>
                        @enderror
                        <label class="check" for="sin_accesorios">
                            <input id="sin_accesorios" name="sin_accesorios" type="checkbox" value="1"
                                @checked(old('sin_accesorios'))>
                            <span>Sin accesorios</span>
                        </label>
                    </div>
                </div>
                <p class="note">Selecciona el tipo de equipo para mostrar las marcas y servicios disponibles.</p>
            </section>

            <section class="card section" aria-labelledby="solicitud-heading">
                <div class="section-heading">
                    <span class="number" aria-hidden="true">3</span>
                    <h2 class="section-title" id="solicitud-heading">Servicios disponibles</h2>
                </div>
                <fieldset>
                    <legend>Selecciona uno o más servicios <span class="required">*</span></legend>
                    <div id="listaServicios" class="checks" aria-live="polite">
                        <div class="empty-services">Selecciona primero el tipo de equipo para mostrar los servicios
                            disponibles.</div>
                    </div>
                    <p class="help">Debes seleccionar al menos un servicio.</p>
                    @error('servicios_solicitados')
                        <p class="error">{{ $message }}</p>
                    @enderror
                    @error('servicios_solicitados.*')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </fieldset>
            </section>

            <section class="card section" aria-labelledby="falla-heading">
                <div class="section-heading">
                    <span class="number" aria-hidden="true">4</span>
                    <h2 class="section-title" id="falla-heading">Problema que reporta el cliente</h2>
                </div>
                <div class="field">
                    <label id="fallaLabel" for="falla_reportada">Falla o detalles de la solicitud</label>
                    <textarea id="falla_reportada" name="falla_reportada" maxlength="5000"
                        aria-describedby="fallaAyuda"
                        placeholder="Describe qué le ocurre al equipo según el cliente.">{{ old('falla_reportada') }}</textarea>
                    <p id="fallaAyuda" class="help">Anota los síntomas o cualquier detalle útil para el técnico.</p>
                    @error('falla_reportada')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section class="card section" aria-labelledby="pago-heading">
                <div class="section-heading">
                    <span class="number" aria-hidden="true">5</span>
                    <h2 class="section-title" id="pago-heading">Costos</h2>
                </div>
                <div class="fields">
                    <div class="field wide">
                        <label class="check" for="cotizacion_pendiente">
                            <input id="cotizacion_pendiente" name="cotizacion_pendiente" type="checkbox" value="1"
                                @checked(old('cotizacion_pendiente'))>
                            <span>Cotización pendiente: todavía no se ha informado un precio al cliente</span>
                        </label>
                        <p class="help">TI podrá registrar el precio después; el cliente lo aprobará desde su enlace.</p>
                    </div>
                    <div class="field">
                        <label for="precio">Costo total del servicio (MXN) <span id="precioRequerido"
                                class="required">*</span></label>
                        <div class="price-input">
                            <span aria-hidden="true">$</span>
                            <input id="precio" name="precio" type="number" value="{{ old('precio') }}"
                                min="0" step="0.01" inputmode="decimal" required>
                        </div>
                        @error('precio')
                            <p class="error">{{ $message }}</p>
                        @enderror
                        <p id="precioAyuda" class="help">El precio acordado al recibir el equipo se considera autorizado y el servicio pasa a reparación.</p>
                    </div>
                </div>
                <p class="help">No se registran anticipos en recepción. Si el cliente ya aceptó el precio aquí, no volverá a pedirse autorización.</p>
            </section>

            <div class="actions">
                <button class="button" type="reset">Limpiar</button>
                <button class="button" type="submit" name="accion" value="guardar">Guardar servicio</button>
                <button class="button primary" type="submit" name="accion" value="imprimir">Guardar e imprimir hoja</button>
            </div>
        </form>
        <footer class="footer">Sistema de recepción de servicio técnico</footer>
    </main>
    <script>
        const marcasPorEquipo = {
            impresora: ['HP', 'Brother', 'Epson', 'Canon', 'Ricoh', 'Xerox', 'Lexmark', 'Kyocera', 'Samsung', 'Pantum'],
            laptop: ['HP', 'Dell', 'Lenovo', 'ASUS', 'Acer', 'MSI', 'Apple', 'Samsung', 'Huawei', 'Microsoft'],
            desktop: ['Dell', 'HP', 'Lenovo', 'ASUS', 'Acer', 'MSI', 'Apple', 'Ensamblada / Genérica'],
            monitor: ['Samsung', 'LG', 'Dell', 'HP', 'ASUS', 'Acer', 'BenQ', 'AOC', 'ViewSonic', 'Lenovo'],
            servidor: ['Dell', 'HP', 'Lenovo', 'IBM', 'Supermicro', 'Fujitsu'],
            minicpu: ['Intel', 'ASUS', 'MSI', 'Gigabyte', 'ZOTAC', 'Minisforum', 'Beelink', 'Geekom', 'Lenovo', 'HP',
                'Dell'
            ],
            otro: ['Otra marca'],
        };

        const serviciosPorEquipo = {
            impresora: ['Diagnóstico', 'Limpieza interna', 'Mantenimiento preventivo', 'Mantenimiento correctivo',
                'Limpieza de cabezales', 'Alineación de cabezales', 'Cambio de cartucho / tóner',
                'Instalación de drivers', 'Configuración de impresión', 'Configuración de red / Wi-Fi',
                'Configuración de impresora en PC', 'Prueba de impresión', 'Reparación de hardware'
            ],
            laptop: ['Diagnóstico', 'Instalación de Windows', 'Instalación de Office', 'Instalación de aplicaciones',
                'Instalación de antivirus', 'Instalación de drivers', 'Formateo', 'Respaldo de información',
                'Recuperación de información', 'Limpieza interna', 'Mantenimiento preventivo',
                'Mantenimiento correctivo', 'Cambio de disco', 'Cambio de memoria RAM', 'Reparación de hardware',
                'Configuración de Windows', 'Configuración de usuario'
            ],
            desktop: ['Diagnóstico', 'Instalación de Windows', 'Instalación de Office', 'Instalación de aplicaciones',
                'Instalación de antivirus', 'Instalación de drivers', 'Formateo', 'Respaldo de información',
                'Recuperación de información', 'Limpieza interna', 'Mantenimiento preventivo',
                'Mantenimiento correctivo', 'Cambio de disco', 'Cambio de memoria RAM', 'Cambio de fuente de poder',
                'Reparación de hardware', 'Configuración de Windows'
            ],
            monitor: ['Diagnóstico', 'Limpieza', 'Prueba de funcionamiento', 'Configuración de pantalla',
                'Configuración de resolución', 'Revisión de cables', 'Reparación de hardware'
            ],
            servidor: ['Diagnóstico', 'Mantenimiento preventivo', 'Mantenimiento correctivo', 'Limpieza interna',
                'Instalación de Windows Server', 'Instalación de Linux', 'Configuración de servidor',
                'Configuración de red', 'Configuración de usuarios', 'Configuración de respaldos',
                'Respaldo de información', 'Recuperación de información', 'Instalación de software',
                'Actualización de sistema', 'Reparación de hardware'
            ],
            minicpu: ['Diagnóstico', 'Instalación de Windows', 'Instalación de Office', 'Instalación de aplicaciones',
                'Instalación de antivirus', 'Instalación de drivers', 'Formateo', 'Respaldo de información',
                'Recuperación de información', 'Limpieza interna', 'Mantenimiento preventivo',
                'Mantenimiento correctivo', 'Cambio de SSD', 'Cambio de memoria RAM', 'Configuración de Windows',
                'Configuración de red', 'Configuración de usuario', 'Reparación de hardware'
            ],
            otro: ['Diagnóstico', 'Mantenimiento preventivo', 'Mantenimiento correctivo', 'Instalación de software',
                'Configuración', 'Limpieza', 'Reparación'
            ],
        };

        const formServicio = document.getElementById('formServicio');
        const tipoEquipo = document.getElementById('tipo_equipo');
        const marca = document.getElementById('marca');
        const listaServicios = document.getElementById('listaServicios');
        const fechaRecepcion = document.getElementById('fechaRecepcion');
        const precio = document.getElementById('precio');
        const precioRequerido = document.getElementById('precioRequerido');
        const precioAyuda = document.getElementById('precioAyuda');
        const cotizacionPendiente = document.getElementById('cotizacion_pendiente');
        const fallaReportada = document.getElementById('falla_reportada');
        const fallaLabel = document.getElementById('fallaLabel');
        const fallaAyuda = document.getElementById('fallaAyuda');
        const accesorios = document.getElementById('accesorios');
        const sinAccesorios = document.getElementById('sin_accesorios');
        const marcaAnterior = @js(old('marca', ''));
        const serviciosAnteriores = @js((array) old('servicios_solicitados', []));

        function actualizarFechaHora() {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString('es-MX');
            const hora = ahora.toLocaleTimeString('es-MX');
            fechaRecepcion.value = `${fecha} ${hora}`;
        }

        function agregarOpcion(select, texto, valor = '') {
            const opcion = document.createElement('option');
            opcion.value = valor;
            opcion.textContent = texto;
            select.appendChild(opcion);
            return opcion;
        }

        function actualizarEquipo(restaurarValores = false) {
            const equipo = tipoEquipo.selectedOptions[0]?.dataset.equipo;
            const marcas = marcasPorEquipo[equipo] ?? [];
            const servicios = serviciosPorEquipo[equipo] ?? [];

            marca.replaceChildren();
            if (!equipo) {
                agregarOpcion(marca, 'Primero selecciona el equipo');
                marca.disabled = true;
            } else {
                agregarOpcion(marca, 'Seleccionar marca');
                marcas.forEach((nombre) => agregarOpcion(marca, nombre, nombre));
                marca.disabled = false;
                if (restaurarValores && marcas.includes(marcaAnterior)) {
                    marca.value = marcaAnterior;
                }
            }

            listaServicios.replaceChildren();
            actualizarEstadoPrecio();
            if (!equipo) {
                const mensaje = document.createElement('div');
                mensaje.className = 'empty-services';
                mensaje.textContent = 'Selecciona primero el tipo de equipo para mostrar los servicios disponibles.';
                listaServicios.appendChild(mensaje);
                return;
            }

            servicios.forEach((nombreServicio) => {
                const etiqueta = document.createElement('label');
                etiqueta.className = 'check';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'servicios_solicitados[]';
                checkbox.value = nombreServicio;
                checkbox.checked = restaurarValores && serviciosAnteriores.includes(nombreServicio);

                const texto = document.createElement('span');
                texto.textContent = nombreServicio;
                etiqueta.append(checkbox, texto);
                listaServicios.appendChild(etiqueta);
            });
        }

        function actualizarEstadoPrecio() {
            const checks = [...listaServicios.querySelectorAll('input[name="servicios_solicitados[]"]')];
            const diagnostico = checks.find((servicio) => servicio.value === 'Diagnóstico');
            const diagnosticoSeleccionado = diagnostico?.checked ?? false;
            const otroServicioSeleccionado = checks.some((servicio) =>
                servicio.checked && servicio.value !== 'Diagnóstico');

            if (diagnostico) {
                diagnostico.disabled = !diagnosticoSeleccionado && otroServicioSeleccionado;
            }
            checks.filter((servicio) => servicio.value !== 'Diagnóstico')
                .forEach((servicio) => {
                    servicio.disabled = diagnosticoSeleccionado;
                });

            cotizacionPendiente.disabled = diagnosticoSeleccionado;
            if (diagnosticoSeleccionado) {
                cotizacionPendiente.checked = false;
            }

            const precioPendiente = diagnosticoSeleccionado || cotizacionPendiente.checked;
            precio.disabled = precioPendiente;
            precio.required = !precioPendiente;
            precioRequerido.hidden = precioPendiente;
            precioAyuda.textContent = diagnosticoSeleccionado ?
                'El precio se determinará después del diagnóstico técnico.' :
                (cotizacionPendiente.checked
                    ? 'El precio se registrará después; el cliente deberá autorizarlo desde su enlace.'
                    : 'El precio acordado al recibir el equipo se considera autorizado y pasa a reparación.');
            fallaReportada.required = diagnosticoSeleccionado;
            fallaLabel.textContent = diagnosticoSeleccionado ?
                '¿Qué falla nota el cliente? (obligatorio)' :
                'Falla o detalles de la solicitud';
            fallaAyuda.textContent = diagnosticoSeleccionado ?
                'Describe los síntomas que reportó el cliente para que TI pueda iniciar el diagnóstico.' :
                'Anota los síntomas o cualquier detalle útil para el técnico.';
            if (precioPendiente) {
                precio.value = '';
            }
        }

        function actualizarAccesorios() {
            accesorios.disabled = sinAccesorios.checked;
            if (sinAccesorios.checked) {
                accesorios.value = '';
            }
        }

        actualizarFechaHora();
        window.setInterval(actualizarFechaHora, 1000);
        tipoEquipo.addEventListener('change', () => {
            actualizarEquipo();
            actualizarEstadoPrecio();
        });
        listaServicios.addEventListener('change', actualizarEstadoPrecio);
        cotizacionPendiente.addEventListener('change', actualizarEstadoPrecio);
        sinAccesorios.addEventListener('change', actualizarAccesorios);
        formServicio.addEventListener('reset', () => {
            window.setTimeout(() => {
                actualizarFechaHora();
                actualizarEquipo();
                cotizacionPendiente.checked = false;
                actualizarEstadoPrecio();
                actualizarAccesorios();
            }, 0);
        });

        actualizarEquipo(true);
        actualizarEstadoPrecio();
        actualizarAccesorios();
    </script>
</body>

</html>
