<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classe_matiere', function (Blueprint $table) {
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->primary(['classe_id', 'matiere_id']);
        });

        // Migrer les associations existantes (classe_id sur matieres)
        DB::table('matieres')
            ->whereNotNull('classe_id')
            ->get()
            ->each(function ($m) {
                DB::table('classe_matiere')->insertOrIgnore([
                    'classe_id'  => $m->classe_id,
                    'matiere_id' => $m->id,
                ]);
            });

        // Supprimer l'ancienne colonne
        Schema::table('matieres', function (Blueprint $table) {
            $table->dropForeign(['classe_id']);
            $table->dropColumn('classe_id');
        });
    }

    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
        });

        DB::table('classe_matiere')->get()->each(function ($row) {
            DB::table('matieres')->where('id', $row->matiere_id)
                ->update(['classe_id' => $row->classe_id]);
        });

        Schema::dropIfExists('classe_matiere');
    }
};
