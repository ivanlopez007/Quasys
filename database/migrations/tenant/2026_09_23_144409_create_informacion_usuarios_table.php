<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informacion_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->string('numero_empleado')->nullable();
            $table->string('puesto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('foto_perfil')->nullable();
            $table->text('firma_digital')->nullable(); // Guardado de firma en Base64/Ruta
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informacion_usuarios');
    }
};