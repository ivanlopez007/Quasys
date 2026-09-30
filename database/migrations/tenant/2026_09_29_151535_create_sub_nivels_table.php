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
    Schema::create('sub_nivels', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->foreignId('nivel_id')->constrained('nivels')->cascadeOnDelete();
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
public function down(): void
{
    Schema::dropIfExists('sub_nivels');
}
};
