<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir les anciennes valeurs 'terminal' en 'lycee'
        DB::table('inscriptions')
            ->where('niveau', 'terminal')
            ->update(['niveau' => 'lycee']);
    }

    public function down(): void
    {
        DB::table('inscriptions')
            ->where('niveau', 'lycee')
            ->update(['niveau' => 'terminal']);
    }
};
