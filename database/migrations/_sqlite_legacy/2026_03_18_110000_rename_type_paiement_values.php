<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // scolarite → mensualite
        DB::table('paiements')->where('type_paiement', 'scolarite')->update(['type_paiement' => 'mensualite']);
        DB::table('tarifs')->where('type_frais', 'scolarite')->update(['type_frais' => 'mensualite']);

        // tenue → tenues
        DB::table('paiements')->where('type_paiement', 'tenue')->update(['type_paiement' => 'tenues']);
        DB::table('tarifs')->where('type_frais', 'tenue')->update(['type_frais' => 'tenues']);

        // cantine / transport → autre
        DB::table('paiements')->whereIn('type_paiement', ['cantine', 'transport'])->update(['type_paiement' => 'autre']);
        DB::table('tarifs')->whereIn('type_frais', ['cantine', 'transport'])->update(['type_frais' => 'autre']);
    }

    public function down(): void
    {
        DB::table('paiements')->where('type_paiement', 'mensualite')->update(['type_paiement' => 'scolarite']);
        DB::table('tarifs')->where('type_frais', 'mensualite')->update(['type_frais' => 'scolarite']);
        DB::table('paiements')->where('type_paiement', 'tenues')->update(['type_paiement' => 'tenue']);
        DB::table('tarifs')->where('type_frais', 'tenues')->update(['type_frais' => 'tenue']);
    }
};
