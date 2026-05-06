<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────────
        $adminPassword = env('ADMIN_SEED_PASSWORD');
        if (!$adminPassword) {
            $adminPassword = bin2hex(random_bytes(12));
            $this->command->warn("⚠️  ADMIN_SEED_PASSWORD non défini dans .env");
            $this->command->warn("   Mot de passe généré : {$adminPassword}");
        }

        // ── Rôles ──────────────────────────────────────────────────────────────
        $rolesData = [
            ['slug' => 'admin',       'nom' => 'Administrateur'],
            ['slug' => 'professeur',  'nom' => 'Professeur'],
            ['slug' => 'surveillant', 'nom' => 'Surveillant'],
            ['slug' => 'observateur', 'nom' => 'Observateur (lecture seule)'],
        ];

        foreach ($rolesData as $r) {
            Role::updateOrCreate(['slug' => $r['slug']], $r);
        }

        // ── Permissions ────────────────────────────────────────────────────────
        $allPermissions = [
            // Tableau de bord
            'dashboard.view'        => 'Voir le tableau de bord',

            // Étudiants
            'etudiants.view'        => 'Voir les étudiants',
            'etudiants.create'      => 'Ajouter un étudiant',
            'etudiants.edit'        => 'Modifier un étudiant',
            'etudiants.delete'      => 'Supprimer un étudiant',

            // Enseignants
            'enseignants.view'      => 'Voir les enseignants',
            'enseignants.create'    => 'Ajouter un enseignant',
            'enseignants.edit'      => 'Modifier un enseignant',
            'enseignants.delete'    => 'Supprimer un enseignant',

            // Classes
            'classes.view'          => 'Voir les classes',
            'classes.create'        => 'Créer une classe',
            'classes.edit'          => 'Modifier une classe',
            'classes.delete'        => 'Supprimer une classe',

            // Matières
            'matieres.view'         => 'Voir les matières',
            'matieres.create'       => 'Créer une matière',
            'matieres.edit'         => 'Modifier une matière',
            'matieres.delete'       => 'Supprimer une matière',

            // Notes
            'notes.view'            => 'Voir les notes',
            'notes.create'          => 'Saisir / modifier des notes',

            // Présences
            'presences.view'        => 'Voir les présences',
            'presences.create'      => 'Gérer les présences',

            // Examens / Devoirs / Compositions
            'examens.view'          => 'Voir les examens',
            'examens.create'        => 'Créer un examen',
            'examens.edit'          => 'Modifier un examen',
            'examens.delete'        => 'Supprimer un examen',
            'devoirs.view'          => 'Voir les devoirs',
            'devoirs.create'        => 'Créer un devoir',
            'devoirs.edit'          => 'Modifier un devoir',
            'devoirs.delete'        => 'Supprimer un devoir',
            'compositions.view'     => 'Voir les compositions',
            'compositions.create'   => 'Créer une composition',
            'compositions.edit'     => 'Modifier une composition',
            'compositions.delete'   => 'Supprimer une composition',

            // Paiements
            'paiements.view'        => 'Voir les paiements',
            'paiements.create'      => 'Enregistrer un paiement',
            'paiements.edit'        => 'Modifier un paiement',
            'paiements.delete'      => 'Supprimer un paiement',

            // Tableau de bord financier
            'finances.view'         => 'Voir les données financières du tableau de bord',

            // Rapports
            'rapports.view'         => 'Voir les rapports',

            // Internes / Chambres
            'internes.view'         => 'Voir les internes',
            'internes.create'       => 'Ajouter un interne',
            'internes.edit'         => 'Modifier un interne',
            'internes.delete'       => 'Supprimer un interne',

            // Dépenses
            'depenses.view'         => 'Voir les dépenses',
            'depenses.create'       => 'Enregistrer une dépense',
            'depenses.edit'         => 'Modifier une dépense',
            'depenses.delete'       => 'Supprimer une dépense',

            // Administration
            'admin.view'            => 'Voir le panneau administrateur',
            'admin.edit'            => 'Modifier les paramètres établissement',
            'parametres.view'       => 'Voir les paramètres système',
            'parametres.edit'       => 'Modifier les paramètres système',
            'backups.view'          => 'Voir les sauvegardes',
            'backups.create'        => 'Créer / restaurer des sauvegardes',
            'rbac.manage'           => 'Gérer les rôles et utilisateurs',
        ];

        foreach ($allPermissions as $slug => $nom) {
            Permission::updateOrCreate(['slug' => $slug], ['nom' => $nom]);
        }

        $permissions = Permission::all()->keyBy('slug');

        // ── Permissions par rôle ───────────────────────────────────────────────
        $rolePermissions = [
            'professeur' => [
                'dashboard.view',
                'etudiants.view',
                'enseignants.view',
                'classes.view',
                'matieres.view',
                'notes.view',    'notes.create',
                'presences.view', 'presences.create',
                'examens.view',  'examens.create',  'examens.edit',  'examens.delete',
                'devoirs.view',  'devoirs.create',  'devoirs.edit',  'devoirs.delete',
                'compositions.view', 'compositions.create', 'compositions.edit', 'compositions.delete',
                'rapports.view',
            ],
            'surveillant' => [
                'dashboard.view',
                'etudiants.view',
                'classes.view',
                'presences.view', 'presences.create',
                'rapports.view',
            ],
            'observateur' => [
                'dashboard.view',
                'finances.view',
                'depenses.view',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $slugs) {
            $role    = Role::where('slug', $roleSlug)->first();
            $permIds = collect($slugs)->map(fn($s) => $permissions[$s]->id)->all();
            $role->permissions()->sync($permIds);
        }

        // ── Utilisateur admin ──────────────────────────────────────────────────
        $adminRole = Role::where('slug', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@school.local'],
            [
                'name'     => 'Administrateur',
                'password' => Hash::make($adminPassword),
                'role_id'  => $adminRole->id,
            ]
        );

        $this->command->info('Base de données initialisée avec succès !');
        $this->command->info('Connexion : admin@school.local');
    }
}
