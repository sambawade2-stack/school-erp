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
        Schema::create('tranches_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paiement_id')->constrained('paiements')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->string('numero_recu')->unique();
            $table->text('remarque')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tranches_paiement');
    }
};
