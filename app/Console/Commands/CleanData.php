<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanData extends Command
{
    protected $signature = 'data:clean {--force : Ignorer la confirmation}';
    protected $description = 'Nettoyer toutes les données de test (garder la structure)';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  ATTENTION: Cela va SUPPRIMER TOUTES LES DONNÉES!');
            if (!$this->confirm('Êtes-vous sûr de vouloir continuer?')) {
                $this->info('Annulé.');
                return Command::SUCCESS;
            }
        }

        try {
            $this->info('🧹 Nettoyage des données...');

            // Désactiver les contraintes étrangères
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Tables à vider (ordre inverse des dépendances)
            $tables = [
                'notes',
                'tranches_paiement',
                'paiements',
                'presences',
                'inscriptions',
                'etudiants',
                'emplois_du_temps',
                'examens',
                'devoirs',
                'compositions',
                'matieres',
                'enseignants',
                'classes',
                'annees_scolaires',
                'tarifs',
                'evaluation_types',
                'internes',
                'depenses',
                'sections',
                'etablissements',
                'users',
            ];

            foreach ($tables as $table) {
                try {
                    if ($this->tableExists($table)) {
                        DB::table($table)->truncate();
                        $this->line("  ✓ $table vidée");
                    }
                } catch (\Exception $e) {
                    $this->line("  ⚠️  $table : " . $e->getMessage());
                }
            }

            // Réactiver les contraintes
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('');
            $this->info('✅ Nettoyage complété!');
            $this->info('');
            $this->info('Prochaines étapes:');
            $this->info('1. php artisan setup:reset --force');
            $this->info('2. Accédez à http://schoolerp.test');
            $this->info('3. Créez votre compte admin');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function tableExists(string $table): bool
    {
        try {
            return DB::connection()->getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }
}
