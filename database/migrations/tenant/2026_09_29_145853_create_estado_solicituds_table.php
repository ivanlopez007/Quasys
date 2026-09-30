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
    Schema::create('estado_solicituds', function (Blueprint $table) {
        $table->id();
        $table->string('estado_solicitud');
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
public function down(): void
{
    Schema::dropIfExists('estado_solicituds');
}
};
