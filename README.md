# Gestion Scolaire — School ERP

Application web de gestion d'établissement scolaire développée avec **Laravel 12**.
Elle couvre la scolarité, la pédagogie, la finance et l'internat à travers une interface unifiée, avec un contrôle d'accès fin par rôles et permissions (RBAC).

## Fonctionnalités

- **Élèves & inscriptions** — fiches élèves (avec photo), inscriptions par année scolaire, détection de doublons, régimes de paiement, export PDF/CSV.
- **Enseignants** — gestion des professeurs (avec photo), affectation aux matières, export PDF/CSV.
- **Classes & matières** — classes, sections, matières et association classe–matière.
- **Examens & notes** — examens, types d'évaluation, compositions, devoirs et saisie des notes.
- **Présences** — pointage des présences et rapports.
- **Paiements** — tarifs, paiements échelonnés (tranches), reçus PDF, reçus groupés, suivi des impayés, export PDF/CSV.
- **Dépenses** — suivi des dépenses et mouvements financiers avec bénéficiaire.
- **Internat** — gestion des chambres et des élèves internes.
- **Emploi du temps** — planification des cours.
- **Tableau de bord** — vue d'ensemble et indicateurs clés.
- **Rapports** — synthèses et documents exportables.
- **Administration** — paramètres de l'établissement (logo, informations), sauvegarde de la base, gestion des utilisateurs.
- **RBAC** — rôles (Administrateur, Professeur, Surveillant, Observateur en lecture seule) et permissions granulaires par module.

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend   | PHP 8.2+, Laravel 12 |
| Frontend  | Blade, Tailwind CSS 4, Alpine.js, Chart.js |
| Build     | Vite 7 |
| PDF       | barryvdh/laravel-dompdf |
| Excel/CSV | maatwebsite/excel |
| Images    | intervention/image |
| Base de données | MySQL (SQLite supporté pour le développement) |

## Prérequis

- PHP **8.2** ou supérieur
- Composer
- Node.js & npm
- MySQL (ou SQLite pour un usage local)

## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-depot>
cd school-erp

# 2. Installer les dépendances
composer install
npm install

# 3. Créer le fichier d'environnement
cp .env.example .env       # puis renseignez vos propres valeurs
php artisan key:generate

# 4. Configurer la base de données dans .env, puis migrer
php artisan migrate

# 5. (Optionnel) Initialiser les rôles, permissions et données de base
php artisan db:seed

# 6. Lien de stockage public
php artisan storage:link

# 7. Compiler les assets
npm run build
```

> Configurez vos paramètres de connexion à la base de données et autres réglages directement dans votre fichier `.env` local. Ce fichier ne doit jamais être versionné.

## Démarrage en développement

```bash
# Tout-en-un (serveur, file d'attente, logs, Vite)
composer dev

# Ou séparément
php artisan serve
npm run dev
```

L'application est ensuite accessible sur `http://localhost:8000`.

## Commandes utiles

```bash
# Assigner un rôle à un utilisateur
php artisan rbac:assign-role --email=user@example.com --role=admin

# Sauvegarder la base de données
php artisan backup:database

# Lancer les tests
php artisan test
```

## Structure du projet

```
app/
├── Console/Commands/   # Commandes artisan (sauvegarde, RBAC, etc.)
├── Http/Controllers/   # Contrôleurs par module métier
└── Models/             # Modèles Eloquent (Etudiant, Enseignant, Paiement, ...)
database/
├── migrations/         # Schéma de la base
└── seeders/            # Données initiales (rôles, permissions)
resources/views/        # Vues Blade
routes/web.php          # Routes web protégées par permissions
```

## Sécurité & contrôle d'accès

Toutes les routes applicatives sont protégées par authentification et par un middleware de permissions (`permission:<module>.<action>`).
Les accès sont déterminés par le rôle de l'utilisateur via le système RBAC.

## Internationalisation

L'application est en **français** par défaut (`APP_LOCALE=fr`).

## Licence

Ce projet s'appuie sur le framework Laravel, distribué sous licence MIT.
