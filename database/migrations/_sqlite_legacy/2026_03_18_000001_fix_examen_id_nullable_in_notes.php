<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite ne supporte pas ALTER COLUMN directement.
        // On recrée la table avec examen_id nullable.
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE notes_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                examen_id INTEGER NULL REFERENCES examens(id) ON DELETE CASCADE,
                etudiant_id INTEGER NOT NULL REFERENCES etudiants(id) ON DELETE CASCADE,
                type VARCHAR CHECK(type IN (\'examen\',\'devoir\',\'composition\')) NOT NULL DEFAULT \'examen\',
                devoir_id INTEGER NULL REFERENCES devoirs(id) ON DELETE CASCADE,
                composition_id INTEGER NULL REFERENCES compositions(id) ON DELETE CASCADE,
                evaluation_type_id INTEGER NULL,
                note DECIMAL(5,2) NOT NULL,
                commentaire TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ');

        DB::statement('INSERT INTO notes_new SELECT * FROM notes');
        DB::statement('DROP TABLE notes');
        DB::statement('ALTER TABLE notes_new RENAME TO notes');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Irréversible sans perte de données — ne rien faire
    }
};
