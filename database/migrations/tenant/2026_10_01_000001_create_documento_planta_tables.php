<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plantas que pueden ver cada versión publicada
        Schema::create('documento_planta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->foreignId('planta_id')->constrained('plantas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['documento_id', 'planta_id']);
        });

        // Plantas elegidas en la solicitud; se copian al documento al aprobarla
        Schema::create('cambio_documento_planta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cambio_documento_id')->constrained('cambio_documentos')->cascadeOnDelete();
            $table->foreignId('planta_id')->constrained('plantas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['cambio_documento_id', 'planta_id']);
        });

        // Lo que ya existía era visible para todos: se asigna a todas las plantas
        // para que nadie pierda acceso al aplicar esta migración.
        $plantas = DB::table('plantas')->whereNull('deleted_at')->pluck('id');
        $ahora = now();

        DB::table('documentos')->orderBy('id')->pluck('id')->chunk(500)->each(function ($ids) use ($plantas, $ahora) {
            DB::table('documento_planta')->insert($ids->crossJoin($plantas)->map(fn ($par) => [
                'documento_id' => $par[0], 'planta_id' => $par[1], 'created_at' => $ahora, 'updated_at' => $ahora,
            ])->all());
        });

        DB::table('cambio_documentos')->orderBy('id')->pluck('id')->chunk(500)->each(function ($ids) use ($plantas, $ahora) {
            DB::table('cambio_documento_planta')->insert($ids->crossJoin($plantas)->map(fn ($par) => [
                'cambio_documento_id' => $par[0], 'planta_id' => $par[1], 'created_at' => $ahora, 'updated_at' => $ahora,
            ])->all());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cambio_documento_planta');
        Schema::dropIfExists('documento_planta');
    }
};
