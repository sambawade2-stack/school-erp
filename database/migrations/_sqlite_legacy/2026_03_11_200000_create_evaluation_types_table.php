<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                        // Devoir, Contrôle, Examen
            $table->string('slug')->unique();             // devoir, controle, examen
            $table->decimal('poids', 4, 2)->default(0.33); // 0.30 = 30%
            $table->string('couleur')->default('#3B82F6'); // badge color
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_types');
    }
};
