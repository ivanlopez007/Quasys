<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_documento');
            $table->string('nombre_documento');
            $table->integer('version')->default(1);
            $table->string('url_documento');

            $table->foreignId('nivel_id')->nullable()->constrained('nivels')->nullOnDelete();
            $table->foreignId('subnivel_id')->nullable()->constrained('sub_nivels')->nullOnDelete();
            $table->foreignId('localidad_id')->nullable()->constrained('localidads')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('lugar_retencion_id')->nullable()->constrained('lugar_retencions')->nullOnDelete();
            $table->foreignId('periodo_retencion_id')->nullable()->constrained('periodo_retencions')->nullOnDelete();
            $table->foreignId('disposicion_final_id')->nullable()->constrained('disposicion_finals')->nullOnDelete();

            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('aprobar_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cambio_documento_id')->nullable()->constrained('cambio_documentos')->nullOnDelete();

            $table->boolean('vigente')->default(true);
            $table->timestamp('fecha_publicacion')->useCurrent();
            $table->timestamps();
        });

        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->foreign('documento_id')->references('id')->on('documentos')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('cambio_documentos', function (Blueprint $table) {
            $table->dropForeign(['documento_id']);
        });
        Schema::dropIfExists('documentos');
    }
};
