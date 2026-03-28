<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE "compositions_new" (
                "id"               integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                "intitule"         varchar NOT NULL,
                "matiere_id"       integer NOT NULL,
                "classe_id"        integer NOT NULL,
                "date_composition" date NOT NULL,
                "note_max"         numeric(5,2) NOT NULL DEFAULT 20,
                "description"      text,
                "annee_scolaire"   varchar NOT NULL,
                "trimestre"        varchar CHECK ("trimestre" IN (\'T1\',\'T2\',\'T3\',\'S1\',\'S2\',\'S3\')),
                "created_at"       datetime,
                "updated_at"       datetime,
                FOREIGN KEY ("matiere_id") REFERENCES "matieres"("id") ON DELETE CASCADE,
                FOREIGN KEY ("classe_id")  REFERENCES "classes"("id")  ON DELETE CASCADE
            )
        ');

        DB::statement('INSERT INTO "compositions_new" SELECT * FROM "compositions"');
        DB::statement('DROP TABLE "compositions"');
        DB::statement('ALTER TABLE "compositions_new" RENAME TO "compositions"');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE "compositions_new" (
                "id"               integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                "intitule"         varchar NOT NULL,
                "matiere_id"       integer NOT NULL,
                "classe_id"        integer NOT NULL,
                "date_composition" date NOT NULL,
                "note_max"         numeric(5,2) NOT NULL DEFAULT 20,
                "description"      text,
                "annee_scolaire"   varchar NOT NULL,
                "trimestre"        varchar CHECK ("trimestre" IN (\'T1\',\'T2\',\'T3\')),
                "created_at"       datetime,
                "updated_at"       datetime,
                FOREIGN KEY ("matiere_id") REFERENCES "matieres"("id") ON DELETE CASCADE,
                FOREIGN KEY ("classe_id")  REFERENCES "classes"("id")  ON DELETE CASCADE
            )
        ');

        DB::statement('INSERT INTO "compositions_new" SELECT * FROM "compositions"');
        DB::statement('DROP TABLE "compositions"');
        DB::statement('ALTER TABLE "compositions_new" RENAME TO "compositions"');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
