<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lugar_retencions', function (Blueprint $table) {
            $table->id();
            $table->string('lugar'); // Ej: 'Servidor Local', 'Archivero Físico A-1', 'Cloud R2'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugar_retencions');
    }
};