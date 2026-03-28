<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Établissement ─────────────────────────────────────────
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('sigle', 50)->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('directeur', 100)->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('pays', 50)->default('Sénégal');
            $table->string('ville', 50)->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->unsignedTinyInteger('jour_limite_paiement')->nullable();
            $table->timestamps();
        });

        // ── Années scolaires ──────────────────────────────────────
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 20)->unique();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['en_cours', 'fermee'])->default('fermee');
            $table->boolean('bulletins_ouverts')->default(false);
            $table->string('trimestre_actuel', 10)->nullable();
            $table->timestamps();
        });

        // ── Sections ──────────────────────────────────────────────
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('couleur', 20)->nullable();
            $table->string('niveau', 50)->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ── Types d'évaluation ────────────────────────────────────
        Schema::create('evaluation_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->decimal('poids', 4, 2)->default(1.00);
            $table->string('couleur', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ── Enseignants ───────────────────────────────────────────
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email')->nullable()->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse')->nullable();
            $table->string('specialite')->nullable();
            $table->string('photo')->nullable();
            $table->date('date_embauche')->nullable();
            $table->string('type', 50)->default('enseignant');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });

        // ── Classes ───────────────────────────────────────────────
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('niveau', 50)->nullable();
            $table->string('categorie', 50)->nullable();
            $table->unsignedSmallInteger('capacite')->default(30);
            $table->string('annee_scolaire', 20)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('enseignant_id')->nullable()->constrained('enseignants')->nullOnDelete();
            $table->timestamps();
        });

        // ── Matières ──────────────────────────────────────────────
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('code', 20)->nullable();
            $table->decimal('coefficient', 4, 2)->default(1);
            $table->string('niveau', 50)->nullable();
            $table->foreignId('enseignant_id')->nullable()->constrained('enseignants')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('section', 100)->nullable();
            $table->timestamps();
        });

        // ── Élèves ────────────────────────────────────────────────
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 50)->nullable()->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 100)->nullable();
            $table->enum('sexe', ['masculin', 'feminin'])->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->string('nom_parent', 200)->nullable();
            $table->string('tel_parent', 20)->nullable();
            $table->date('date_inscription')->nullable();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('statut', ['actif', 'inactif', 'archive'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Inscriptions ──────────────────────────────────────────
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('annee_scolaire', 20);
            $table->string('niveau', 50)->nullable();
            $table->date('date_inscription')->nullable();
            $table->enum('statut', ['actif', 'transfere', 'abandon'])->default('actif');
            $table->timestamps();
        });

        // ── Examens ───────────────────────────────────────────────
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('evaluation_type_id')->nullable()->constrained('evaluation_types')->nullOnDelete();
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'])->nullable();
            $table->date('date_examen')->nullable();
            $table->decimal('bareme', 5, 2)->default(20);
            $table->string('annee_scolaire', 20)->nullable();
            $table->timestamps();
        });

        // ── Devoirs ───────────────────────────────────────────────
        Schema::create('devoirs', function (Blueprint $table) {
            $table->id();
            $table->string('intitule', 150);
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'])->nullable();
            $table->date('date_devoir')->nullable();
            $table->decimal('note_max', 5, 2)->default(20);
            $table->text('description')->nullable();
            $table->string('annee_scolaire', 20)->nullable();
            $table->timestamps();
        });

        // ── Compositions ──────────────────────────────────────────
        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->string('intitule', 150);
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'])->nullable();
            $table->date('date_composition')->nullable();
            $table->decimal('note_max', 5, 2)->default(20);
            $table->text('description')->nullable();
            $table->string('annee_scolaire', 20)->nullable();
            $table->timestamps();
        });

        // ── Notes ─────────────────────────────────────────────────
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('examen_id')->nullable()->constrained('examens')->nullOnDelete();
            $table->foreignId('devoir_id')->nullable()->constrained('devoirs')->nullOnDelete();
            $table->foreignId('composition_id')->nullable()->constrained('compositions')->nullOnDelete();
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('evaluation_type_id')->nullable()->constrained('evaluation_types')->nullOnDelete();
            $table->string('type', 50)->nullable();
            $table->decimal('note', 5, 2);
            $table->decimal('bareme', 5, 2)->default(20);
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'])->nullable();
            $table->string('annee_scolaire', 20)->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });

        // ── Présences ─────────────────────────────────────────────
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->date('date');
            $table->enum('statut', ['present', 'absent', 'retard', 'excuse'])->default('present');
            $table->text('motif')->nullable();
            $table->timestamps();
        });

        // ── Emplois du temps ──────────────────────────────────────
        Schema::create('emplois_du_temps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();
            $table->foreignId('enseignant_id')->nullable()->constrained('enseignants')->nullOnDelete();
            $table->enum('jour', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('salle', 50)->nullable();
            $table->timestamps();
        });

        // ── Tarifs ────────────────────────────────────────────────
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 150);
            $table->string('type_frais', 50);
            $table->decimal('montant', 10, 2);
            $table->string('niveau', 50)->nullable();
            $table->string('annee_scolaire', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ── Paiements ─────────────────────────────────────────────
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->string('type_paiement', 50);
            $table->date('date_paiement');
            $table->string('annee_scolaire', 20)->nullable();
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'])->nullable();
            $table->enum('statut', ['complet', 'partiel', 'non_paye'])->default('complet');
            $table->string('numero_recu', 30)->nullable()->unique();
            $table->string('numero_facture', 30)->nullable()->index();
            $table->json('lignes')->nullable();
            $table->text('remarque')->nullable();
            $table->timestamps();
        });

        // ── Tranches de paiement ──────────────────────────────────
        Schema::create('tranches_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->string('type_frais', 50);
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->string('annee_scolaire', 20)->nullable();
            $table->enum('statut', ['en_cours', 'solde'])->default('en_cours');
            $table->timestamps();
        });

        // ── Chambres ──────────────────────────────────────────────
        Schema::create('chambres', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->unsignedSmallInteger('capacite')->default(1);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ── Internes ──────────────────────────────────────────────
        Schema::create('internes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('chambre_id')->nullable()->constrained('chambres')->nullOnDelete();
            $table->string('chambre', 50)->nullable();
            $table->date('date_entree');
            $table->date('date_sortie')->nullable();
            $table->string('annee_scolaire', 20)->nullable();
            $table->enum('statut', ['actif', 'sorti'])->default('actif');
            $table->text('remarque')->nullable();
            $table->timestamps();
        });

        // ── Dépenses ──────────────────────────────────────────────
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 150);
            $table->string('categorie', 100)->nullable();
            $table->decimal('montant', 10, 2);
            $table->date('date_depense');
            $table->string('annee_scolaire', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('internes');
        Schema::dropIfExists('chambres');
        Schema::dropIfExists('tranches_paiement');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('tarifs');
        Schema::dropIfExists('emplois_du_temps');
        Schema::dropIfExists('presences');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('compositions');
        Schema::dropIfExists('devoirs');
        Schema::dropIfExists('examens');
        Schema::dropIfExists('inscriptions');
        Schema::dropIfExists('etudiants');
        Schema::dropIfExists('matieres');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('enseignants');
        Schema::dropIfExists('evaluation_types');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('annees_scolaires');
        Schema::dropIfExists('etablissements');
    }
};
