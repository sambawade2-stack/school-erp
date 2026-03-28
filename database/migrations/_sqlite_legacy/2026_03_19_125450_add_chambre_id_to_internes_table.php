<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('internes', function (Blueprint $table) {
            $table->foreignId('chambre_id')->nullable()->constrained('chambres')->nullOnDelete()->after('etudiant_id');
        });
    }

    public function down(): void
    {
        Schema::table('internes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Chambre::class);
            $table->dropColumn('chambre_id');
        });
    }
};
