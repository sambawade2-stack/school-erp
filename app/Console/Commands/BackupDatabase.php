<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:create {--description=}';
    protected $description = 'Créer une sauvegarde de la base de données SQLite';

    public function __construct(private BackupService $backupService) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🔄 Création de la sauvegarde...');

        $result = $this->backupService->backup();

        if ($result['success']) {
            $this->info('✅ Succès!');
            $this->line("📁 Fichier: {$result['file']}");
            $this->line("📊 Taille: {$result['size_mb']} MB");
            return Command::SUCCESS;
        }

        $this->error('❌ Erreur: ' . $result['message']);
        return Command::FAILURE;
    }
}
