<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesos_asignados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('sub_menu_id')->constrained('sub_menus')->onDelete('cascade');
            $table->boolean('puede_ver')->default(true);
            $table->boolean('puede_editar')->default(false);
            $table->boolean('puede_eliminar')->default(false);
            $table->timestamps();

            $table->unique(['rol_id', 'sub_menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesos_asignados');
    }
};