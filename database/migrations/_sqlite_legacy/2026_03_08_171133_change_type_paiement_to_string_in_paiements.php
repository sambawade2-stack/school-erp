<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite: recreate paiements with string type_paiement (was enum)
        DB::statement('PRAGMA foreign_keys=OFF');
        DB::statement('DROP TABLE IF EXISTS paiements_old');
        // Drop indexes before renaming (SQLite keeps them on renamed table)
        DB::statement('DROP INDEX IF EXISTS paiements_etudiant_id_index');
        DB::statement('DROP INDEX IF EXISTS paiements_date_paiement_index');
        DB::statement('DROP INDEX IF EXISTS paiements_numero_recu_unique');
        DB::statement('ALTER TABLE paiements RENAME TO paiements_old');

        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->string('statut')->default('complet');
            $table->string('type_paiement')->default('scolarite');
            $table->date('date_paiement');
            $table->string('annee_scolaire');
            $table->string('trimestre')->nullable();
            $table->string('numero_recu')->unique();
            $table->text('remarque')->nullable();
            $table->timestamps();
            $table->index('etudiant_id');
            $table->index('date_paiement');
        });

        DB::statement('INSERT INTO paiements SELECT id,etudiant_id,montant,montant_total,statut,type_paiement,date_paiement,annee_scolaire,trimestre,numero_recu,remarque,created_at,updated_at FROM paiements_old');
        DB::statement('DROP TABLE paiements_old');
        DB::statement('PRAGMA foreign_keys=ON');
    }

    public function down(): void {}
};
