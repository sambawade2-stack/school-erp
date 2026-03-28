<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Examen;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@school.local'],
            [
                'name'     => 'Administrateur',
                'email'    => 'admin@school.local',
                'password' => Hash::make('admin123'),
            ]
        );

        // Classes
        $classes = [
            ['nom' => '6eme A', 'niveau' => '6eme', 'capacite' => 35, 'annee_scolaire' => '2024-2025'],
            ['nom' => '6eme B', 'niveau' => '6eme', 'capacite' => 35, 'annee_scolaire' => '2024-2025'],
            ['nom' => '5eme A', 'niveau' => '5eme', 'capacite' => 33, 'annee_scolaire' => '2024-2025'],
            ['nom' => '5eme B', 'niveau' => '5eme', 'capacite' => 33, 'annee_scolaire' => '2024-2025'],
            ['nom' => '4eme A', 'niveau' => '4eme', 'capacite' => 32, 'annee_scolaire' => '2024-2025'],
            ['nom' => '3eme A', 'niveau' => '3eme', 'capacite' => 30, 'annee_scolaire' => '2024-2025'],
            ['nom' => '2nde A', 'niveau' => '2nde', 'capacite' => 28, 'annee_scolaire' => '2024-2025'],
            ['nom' => '1ere A', 'niveau' => '1ere', 'capacite' => 28, 'annee_scolaire' => '2024-2025'],
            ['nom' => 'Tle A',  'niveau' => 'Terminale', 'capacite' => 26, 'annee_scolaire' => '2024-2025'],
            ['nom' => 'Tle B',  'niveau' => 'Terminale', 'capacite' => 26, 'annee_scolaire' => '2024-2025'],
            ['nom' => 'CP',     'niveau' => 'Primaire',  'capacite' => 25, 'annee_scolaire' => '2024-2025'],
            ['nom' => 'CE1',    'niveau' => 'Primaire',  'capacite' => 25, 'annee_scolaire' => '2024-2025'],
        ];

        foreach ($classes as $c) {
            Classe::updateOrCreate(['nom' => $c['nom'], 'annee_scolaire' => $c['annee_scolaire']], $c);
        }

        // Enseignants
        $enseignants = [
            ['prenom' => 'Mme', 'nom' => 'Dubois',    'email' => 'dubois@school.local',    'specialite' => 'Mathematiques',    'telephone' => '0612345671', 'date_embauche' => '2018-09-01', 'statut' => 'actif'],
            ['prenom' => 'M.',  'nom' => 'Lambert',   'email' => 'lambert@school.local',   'specialite' => 'Francais',         'telephone' => '0612345672', 'date_embauche' => '2019-09-01', 'statut' => 'actif'],
            ['prenom' => 'Mlle','nom' => 'Richard',   'email' => 'richard@school.local',   'specialite' => 'Informatique',     'telephone' => '0612345673', 'date_embauche' => '2020-09-01', 'statut' => 'actif'],
            ['prenom' => 'M.',  'nom' => 'Moreau',    'email' => 'moreau@school.local',     'specialite' => 'Histoire',         'telephone' => '0612345674', 'date_embauche' => '2017-09-01', 'statut' => 'actif'],
            ['prenom' => 'Mme', 'nom' => 'Bernard',  'email' => 'bernard@school.local',   'specialite' => 'Sciences',         'telephone' => '0612345675', 'date_embauche' => '2021-09-01', 'statut' => 'actif'],
            ['prenom' => 'M.',  'nom' => 'Martin',   'email' => 'martin@school.local',    'specialite' => 'Anglais',          'telephone' => '0612345676', 'date_embauche' => '2016-09-01', 'statut' => 'actif'],
            ['prenom' => 'Mme', 'nom' => 'Simon',    'email' => 'simon@school.local',     'specialite' => 'Geographie',       'telephone' => '0612345677', 'date_embauche' => '2022-09-01', 'statut' => 'actif'],
        ];

        foreach ($enseignants as $e) {
            Enseignant::updateOrCreate(['email' => $e['email']], $e);
        }

        $ens = Enseignant::all()->keyBy('nom');
        $cls = Classe::all()->keyBy('nom');

        // Matières
        $matieres = [
            ['nom' => 'Mathematiques', 'code' => 'MATH', 'coefficient' => 3, 'enseignant_id' => $ens['Dubois']->id,  'classe_id' => $cls['5eme A']->id],
            ['nom' => 'Francais',      'code' => 'FR',   'coefficient' => 3, 'enseignant_id' => $ens['Lambert']->id, 'classe_id' => $cls['5eme A']->id],
            ['nom' => 'Informatique',  'code' => 'INFO', 'coefficient' => 2, 'enseignant_id' => $ens['Richard']->id, 'classe_id' => $cls['5eme A']->id],
            ['nom' => 'Histoire',      'code' => 'HIST', 'coefficient' => 2, 'enseignant_id' => $ens['Moreau']->id,  'classe_id' => $cls['5eme A']->id],
            ['nom' => 'Sciences',      'code' => 'SCI',  'coefficient' => 2, 'enseignant_id' => $ens['Bernard']->id, 'classe_id' => $cls['5eme A']->id],
            ['nom' => 'Anglais',       'code' => 'ANG',  'coefficient' => 2, 'enseignant_id' => $ens['Martin']->id,  'classe_id' => $cls['5eme A']->id],
        ];

        foreach ($matieres as $m) {
            Matiere::updateOrCreate(['nom' => $m['nom'], 'classe_id' => $m['classe_id']], $m);
        }

        // Etudiants de démonstration
        $prenomsM = ['Lucas', 'Adam', 'Mohamed', 'Youssef', 'Amine', 'Karim', 'Omar', 'Saad', 'Hamza', 'Bilal',
                     'Thomas', 'Hugo', 'Nathan', 'Arthur', 'Leo', 'Ethan', 'Rayan', 'Ilias', 'Younes', 'Zakaria'];
        $prenomsF = ['Camille', 'Sophie', 'Emma', 'Lea', 'Chloe', 'Manon', 'Inès', 'Sara', 'Fatima', 'Amina',
                     'Yasmine', 'Nour', 'Hana', 'Layla', 'Rania', 'Lina', 'Maya', 'Salma', 'Dina', 'Alice'];
        $noms = ['Martin', 'Dupont', 'Bernard', 'Durand', 'Lefevre', 'Simon', 'Laurent', 'Rousseau', 'Bouali',
                 'Khalil', 'Benali', 'Chaoui', 'Idriss', 'Saidi', 'Karimi', 'Mansouri', 'Alami', 'Berrada', 'Tahir', 'Mokhtar'];

        $classesList = Classe::all();
        $annee = now()->year;

        for ($i = 1; $i <= 100; $i++) {
            $isMasculin = rand(0, 1) === 1;
            $prenomsList = $isMasculin ? $prenomsM : $prenomsF;
            $prenom = $prenomsList[array_rand($prenomsList)];
            $nom    = $noms[array_rand($noms)];
            $classe = $classesList->random();

            Etudiant::create([
                'matricule'       => "ETU-{$annee}-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'prenom'          => $prenom,
                'nom'             => $nom,
                'date_naissance'  => now()->subYears(rand(10, 18))->subDays(rand(0, 365)),
                'sexe'            => $isMasculin ? 'masculin' : 'feminin',
                'telephone'       => '06' . rand(10000000, 99999999),
                'nom_parent'      => 'M. ' . $nom,
                'tel_parent'      => '06' . rand(10000000, 99999999),
                'classe_id'       => $classe->id,
                'date_inscription'=> now()->subMonths(rand(1, 24)),
                'statut'          => rand(0, 9) < 9 ? 'actif' : 'inactif',
            ]);
        }

        // Paiements
        $etudiants = Etudiant::all();
        $types = ['scolarite', 'cantine', 'transport'];

        for ($i = 1; $i <= 80; $i++) {
            $etudiant = $etudiants->random();
            Paiement::create([
                'etudiant_id'    => $etudiant->id,
                'montant'        => rand(200, 1500) * 10,
                'type_paiement'  => $types[array_rand($types)],
                'date_paiement'  => now()->subDays(rand(0, 90)),
                'annee_scolaire' => '2024-2025',
                'trimestre'      => ['T1', 'T2', 'T3'][array_rand(['T1', 'T2', 'T3'])],
                'numero_recu'    => 'REC-2025-' . str_pad($i, 5, '0', STR_PAD_LEFT),
            ]);
        }

        // Examens et notes pour 5eme A
        $classeA = $cls['5eme A'];
        $mat = Matiere::where('classe_id', $classeA->id)->first();
        $etudiantsA = Etudiant::where('classe_id', $classeA->id)->where('statut', 'actif')->get();

        if ($mat && $etudiantsA->count() > 0) {
            $examen = Examen::updateOrCreate(
                ['intitule' => 'Controle 1 - ' . $mat->nom, 'classe_id' => $classeA->id],
                [
                    'matiere_id'     => $mat->id,
                    'date_examen'    => now()->subDays(15),
                    'note_max'       => 20,
                    'annee_scolaire' => '2024-2025',
                    'trimestre'      => 'T1',
                ]
            );

            foreach ($etudiantsA as $etu) {
                Note::updateOrCreate(
                    ['examen_id' => $examen->id, 'etudiant_id' => $etu->id],
                    ['note' => rand(5, 20) - rand(0, 5) + round(rand(0, 100) / 100, 2)]
                );
            }

            // Presences
            for ($j = 0; $j < 5; $j++) {
                $date = now()->subDays($j + 1);
                foreach ($etudiantsA->take(10) as $etu) {
                    Presence::updateOrCreate(
                        ['etudiant_id' => $etu->id, 'date' => $date->format('Y-m-d')],
                        [
                            'classe_id' => $classeA->id,
                            'statut'    => ['present', 'present', 'present', 'absent', 'retard'][rand(0, 4)],
                        ]
                    );
                }
            }
        }

        $this->command->info('Base de donnees remplie avec succes !');
        $this->command->info('Connexion : admin@school.local / admin123');
    }
}
