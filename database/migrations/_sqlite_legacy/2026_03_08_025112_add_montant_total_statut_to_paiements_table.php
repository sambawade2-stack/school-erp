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
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('montant_total', 10, 2)->nullable()->after('montant');
            $table->enum('statut', ['complet', 'partiel', 'non_paye'])->default('complet')->after('montant_total');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['montant_total', 'statut']);
        });
    }
};
