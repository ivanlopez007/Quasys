<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 2026_01_01_000020_create_cambio_documentos_table.php
    public function up(): void
    {
        Schema::create('cambio_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id')->nullable(); // FK cruzada agregada más adelante
            $table->string('nombre_documento');
            $table->integer('version');
            $table->string('url_documento');

            $table->foreignId('nivel_id')->nullable()->constrained('nivels')->nullOnDelete();
            $table->foreignId('subnivel_id')->nullable()->constrained('sub_nivels')->nullOnDelete();
            $table->foreignId('localidad_id')->nullable()->constrained('localidads')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('lugar_retencion_id')->nullable()->constrained('lugar_retencions')->nullOnDelete();
            $table->foreignId('periodo_retencion_id')->nullable()->constrained('periodo_retencions')->nullOnDelete();
            $table->foreignId('disposicion_final_id')->nullable()->constrained('disposicion_finals')->nullOnDelete();
            $table->foreignId('tipo_solicitud_id')->nullable()->constrained('tipo_solicituds')->nullOnDelete();

            $table->foreignId('solicitante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('aprobar_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('estado_id')->constrained('estado_solicituds');

            $table->text('motivo_cambio')->nullable();
            $table->text('descripcion_cambios')->nullable();
            $table->text('comentario_aprobador')->nullable();

            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cambio_documentos');
    }
};
