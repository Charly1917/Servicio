<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso TI · PCS Solutions TI</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: grid;
            min-height: 100vh;
            margin: 0;
            place-items: center;
            padding: 20px;
            background: #f1f5f9;
            color: #1e293b;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        }

        .card {
            width: min(100%, 440px);
            padding: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            box-shadow: 0 12px 35px rgb(15 23 42 / 7%);
        }

        .brand {
            color: #0f766e;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0;
            color: #0f172a;
            font-size: 1.65rem;
        }

        p {
            color: #64748b;
        }

        label {
            display: block;
            margin: 16px 0 6px;
            font-weight: 700;
        }

        input {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
        }

        button {
            width: 100%;
            min-height: 46px;
            margin-top: 22px;
            border: 0;
            border-radius: 8px;
            background: #0f766e;
            color: white;
            font: inherit;
            font-weight: 750;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
            font-size: .88rem;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
        }

        .remember input {
            width: 16px;
            min-height: 16px;
        }
    </style>
</head>

<body>
    <main class="card">
        <span class="brand">PCS Solutions TI</span>
        <h1>Panel técnico</h1>
        <p>Inicia sesión para administrar servicios y dar seguimiento a tus clientes.</p>
        @if ($errors->any())
            <p class="error">No se pudo iniciar sesión. Revisa el correo y la contraseña.</p>
        @endif
        <form method="POST" action="{{ route('ti.ingresar') }}">
            @csrf
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username"
                required autofocus>
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="remember"><input type="checkbox" name="recordarme" value="1"> Mantener sesión
                iniciada</label>
            <button type="submit">Iniciar sesión</button>
        </form>
    </main>
</body>

</html>
