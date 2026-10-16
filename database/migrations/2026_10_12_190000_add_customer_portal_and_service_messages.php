<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->char('portal_token_hash', 64)->nullable()->unique();
            $table->text('portal_token')->nullable();
        });

        Schema::create('mensajes_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remitente', 20);
            $table->text('mensaje');
            $table->timestamps();

            $table->index(['servicio_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_servicio');

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['portal_token_hash']);
            $table->dropColumn(['portal_token_hash', 'portal_token']);
        });
    }
};
