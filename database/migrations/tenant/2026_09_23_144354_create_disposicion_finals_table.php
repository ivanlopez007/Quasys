<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposicion_finals', function (Blueprint $table) {
            $table->id();
            $table->string('disposicion'); // Ej: 'Destrucción / Triturado', 'Archivo Histórico', 'Reciclaje'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposicion_finals');
    }
};