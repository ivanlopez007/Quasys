<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('documentos') && !Schema::hasColumn('documentos', 'activo')) {
            Schema::table('documentos', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('cambio_documento_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('documentos') && Schema::hasColumn('documentos', 'activo')) {
            Schema::table('documentos', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }
    }
};
