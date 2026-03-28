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
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('prenom');
            $table->string('nom');
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['masculin', 'feminin']);
            $table->string('photo')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('nom_parent')->nullable();
            $table->string('tel_parent')->nullable();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->date('date_inscription');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();

            $table->index(['nom', 'prenom']);
            $table->index('classe_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};
