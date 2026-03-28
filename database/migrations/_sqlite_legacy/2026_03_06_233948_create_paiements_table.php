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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->enum('type_paiement', ['scolarite', 'cantine', 'transport', 'autre'])->default('scolarite');
            $table->date('date_paiement');
            $table->string('annee_scolaire');
            $table->enum('trimestre', ['T1', 'T2', 'T3'])->nullable();
            $table->string('numero_recu')->unique();
            $table->text('remarque')->nullable();
            $table->timestamps();

            $table->index('etudiant_id');
            $table->index('date_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
