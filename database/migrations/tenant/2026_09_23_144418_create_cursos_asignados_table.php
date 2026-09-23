<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos_asignados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado', 'reprobado'])->default('pendiente');
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->timestamp('fecha_completado')->nullable();
            $table->text('certificado_url')->nullable();
            $table->timestamps();

            $table->unique(['curso_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos_asignados');
    }
};