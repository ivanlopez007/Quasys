<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_nivels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_id')->constrained('nivels')->onDelete('cascade');
            $table->string('nombre'); // Ej: 'Procedimiento Operativo', 'Instructivo de Trabajo'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_nivels');
    }
};