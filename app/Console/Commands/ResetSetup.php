<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResetSetup extends Command
{
    protected $signature = 'setup:reset {--force : Ignorer la confirmation}';
    protected $description = 'Réinitialiser l\'installation pour recommencer depuis zéro';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  ATTENTION: Cela va supprimer TOUS les utilisateurs!');
            if (!$this->confirm('Êtes-vous sûr de vouloir continuer?')) {
                $this->info('Annulé.');
                return Command::SUCCESS;
            }
        }

        try {
            $this->info('🔄 Réinitialisation en cours...');

            // Supprimer tous les utilisateurs
            User::truncate();
            $this->info('✓ Utilisateurs supprimés');

            // Nettoyer le cache
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->info('✓ Cache nettoyé');

            $this->info('');
            $this->info('✅ Réinitialisation complétée!');
            $this->info('');
            $this->info('Prochaines étapes:');
            $this->info('1. Accédez à http://schoolerp.test');
            $this->info('2. Le wizard de configuration devrait s\'afficher');
            $this->info('3. Remplissez le formulaire pour créer l\'administrateur');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
