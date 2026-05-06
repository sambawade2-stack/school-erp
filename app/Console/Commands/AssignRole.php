<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignRole extends Command
{
    protected $signature   = 'rbac:assign-role {--email= : Email de l\'utilisateur} {--role=admin : Slug du rôle à assigner}';
    protected $description = 'Assigne un rôle à un utilisateur (utile après déploiement initial du système RBAC)';

    public function handle(): int
    {
        $roleSlug = $this->option('role');
        $role     = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            $this->error("Rôle '{$roleSlug}' introuvable. Lancez d'abord : php artisan db:seed");
            return self::FAILURE;
        }

        // Mode ciblé : un utilisateur précis
        if ($email = $this->option('email')) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("Utilisateur '{$email}' introuvable.");
                return self::FAILURE;
            }
            $user->update(['role_id' => $role->id]);
            $this->info("Rôle '{$roleSlug}' assigné à {$email}.");
            return self::SUCCESS;
        }

        // Mode interactif : lister les utilisateurs sans rôle
        $sansRole = User::whereNull('role_id')->get();

        if ($sansRole->isEmpty()) {
            $this->info('Tous les utilisateurs ont déjà un rôle.');
            return self::SUCCESS;
        }

        $this->table(['ID', 'Nom', 'Email'], $sansRole->map(fn($u) => [$u->id, $u->name, $u->email]));

        if (!$this->confirm("Assigner le rôle '{$roleSlug}' à ces {$sansRole->count()} utilisateur(s) ?")) {
            $this->info('Annulé.');
            return self::SUCCESS;
        }

        User::whereNull('role_id')->update(['role_id' => $role->id]);
        $this->info("{$sansRole->count()} utilisateur(s) mis à jour.");

        return self::SUCCESS;
    }
}
