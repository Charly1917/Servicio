
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->string('folio')->unique();

            // Datos del equipo
            $table->string('tipo_equipo');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serie')->nullable();
            $table->text('accesorios')->nullable();

            // Recepción
            $table->dateTime('fecha_recepcion');
            $table->string('estado_fisico')->default('Bueno');

            // Diagnóstico y seguimiento
            $table->text('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado_servicio')
                ->default('En diagnóstico');

            // Costos
            $table->decimal('precio', 10, 2)->default(0);
            $table->decimal('anticipo', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);

            // Autorización del cliente
            $table->string('autorizacion')
                ->default('Pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
