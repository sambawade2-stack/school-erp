<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->foreignId('evaluation_type_id')
                  ->nullable()
                  ->after('composition_id')
                  ->constrained('evaluation_types')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['evaluation_type_id']);
            $table->dropColumn('evaluation_type_id');
        });
    }
};
