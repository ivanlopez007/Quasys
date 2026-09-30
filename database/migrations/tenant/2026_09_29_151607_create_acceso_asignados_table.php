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
        Schema::create('acceso_asignados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('rols')->cascadeOnDelete();
            $table->foreignId('sub_menu_id')->constrained('sub_menus')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('acceso_asignados');
    }
};
