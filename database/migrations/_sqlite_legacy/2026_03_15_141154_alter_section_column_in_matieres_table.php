<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('CREATE TABLE matieres_backup AS SELECT * FROM matieres');
        DB::statement('DROP TABLE matieres');

        DB::statement('
            CREATE TABLE "matieres" (
                "id" integer primary key autoincrement not null,
                "nom" varchar not null,
                "code" varchar,
                "coefficient" numeric not null default \'1\',
                "enseignant_id" integer,
                "classe_id" integer,
                "created_at" datetime,
                "updated_at" datetime,
                "section" varchar not null default \'Générale\',
                foreign key("enseignant_id") references "enseignants"("id") on delete set null,
                foreign key("classe_id") references "classes"("id") on delete set null
            )
        ');

        DB::statement('INSERT INTO matieres SELECT * FROM matieres_backup');
        DB::statement('DROP TABLE matieres_backup');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        //
    }
};
