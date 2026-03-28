<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étend les colonnes `trimestre` des tables examens, devoirs, compositions, paiements
     * pour accepter S1/S2/S3 (semestres élémentaire) en plus de T1/T2/T3.
     *
     * SQLite ne supporte pas ALTER COLUMN — on recrée les tables via une migration de données.
     * MySQL : simple ALTER TABLE.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE examens      MODIFY trimestre VARCHAR(3) NOT NULL DEFAULT 'T1'");
            DB::statement("ALTER TABLE devoirs       MODIFY trimestre VARCHAR(3) NULL");
            DB::statement("ALTER TABLE compositions  MODIFY trimestre VARCHAR(3) NULL");
            DB::statement("ALTER TABLE paiements     MODIFY trimestre VARCHAR(3) NULL");
        }

        // SQLite : les ENUMs sont stockés en TEXT, le CHECK constraint bloque les valeurs.
        // On recrée chaque table en supprimant le CHECK constraint.
        if ($driver === 'sqlite') {
            $this->fixSqliteTable('examens',     'trimestre', 'TEXT NOT NULL DEFAULT \'T1\'');
            $this->fixSqliteTable('devoirs',      'trimestre', 'TEXT');
            $this->fixSqliteTable('compositions', 'trimestre', 'TEXT');
            $this->fixSqliteTable('paiements',    'trimestre', 'TEXT');
        }
    }

    private function fixSqliteTable(string $table, string $col, string $newType): void
    {
        // Récupérer la définition actuelle de la table
        $createSql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table])[0]->sql ?? '';

        if (!$createSql) return;

        // Remplacer la contrainte ENUM par un simple TEXT
        // Pattern: "trimestre" text check ("trimestre" in ('T1','T2','T3')) NOT NULL
        $newSql = preg_replace(
            '/("' . $col . '"|`' . $col . '`)\s+text\s+check\s*\([^)]+\)(\s+not null)?(\s+default\s+\'[^\']+\')?/i',
            '"' . $col . '" ' . $newType,
            $createSql
        );

        if ($newSql === $createSql) {
            // Try alternate pattern without quotes
            $newSql = preg_replace(
                '/\b' . $col . '\b\s+text\s+check\s*\([^)]+\)(\s+not null)?(\s+default\s+\'[^\']+\')?/i',
                '"' . $col . '" ' . $newType,
                $createSql
            );
        }

        if ($newSql === $createSql) return; // Pas de changement nécessaire

        // Renommer l'ancienne table
        DB::statement("ALTER TABLE \"{$table}\" RENAME TO \"{$table}_old\"");

        // Créer la nouvelle table
        $newTableSql = str_replace("CREATE TABLE \"{$table}\"", "CREATE TABLE \"{$table}\"",
            str_replace("CREATE TABLE \"{$table}_old\"", "CREATE TABLE \"{$table}\"", $newSql));
        DB::statement($newTableSql);

        // Copier les données
        $cols = collect(DB::select("PRAGMA table_info(\"{$table}_old\")"))->pluck('name')->map(fn($c) => '"'.$c.'"')->implode(', ');
        DB::statement("INSERT INTO \"{$table}\" SELECT {$cols} FROM \"{$table}_old\"");

        // Supprimer l'ancienne table
        DB::statement("DROP TABLE \"{$table}_old\"");
    }

    public function down(): void
    {
        // Pas de rollback nécessaire (extension de contrainte)
    }
};
