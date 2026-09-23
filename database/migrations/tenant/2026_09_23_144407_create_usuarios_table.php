<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('password');
            
            // Llaves foráneas
            $table->foreignId('rol_id')->constrained('roles');
            $table->foreignId('localidad_id')->constrained('localidades');
            $table->foreignId('area_id')->constrained('areas');
            
            // Auto-referencia para flujo de jerarquías/aprobaciones
            $table->foreignId('jefe_inmediato_id')->nullable()->constrained('usuarios')->onDelete('set null');
            
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};