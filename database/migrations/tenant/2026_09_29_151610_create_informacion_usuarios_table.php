<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('informacion_usuarios')) {
            Schema::create('informacion_usuarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
                $table->string('nombre');
                $table->string('apellidos');
                $table->string('rfc')->nullable();
                $table->string('curp')->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('informacion_usuarios');
    }
};