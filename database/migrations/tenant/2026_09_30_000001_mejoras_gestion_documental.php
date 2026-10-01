<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- cambio_documentos ----------
        Schema::table('cambio_documentos', function (Blueprint $table) {
            // Sin esto el código de un documento NUEVO se perdía entre la solicitud y la aprobación.
            $table->string('codigo_documento', 100)->nullable()->after('documento_id')->index();
            // El periodo_retencion_id es la UNIDAD (Años/Días/Semanas); aquí va la CANTIDAD.
            $table->unsignedInteger('tiempo_retencion')->nullable()->after('periodo_retencion_id');
            $table->date('fecha_proxima_revision')->nullable()->after('tiempo_retencion');
        });

        // Las solicitudes de tipo "Eliminar" no llevan archivo.
        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->string('url_documento')->nullable()->change();
        });

        // Trazabilidad: no borrar solicitudes si se borra definitivamente al usuario.
        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->dropForeign(['solicitante_id']);
        });
        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->foreign('solicitante_id')->references('id')->on('users')->restrictOnDelete();
        });

        // ---------- documentos ----------
        Schema::table('documentos', function (Blueprint $table) {
            $table->unsignedInteger('tiempo_retencion')->nullable()->after('periodo_retencion_id');
            $table->date('fecha_proxima_revision')->nullable()->after('fecha_publicacion');
            $table->timestamp('fecha_baja')->nullable()->after('fecha_proxima_revision'); // cuando deja de ser vigente
            $table->text('motivo_baja')->nullable()->after('fecha_baja');

            // Evita duplicar la misma versión de un código (si ya tienes duplicados, límpialos antes).
            $table->unique(['codigo_documento', 'version']);
            $table->index(['codigo_documento', 'vigente']);
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('users')->restrictOnDelete();
        });

        // ---------- bitácora de auditoría ----------
        Schema::create('bitacora_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->nullOnDelete();
            $table->foreignId('cambio_documento_id')->nullable()->constrained('cambio_documentos')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 50)->index();
            $table->json('detalle')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_documentos');

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropUnique(['codigo_documento', 'version']);
            $table->dropIndex(['codigo_documento', 'vigente']);
            $table->dropColumn(['tiempo_retencion', 'fecha_proxima_revision', 'fecha_baja', 'motivo_baja']);
        });
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->dropForeign(['solicitante_id']);
            $table->dropIndex(['codigo_documento']);
            $table->dropColumn(['codigo_documento', 'tiempo_retencion', 'fecha_proxima_revision']);
        });
        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->foreign('solicitante_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
