<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('ALTER TABLE annees_scolaires RENAME TO annees_scolaires_backup');

        DB::statement('
            CREATE TABLE "annees_scolaires" (
                "id" integer primary key autoincrement not null,
                "libelle" varchar not null,
                "date_debut" date not null,
                "date_fin" date not null,
                "statut" varchar check ("statut" in (\'en_cours\', \'fermee\')) not null default \'fermee\',
                "bulletins_ouverts" tinyint(1) not null default \'0\',
                "created_at" datetime,
                "updated_at" datetime,
                "trimestre_actuel" varchar check ("trimestre_actuel" in (\'T1\', \'T2\', \'T3\', \'S1\', \'S2\', \'S3\')) not null default \'T1\'
            )
        ');

        DB::statement('INSERT INTO annees_scolaires SELECT * FROM annees_scolaires_backup');
        DB::statement('DROP TABLE annees_scolaires_backup');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Not reversible
    }
};
