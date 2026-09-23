<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aprobaciones_pasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_cambio_id')->constrained('solicitudes_cambio')->onDelete('cascade');
            $table->foreignId('aprobador_id')->constrained('usuarios');
            
            $table->integer('orden'); // Paso 1, Paso 2 en el workflow
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('comentarios')->nullable();
            $table->timestamp('fecha_revision')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aprobaciones_pasos');
    }
};