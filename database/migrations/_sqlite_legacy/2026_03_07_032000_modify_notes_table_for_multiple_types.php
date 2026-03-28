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
        Schema::table('notes', function (Blueprint $table) {
            $table->enum('type', ['examen', 'devoir', 'composition'])->default('examen')->after('etudiant_id');
            $table->foreignId('devoir_id')->nullable()->constrained('devoirs')->cascadeOnDelete()->after('type');
            $table->foreignId('composition_id')->nullable()->constrained('compositions')->cascadeOnDelete()->after('devoir_id');

            // Rendre examen_id nullable
            $table->dropForeign(['examen_id']);
            $table->foreign('examen_id')->references('id')->on('examens')->cascadeOnDelete()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['devoir_id']);
            $table->dropForeign(['composition_id']);
            $table->dropColumn(['type', 'devoir_id', 'composition_id']);
        });
    }
};
