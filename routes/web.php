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
use App\Http\Controllers\TarifController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TranchePaiementController;
use App\Http\Controllers\InterneController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChambreController;
use Illuminate\Support\Facades\Route;

// ─── Routes dédiées pour les fichiers (contournent les symlinks Windows) ─────
Route::get('/logo-etablissement', [AdminController::class, 'servirLogo'])->name('logo.etablissement');
Route::get('/etudiants/{etudiant}/photo', [EtudiantController::class, 'servirPhoto'])->name('etudiants.photo');
Route::get('/enseignants/{enseignant}/photo', [EnseignantController::class, 'servirPhoto'])->name('enseignants.photo');

// ─── Fallback pour servir les fichiers storage (Windows/Laragon) ────────────
Route::get('/storage/{path}', function (string $path) {
    // Neutralise les tentatives de traversal (../../etc/passwd)
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

// ─── Authentification ────────────────────────────────────────────────────────
Route::get('/connexion',    [AuthController::class, 'afficherConnexion'])->name('login');
Route::post('/connexion',   [AuthController::class, 'connecter'])->middleware('throttle:10,1')->name('login.post');
Route::post('/deconnexion', [AuthController::class, 'deconnecter'])->name('logout');

// ─── Application protégée ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/impayes', [DashboardController::class, 'impayes'])->name('impayes');

    Route::resource('etudiants',   EtudiantController::class);
    Route::post('etudiants/{etudiant}/restaurer', [EtudiantController::class, 'restaurer'])->name('etudiants.restaurer');
    Route::post('etudiants-verifier-doublon', [EtudiantController::class, 'verifierDoublon'])->name('etudiants.verifier-doublon');
    Route::get('etudiants-export/pdf', [EtudiantController::class, 'exportPdf'])->name('etudiants.export.pdf');
    Route::get('etudiants-export/csv', [EtudiantController::class, 'exportCsv'])->name('etudiants.export.csv');
    Route::resource('enseignants', EnseignantController::class);
    Route::get('enseignants-export/pdf', [EnseignantController::class, 'exportPdf'])->name('enseignants.export.pdf');
    Route::get('enseignants-export/csv', [EnseignantController::class, 'exportCsv'])->name('enseignants.export.csv');
    Route::resource('classes',     ClasseController::class);
    Route::resource('matieres',    MatiereController::class);
    Route::resource('sections',    SectionController::class)->except(['show']);
    Route::resource('internes',    InterneController::class)->except(['show']);
    Route::resource('chambres',    ChambreController::class)->except(['show']);
    Route::resource('depenses',    DepenseController::class)->except(['show']);
    Route::get('depenses-export/pdf', [DepenseController::class, 'exportPdf'])->name('depenses.export.pdf');
    Route::get('depenses-export/csv', [DepenseController::class, 'exportCsv'])->name('depenses.export.csv');

    Route::get('paiements/recu-groupe', [PaiementController::class, 'recuGroupe'])->name('paiements.recu-groupe');
    Route::resource('paiements', PaiementController::class);
    Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'pdf'])->name('paiements.pdf');
    Route::get('paiements-export/pdf', [PaiementController::class, 'exportPdf'])->name('paiements.export.pdf');
    Route::get('paiements-export/csv', [PaiementController::class, 'exportCsv'])->name('paiements.export.csv');
    Route::post('paiements/{paiement}/tranches', [TranchePaiementController::class, 'store'])->name('tranches.store');
    Route::delete('tranches/{tranche}', [TranchePaiementController::class, 'destroy'])->name('tranches.destroy');

    // Présences
    Route::get('presences',          [PresenceController::class, 'index'])->name('presences.index');
    Route::post('presences',         [PresenceController::class, 'store'])->name('presences.store');
    Route::get('presences/rapport',  [PresenceController::class, 'rapport'])->name('presences.rapport');

    // Examens, Devoirs, Compositions
    Route::resource('examens',       ExamenController::class);
    Route::resource('devoirs',       DevoirController::class);
    Route::resource('compositions',  CompositionController::class);

    // Types d'évaluation (Devoir / Contrôle / Examen avec pondération)
    Route::get('evaluation-types',                     [EvaluationTypeController::class, 'index'])->name('evaluation-types.index');
    Route::post('evaluation-types',                    [EvaluationTypeController::class, 'store'])->name('evaluation-types.store');
    Route::put('evaluation-types/{evaluationType}',    [EvaluationTypeController::class, 'update'])->name('evaluation-types.update');
    Route::delete('evaluation-types/{evaluationType}', [EvaluationTypeController::class, 'destroy'])->name('evaluation-types.destroy');

    // Notes - Examens
    Route::get('examens/{examen}/notes',  [NoteController::class, 'saisir'])->name('notes.saisir');
    Route::post('examens/{examen}/notes', [NoteController::class, 'enregistrer'])->name('notes.enregistrer');

    // Notes - Devoirs
    Route::get('devoirs/{devoir}/notes',  [NoteController::class, 'saisirDevoir'])->name('notes.saisir.devoir');
    Route::post('devoirs/{devoir}/notes', [NoteController::class, 'enregistrerDevoir'])->name('notes.enregistrer.devoir');

    // Notes - Compositions
    Route::get('compositions/{composition}/notes',  [NoteController::class, 'saisirComposition'])->name('notes.saisir.composition');
    Route::post('compositions/{composition}/notes', [NoteController::class, 'enregistrerComposition'])->name('notes.enregistrer.composition');

    // Bulletin
    Route::get('etudiants/{etudiant}/bulletin', [NoteController::class, 'bulletin'])->name('notes.bulletin');
    Route::get('etudiants/{etudiant}/bulletin/pdf', [NoteController::class, 'bulletinPdf'])->name('notes.bulletin.pdf');
    Route::get('etudiants/{etudiant}/bulletin/print', [NoteController::class, 'bulletinPrint'])->name('notes.bulletin.print');
    Route::get('etudiants/{etudiant}/certificat', [NoteController::class, 'certificat'])->name('etudiant.certificat');
    Route::get('bulletins/download-all', [NoteController::class, 'downloadAllBulletins'])->name('bulletins.download-all');

    // Rapports
    Route::prefix('rapports')->name('rapports.')->group(function () {
        Route::get('/',          [RapportController::class, 'index'])->name('index');
        Route::get('/etudiants', [RapportController::class, 'etudiants'])->name('etudiants');
        Route::get('/paiements', [RapportController::class, 'paiements'])->name('paiements');
        Route::get('/presences', [RapportController::class, 'presences'])->name('presences');
        Route::get('/bulletins', [RapportController::class, 'bulletins'])->name('bulletins');
    });

    // Paramètres
    Route::prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/',                    [ParametresController::class, 'index'])->name('index');
        Route::post('/sauvegarder',        [ParametresController::class, 'sauvegarder'])->name('sauvegarder')->middleware('throttle:5,1');
        Route::post('/restaurer',          [ParametresController::class, 'restaurer'])->name('restaurer')->middleware('throttle:3,1');
        Route::post('/supprimer-sauvegarde', [ParametresController::class, 'supprimerSauvegarde'])->name('supprimer-sauvegarde');
        Route::get('/telecharger/{fichier}', [ParametresController::class, 'telecharger'])->name('telecharger');
        Route::post('/mot-de-passe',         [ParametresController::class, 'changerMotDePasse'])->name('mot-de-passe');
    });

    // Sauvegardes de base de données
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/',          [BackupController::class, 'index'])->name('index');
        Route::post('/create',   [BackupController::class, 'create'])->name('create')->middleware('throttle:3,1');
        Route::post('/restore',  [BackupController::class, 'restore'])->name('restore')->middleware('throttle:2,5');
        Route::post('/delete',   [BackupController::class, 'delete'])->name('delete')->middleware('throttle:5,1');
        Route::post('/download', [BackupController::class, 'download'])->name('download')->middleware('throttle:5,1');
    });

    // Administration
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/update', [AdminController::class, 'update'])->name('update');

        // Tarifs
        Route::prefix('tarifs')->name('tarifs.')->group(function () {
            Route::get('/',                  [TarifController::class, 'index'])->name('index');
            Route::post('/',                 [TarifController::class, 'store'])->name('store');
            Route::put('/{tarif}',           [TarifController::class, 'update'])->name('update');
            Route::delete('/{tarif}',        [TarifController::class, 'destroy'])->name('destroy');
        });

        // Années scolaires
        Route::prefix('annees')->name('annees.')->group(function () {
            Route::get('/',                               [AnneeScolaireController::class, 'index'])->name('index');
            Route::post('/',                              [AnneeScolaireController::class, 'store'])->name('store');
            Route::post('/{annee}/activer',               [AnneeScolaireController::class, 'activer'])->name('activer');
            Route::post('/{annee}/fermer',                [AnneeScolaireController::class, 'fermer'])->name('fermer');
            Route::post('/{annee}/toggle-bulletins',      [AnneeScolaireController::class, 'toggleBulletins'])->name('toggle-bulletins');
            Route::post('/{annee}/set-periode',           [AnneeScolaireController::class, 'setPeriode'])->name('set-periode');
            Route::put('/{annee}',                        [AnneeScolaireController::class, 'update'])->name('update');
        });
    });
});
