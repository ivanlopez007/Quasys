<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodo_retencions', function (Blueprint $table) {
            $table->id();
            $table->string('periodo'); // Ej: '1 Año', '3 Años', 'Permanente', 'Hasta superación'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodo_retencions');
    }
};