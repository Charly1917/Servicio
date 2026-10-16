<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CrearUsuarioTi extends Command
{
    protected $signature = 'ti:crear-usuario
        {--name= : Nombre del técnico}
        {--email= : Correo de acceso}
        {--password-env= : Nombre de una variable de entorno con la contraseña}';

    protected $description = 'Crea una cuenta de acceso para el panel interno de TI';

    public function handle(): int
    {
        $nombre = $this->option('name') ?? $this->ask('Nombre del técnico');
        $correo = mb_strtolower(trim((string) (
            $this->option('email') ?? $this->ask('Correo de acceso')
        )));

        if (! is_string($nombre) || trim($nombre) === '') {
            $this->error('El nombre del técnico es obligatorio.');

            return self::FAILURE;
        }

        if (! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->error('Ingresa un correo electrónico válido.');

            return self::FAILURE;
        }

        if (User::where('email', $correo)->exists()) {
            $this->error('Ya existe una cuenta con ese correo.');

            return self::FAILURE;
        }

        $nombreVariable = $this->option('password-env');
        if ($nombreVariable !== null && ! preg_match('/^[A-Z_][A-Z0-9_]*$/', $nombreVariable)) {
            $this->error('El nombre de la variable de entorno no es válido.');

            return self::FAILURE;
        }

        $contrasena = $nombreVariable !== null
            ? getenv($nombreVariable)
            : $this->secret('Contraseña (mínimo 8 caracteres)');

        if (! is_string($contrasena) || mb_strlen($contrasena) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        User::create([
            'name' => trim($nombre),
            'email' => $correo,
            'password' => $contrasena,
        ]);

        $this->info('Cuenta de TI creada correctamente.');

        return self::SUCCESS;
    }
}
