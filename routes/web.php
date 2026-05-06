<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CompositionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevoirController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\EvaluationTypeController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RbacController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TranchePaiementController;
use App\Http\Controllers\InterneController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChambreController;
use Illuminate\Support\Facades\Route;

// ─── Fichiers storage (logo, photos) ─────────────────────────────────────────
Route::get('/logo-etablissement', [AdminController::class, 'servirLogo'])->name('logo.etablissement');
Route::get('/etudiants/{etudiant}/photo', [EtudiantController::class, 'servirPhoto'])->name('etudiants.photo');
Route::get('/enseignants/{enseignant}/photo', [EnseignantController::class, 'servirPhoto'])->name('enseignants.photo');

Route::get('/storage/{path}', function (string $path) {
    $realBase = realpath(storage_path('app/public'));
    $fullPath = realpath($realBase . '/' . $path);

    if (!$fullPath || !str_starts_with($fullPath, $realBase . DIRECTORY_SEPARATOR)) {
        abort(403);
    }
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

// ─── Authentification ─────────────────────────────────────────────────────────
Route::get('/connexion',    [AuthController::class, 'afficherConnexion'])->name('login');
Route::post('/connexion',   [AuthController::class, 'connecter'])->middleware('throttle:10,1')->name('login.post');
Route::post('/deconnexion', [AuthController::class, 'deconnecter'])->name('logout');

// ─── Application protégée ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Tableau de bord ───────────────────────────────────────────────────────
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/impayes', [DashboardController::class, 'impayes'])->name('impayes');
    });

    // ── Étudiants ─────────────────────────────────────────────────────────────
    // create/store AVANT index/show pour éviter que {etudiant} capture "create"
    Route::middleware('permission:etudiants.create')->group(function () {
        Route::resource('etudiants', EtudiantController::class)->only(['create', 'store']);
        Route::post('etudiants-verifier-doublon', [EtudiantController::class, 'verifierDoublon'])->name('etudiants.verifier-doublon');
    });
    Route::middleware('permission:etudiants.view')->group(function () {
        Route::resource('etudiants', EtudiantController::class)->only(['index', 'show']);
        Route::get('etudiants-export/pdf', [EtudiantController::class, 'exportPdf'])->name('etudiants.export.pdf');
        Route::get('etudiants-export/csv', [EtudiantController::class, 'exportCsv'])->name('etudiants.export.csv');
    });
    Route::middleware('permission:etudiants.edit')->group(function () {
        Route::resource('etudiants', EtudiantController::class)->only(['edit', 'update']);
        Route::post('etudiants/{etudiant}/restaurer', [EtudiantController::class, 'restaurer'])->name('etudiants.restaurer');
    });
    Route::middleware('permission:etudiants.delete')->group(function () {
        Route::resource('etudiants', EtudiantController::class)->only(['destroy']);
    });

    // ── Enseignants ───────────────────────────────────────────────────────────
    Route::middleware('permission:enseignants.create')->group(function () {
        Route::resource('enseignants', EnseignantController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:enseignants.view')->group(function () {
        Route::resource('enseignants', EnseignantController::class)->only(['index', 'show']);
        Route::get('enseignants-export/pdf', [EnseignantController::class, 'exportPdf'])->name('enseignants.export.pdf');
        Route::get('enseignants-export/csv', [EnseignantController::class, 'exportCsv'])->name('enseignants.export.csv');
    });
    Route::middleware('permission:enseignants.edit')->group(function () {
        Route::resource('enseignants', EnseignantController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:enseignants.delete')->group(function () {
        Route::resource('enseignants', EnseignantController::class)->only(['destroy']);
    });

    // ── Classes ───────────────────────────────────────────────────────────────
    Route::middleware('permission:classes.create')->group(function () {
        Route::resource('classes', ClasseController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:classes.view')->group(function () {
        Route::resource('classes', ClasseController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:classes.edit')->group(function () {
        Route::resource('classes', ClasseController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:classes.delete')->group(function () {
        Route::resource('classes', ClasseController::class)->only(['destroy']);
    });

    // ── Matières & Sections ───────────────────────────────────────────────────
    Route::middleware('permission:matieres.create')->group(function () {
        Route::resource('matieres', MatiereController::class)->only(['create', 'store']);
        Route::resource('sections', SectionController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:matieres.view')->group(function () {
        Route::resource('matieres', MatiereController::class)->only(['index', 'show']);
        Route::resource('sections', SectionController::class)->only(['index']);
    });
    Route::middleware('permission:matieres.edit')->group(function () {
        Route::resource('matieres', MatiereController::class)->only(['edit', 'update']);
        Route::resource('sections', SectionController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:matieres.delete')->group(function () {
        Route::resource('matieres', MatiereController::class)->only(['destroy']);
        Route::resource('sections', SectionController::class)->only(['destroy']);
    });

    // ── Paiements ─────────────────────────────────────────────────────────────
    Route::middleware('permission:paiements.create')->group(function () {
        Route::resource('paiements', PaiementController::class)->only(['create', 'store']);
        Route::post('paiements/{paiement}/tranches', [TranchePaiementController::class, 'store'])->name('tranches.store');
    });
    Route::middleware('permission:paiements.view')->group(function () {
        Route::get('paiements/recu-groupe', [PaiementController::class, 'recuGroupe'])->name('paiements.recu-groupe');
        Route::resource('paiements', PaiementController::class)->only(['index', 'show']);
        Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'pdf'])->name('paiements.pdf');
        Route::get('paiements-export/pdf', [PaiementController::class, 'exportPdf'])->name('paiements.export.pdf');
        Route::get('paiements-export/csv', [PaiementController::class, 'exportCsv'])->name('paiements.export.csv');
    });
    Route::middleware('permission:paiements.edit')->group(function () {
        Route::resource('paiements', PaiementController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:paiements.delete')->group(function () {
        Route::resource('paiements', PaiementController::class)->only(['destroy']);
        Route::delete('tranches/{tranche}', [TranchePaiementController::class, 'destroy'])->name('tranches.destroy');
    });

    // ── Présences ─────────────────────────────────────────────────────────────
    Route::middleware('permission:presences.create')->group(function () {
        Route::post('presences', [PresenceController::class, 'store'])->name('presences.store');
    });
    Route::middleware('permission:presences.view')->group(function () {
        Route::get('presences',         [PresenceController::class, 'index'])->name('presences.index');
        Route::get('presences/rapport', [PresenceController::class, 'rapport'])->name('presences.rapport');
    });

    // ── Examens ───────────────────────────────────────────────────────────────
    Route::middleware('permission:examens.create')->group(function () {
        Route::resource('examens', ExamenController::class)->only(['create', 'store']);
        Route::post('evaluation-types', [EvaluationTypeController::class, 'store'])->name('evaluation-types.store');
    });
    Route::middleware('permission:examens.view')->group(function () {
        Route::resource('examens', ExamenController::class)->only(['index', 'show']);
        Route::get('evaluation-types', [EvaluationTypeController::class, 'index'])->name('evaluation-types.index');
    });
    Route::middleware('permission:examens.edit')->group(function () {
        Route::resource('examens', ExamenController::class)->only(['edit', 'update']);
        Route::put('evaluation-types/{evaluationType}', [EvaluationTypeController::class, 'update'])->name('evaluation-types.update');
    });
    Route::middleware('permission:examens.delete')->group(function () {
        Route::resource('examens', ExamenController::class)->only(['destroy']);
        Route::delete('evaluation-types/{evaluationType}', [EvaluationTypeController::class, 'destroy'])->name('evaluation-types.destroy');
    });

    // ── Devoirs ───────────────────────────────────────────────────────────────
    Route::middleware('permission:devoirs.create')->group(function () {
        Route::resource('devoirs', DevoirController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:devoirs.view')->group(function () {
        Route::resource('devoirs', DevoirController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:devoirs.edit')->group(function () {
        Route::resource('devoirs', DevoirController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:devoirs.delete')->group(function () {
        Route::resource('devoirs', DevoirController::class)->only(['destroy']);
    });

    // ── Compositions ──────────────────────────────────────────────────────────
    Route::middleware('permission:compositions.create')->group(function () {
        Route::resource('compositions', CompositionController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:compositions.view')->group(function () {
        Route::resource('compositions', CompositionController::class)->only(['index', 'show']);
    });
    Route::middleware('permission:compositions.edit')->group(function () {
        Route::resource('compositions', CompositionController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:compositions.delete')->group(function () {
        Route::resource('compositions', CompositionController::class)->only(['destroy']);
    });

    // ── Notes ─────────────────────────────────────────────────────────────────
    // Lecture : professeur, observateur, admin
    Route::middleware('permission:notes.view')->group(function () {
        Route::get('examens/{examen}/notes',           [NoteController::class, 'saisir'])->name('notes.saisir');
        Route::get('devoirs/{devoir}/notes',           [NoteController::class, 'saisirDevoir'])->name('notes.saisir.devoir');
        Route::get('compositions/{composition}/notes', [NoteController::class, 'saisirComposition'])->name('notes.saisir.composition');

        Route::get('etudiants/{etudiant}/bulletin',       [NoteController::class, 'bulletin'])->name('notes.bulletin');
        Route::get('etudiants/{etudiant}/bulletin/pdf',   [NoteController::class, 'bulletinPdf'])->name('notes.bulletin.pdf');
        Route::get('etudiants/{etudiant}/bulletin/print', [NoteController::class, 'bulletinPrint'])->name('notes.bulletin.print');
        Route::get('etudiants/{etudiant}/certificat',     [NoteController::class, 'certificat'])->name('etudiant.certificat');
        Route::get('bulletins/download-all',              [NoteController::class, 'downloadAllBulletins'])->name('bulletins.download-all');
    });
    // Écriture : professeur et admin uniquement
    Route::middleware('permission:notes.create')->group(function () {
        Route::post('examens/{examen}/notes',           [NoteController::class, 'enregistrer'])->name('notes.enregistrer');
        Route::post('devoirs/{devoir}/notes',           [NoteController::class, 'enregistrerDevoir'])->name('notes.enregistrer.devoir');
        Route::post('compositions/{composition}/notes', [NoteController::class, 'enregistrerComposition'])->name('notes.enregistrer.composition');
    });

    // ── Internes & Chambres ───────────────────────────────────────────────────
    Route::middleware('permission:internes.create')->group(function () {
        Route::resource('internes', InterneController::class)->only(['create', 'store']);
        Route::resource('chambres', ChambreController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:internes.view')->group(function () {
        Route::resource('internes', InterneController::class)->only(['index']);
        Route::resource('chambres', ChambreController::class)->only(['index']);
    });
    Route::middleware('permission:internes.edit')->group(function () {
        Route::resource('internes', InterneController::class)->only(['edit', 'update']);
        Route::resource('chambres', ChambreController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:internes.delete')->group(function () {
        Route::resource('internes', InterneController::class)->only(['destroy']);
        Route::resource('chambres', ChambreController::class)->only(['destroy']);
    });

    // ── Dépenses ──────────────────────────────────────────────────────────────
    Route::middleware('permission:depenses.create')->group(function () {
        Route::resource('depenses', DepenseController::class)->only(['create', 'store']);
    });
    Route::middleware('permission:depenses.view')->group(function () {
        Route::resource('depenses', DepenseController::class)->only(['index']);
        Route::get('depenses-export/pdf', [DepenseController::class, 'exportPdf'])->name('depenses.export.pdf');
        Route::get('depenses-export/csv', [DepenseController::class, 'exportCsv'])->name('depenses.export.csv');
    });
    Route::middleware('permission:depenses.edit')->group(function () {
        Route::resource('depenses', DepenseController::class)->only(['edit', 'update']);
    });
    Route::middleware('permission:depenses.delete')->group(function () {
        Route::resource('depenses', DepenseController::class)->only(['destroy']);
    });

    // ── Rapports ──────────────────────────────────────────────────────────────
    Route::middleware('permission:rapports.view')->prefix('rapports')->name('rapports.')->group(function () {
        Route::get('/',          [RapportController::class, 'index'])->name('index');
        Route::get('/etudiants', [RapportController::class, 'etudiants'])->name('etudiants');
        Route::get('/paiements', [RapportController::class, 'paiements'])->name('paiements');
        Route::get('/presences', [RapportController::class, 'presences'])->name('presences');
        Route::get('/bulletins', [RapportController::class, 'bulletins'])->name('bulletins');
    });

    // ── Paramètres ────────────────────────────────────────────────────────────
    Route::middleware('permission:parametres.view')->prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/', [ParametresController::class, 'index'])->name('index');
        Route::get('/telecharger/{fichier}', [ParametresController::class, 'telecharger'])->name('telecharger');
    });
    Route::middleware('permission:parametres.edit')->prefix('parametres')->name('parametres.')->group(function () {
        Route::post('/sauvegarder',          [ParametresController::class, 'sauvegarder'])->name('sauvegarder')->middleware('throttle:5,1');
        Route::post('/restaurer',            [ParametresController::class, 'restaurer'])->name('restaurer')->middleware('throttle:3,1');
        Route::post('/supprimer-sauvegarde', [ParametresController::class, 'supprimerSauvegarde'])->name('supprimer-sauvegarde');
        Route::post('/mot-de-passe',         [ParametresController::class, 'changerMotDePasse'])->name('mot-de-passe');
    });

    // ── Sauvegardes ───────────────────────────────────────────────────────────
    Route::middleware('permission:backups.view')->prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
    });
    Route::middleware('permission:backups.create')->prefix('backups')->name('backups.')->group(function () {
        Route::post('/create',   [BackupController::class, 'create'])->name('create')->middleware('throttle:3,1');
        Route::post('/restore',  [BackupController::class, 'restore'])->name('restore')->middleware('throttle:2,5');
        Route::post('/delete',   [BackupController::class, 'delete'])->name('delete')->middleware('throttle:5,1');
        Route::post('/download', [BackupController::class, 'download'])->name('download')->middleware('throttle:5,1');
    });

    // ── Administration ────────────────────────────────────────────────────────
    Route::middleware('permission:admin.view')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        Route::prefix('tarifs')->name('tarifs.')->group(function () {
            Route::get('/', [TarifController::class, 'index'])->name('index');
        });

        Route::prefix('annees')->name('annees.')->group(function () {
            Route::get('/', [AnneeScolaireController::class, 'index'])->name('index');
        });
    });
    Route::middleware('permission:admin.edit')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/update', [AdminController::class, 'update'])->name('update');

        Route::prefix('tarifs')->name('tarifs.')->group(function () {
            Route::post('/',          [TarifController::class, 'store'])->name('store');
            Route::put('/{tarif}',    [TarifController::class, 'update'])->name('update');
            Route::delete('/{tarif}', [TarifController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('annees')->name('annees.')->group(function () {
            Route::post('/',                         [AnneeScolaireController::class, 'store'])->name('store');
            Route::put('/{annee}',                   [AnneeScolaireController::class, 'update'])->name('update');
            Route::post('/{annee}/activer',          [AnneeScolaireController::class, 'activer'])->name('activer');
            Route::post('/{annee}/fermer',           [AnneeScolaireController::class, 'fermer'])->name('fermer');
            Route::post('/{annee}/toggle-bulletins', [AnneeScolaireController::class, 'toggleBulletins'])->name('toggle-bulletins');
            Route::post('/{annee}/set-periode',      [AnneeScolaireController::class, 'setPeriode'])->name('set-periode');
            Route::post('/{annee}/initialiser',      [AnneeScolaireController::class, 'initialiser'])->name('initialiser');
        });
    });

    // ── Gestion des utilisateurs (admin uniquement) ───────────────────────────
    Route::middleware('permission:rbac.manage')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',              [UserController::class, 'index'])->name('users.index');
        Route::post('/users',             [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',       [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',    [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ── Gestion des rôles (admin uniquement) ──────────────────────────────────
    Route::middleware('permission:rbac.manage')->prefix('rbac')->name('rbac.')->group(function () {
        Route::get('/',                          [RbacController::class, 'index'])->name('index');
        Route::post('/users/{user}/assign-role', [RbacController::class, 'assignRole'])->name('assign-role');
    });
});
