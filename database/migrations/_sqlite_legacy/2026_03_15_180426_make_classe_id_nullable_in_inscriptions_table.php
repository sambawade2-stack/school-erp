<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so we recreate the table
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('CREATE TABLE inscriptions_tmp (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            etudiant_id INTEGER NOT NULL,
            classe_id INTEGER NULL,
            annee_scolaire VARCHAR NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            niveau VARCHAR NULL
        )');

        DB::statement('INSERT INTO inscriptions_tmp SELECT * FROM inscriptions');
        DB::statement('DROP TABLE inscriptions');
        DB::statement('ALTER TABLE inscriptions_tmp RENAME TO inscriptions');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('CREATE TABLE inscriptions_tmp (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            etudiant_id INTEGER NOT NULL,
            classe_id INTEGER NOT NULL,
            annee_scolaire VARCHAR NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            niveau VARCHAR NULL
        )');

        DB::statement('INSERT INTO inscriptions_tmp SELECT * FROM inscriptions WHERE classe_id IS NOT NULL');
        DB::statement('DROP TABLE inscriptions');
        DB::statement('ALTER TABLE inscriptions_tmp RENAME TO inscriptions');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
