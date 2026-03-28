<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Dans la grille tarifaire, l'ancienne "mensualite" devient "premiere_mensualite"
        DB::table('tarifs')->where('type_frais', 'mensualite')->update(['type_frais' => 'premiere_mensualite']);
    }

    public function down(): void
    {
        DB::table('tarifs')->where('type_frais', 'premiere_mensualite')->update(['type_frais' => 'mensualite']);
    }
};
