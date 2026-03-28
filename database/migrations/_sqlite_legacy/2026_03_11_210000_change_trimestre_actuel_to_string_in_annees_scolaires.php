<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change l'ENUM trimestre_actuel en VARCHAR pour supporter T1/T2/T3 ET S1/S2/S3.
     * SQLite ne supporte pas la modification d'ENUM, mais n'en valide pas les valeurs.
     * Cette migration gère MySQL/MariaDB explicitement.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE annees_scolaires MODIFY trimestre_actuel VARCHAR(3) NOT NULL DEFAULT 'T1'");
        }
        // SQLite : la colonne est TEXT en interne, aucune modification nécessaire.
        // PostgreSQL : idem, gestion par cast.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE annees_scolaires MODIFY trimestre_actuel ENUM('T1','T2','T3') NOT NULL DEFAULT 'T1'");
        }
    }
};
