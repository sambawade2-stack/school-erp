<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->string('chambre')->nullable();
            $table->date('date_entree');
            $table->date('date_sortie')->nullable();
            $table->string('annee_scolaire');
            $table->enum('statut', ['actif', 'sorti'])->default('actif');
            $table->text('remarque')->nullable();
            $table->timestamps();

            $table->index('etudiant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internes');
    }
};
