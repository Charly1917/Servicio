<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel TI') · PCS Solutions TI</title>
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

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        a {
            color: #0f766e;
        }

        .shell {
            width: min(100% - 32px, 1120px);
            margin: 28px auto;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .brand {
            color: #0f766e;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .topbar h1 {
            margin: 5px 0 0;
            color: #0f172a;
            font-size: 1.45rem;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            color: #334155;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button.primary {
            border-color: #0f766e;
            background: #0f766e;
            color: white;
        }

        .button.danger {
            border-color: #fecaca;
            color: #991b1b;
        }

        .card {
            margin-top: 16px;
            padding: clamp(18px, 3vw, 26px);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: white;
            box-shadow: 0 8px 26px rgb(15 23 42 / 5%);
        }

        .card h2 {
            margin: 0 0 16px;
            color: #0f172a;
            font-size: 1.08rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(150px, .6fr) minmax(220px, 1.5fr) auto auto;
            align-items: end;
            gap: 12px;
            margin-bottom: 16px;
        }

        .service-groups {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 18px 0;
        }

        .group-count {
            margin-left: 6px;
            opacity: .8;
        }

        .section-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .section-heading h2 {
            margin-bottom: 6px;
        }

        .section-heading p {
            margin: 0 0 14px;
        }

        .field {
            min-width: 0;
        }

        .wide {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: .88rem;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            min-height: 42px;
            padding: 9px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #0f766e;
            outline: 3px solid rgb(15 118 110 / 14%);
        }

        .muted,
        .help {
            color: #64748b;
        }

        .help {
            margin: 6px 0 0;
            font-size: .84rem;
        }

        .error {
            margin: 5px 0 0;
            color: #b91c1c;
            font-size: .85rem;
        }

        .alert {
            margin: 12px 0;
            padding: 12px 15px;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            background: #ecfdf5;
            color: #065f46;
        }

        .alert.error-box {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #64748b;
            font-size: .75rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            background: #ccfbf1;
            color: #115e59;
            font-size: .78rem;
            font-weight: 750;
        }

        .badge.status-complete {
            background: #dcfce7;
            color: #166534;
        }

        .badge.status-review {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.status-cancelled,
        .badge.status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .badge.status-pending {
            background: #e0f2fe;
            color: #075985;
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

        .message.client {
            border-color: #99f6e4;
            background: #f0fdfa;
        }

        .message p {
            margin: 6px 0 0;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        .timeline {
            display: grid;
            gap: 0;
        }

        .timeline-item {
            padding: 0 0 16px 18px;
            border-left: 2px solid #99f6e4;
        }

        .timeline-item:last-child {
            border-left-color: transparent;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .link-box {
            overflow-wrap: anywhere;
            padding: 12px;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            background: #f8fafc;
        }

        .footer {
            margin: 24px 0;
            color: #64748b;
            font-size: .82rem;
            text-align: center;
        }

        @media (max-width: 640px) {

            .grid,
            .filters {
                grid-template-columns: 1fr;
            }

            .wide {
                grid-column: auto;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <main class="shell">
        @yield('content')
        <p id="liveRefreshStatus" class="help" role="status">Los cambios se actualizan automáticamente cada 8 segundos.</p>
        <footer class="footer">PCS Solutions TI · Panel interno</footer>
    </main>
    <script>
        (() => {
            const panel = document.querySelector('.shell');
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

            function setStatus(message) {
                const status = document.getElementById('liveRefreshStatus');
                if (status) status.textContent = message;
            }

            async function refreshPanel() {
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
                    const refreshedPanel = refreshedDocument.querySelector('.shell');
                    if (!refreshedPanel) throw new Error('No se encontró el contenido actualizado del panel.');

                    if (panel.innerHTML !== refreshedPanel.innerHTML) {
                        const formState = saveFormState(panel);
                        panel.innerHTML = refreshedPanel.innerHTML;
                        restoreFormState(panel, formState);
                    }
                    setStatus('Los cambios se actualizan automáticamente cada 8 segundos.');
                } catch (error) {
                    setStatus('No se pudo actualizar automáticamente. Verifica tu conexión o vuelve a iniciar sesión.');
                } finally {
                    checking = false;
                }
            }

            window.setInterval(refreshPanel, 8000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refreshPanel();
            });
        })();
    </script>
</body>

</html>
