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
        Schema::table('annees_scolaires', function (Blueprint $table) {
            $table->enum('trimestre_actuel', ['T1', 'T2', 'T3'])->default('T1')->after('bulletins_ouverts');
        });
    }

    public function down(): void
    {
        Schema::table('annees_scolaires', function (Blueprint $table) {
            $table->dropColumn('trimestre_actuel');
        });
    }
};
