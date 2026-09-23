<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->integer('version_numero'); // Ej: 1, 2, 3
            $table->string('archivo_path'); // Ruta en Cloud R2 / Storage
            $table->text('cambios_realizados')->nullable();
            $table->foreignId('subido_por_id')->constrained('usuarios');
            $table->boolean('es_version_actual')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_versiones');
    }
};