<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->nullable()->default(null)->change();
            $table->decimal('saldo', 10, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->default(0)->change();
            $table->decimal('saldo', 10, 2)->default(0)->change();
        });
    }
};
