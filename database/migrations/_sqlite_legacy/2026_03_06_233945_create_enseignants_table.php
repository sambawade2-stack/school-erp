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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('email')->unique()->nullable();
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->string('specialite')->nullable();
            $table->string('photo')->nullable();
            $table->date('date_embauche')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
