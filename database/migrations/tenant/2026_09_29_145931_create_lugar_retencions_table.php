<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 // 2026_01_01_000008_create_lugar_retencions_table.php
public function up(): void
{
    Schema::create('lugar_retencions', function (Blueprint $table) {
        $table->id();
        $table->string('lugar_retencion');
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
public function down(): void
{
    Schema::dropIfExists('lugar_retencions');
}
};
