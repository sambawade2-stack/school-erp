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
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');          // ex: 2025-2026
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['en_cours', 'fermee'])->default('fermee');
            $table->boolean('bulletins_ouverts')->default(false); // réouverture bulletins
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annees_scolaires');
    }
};
