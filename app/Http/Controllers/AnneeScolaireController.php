<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::orderByDesc('date_debut')->get();
        $active = AnneeScolaire::active();

        // Nombre de classes et d'élèves actifs par année
        $statsParAnnee = Classe::selectRaw('annee_scolaire, COUNT(*) as nb_classes')
            ->groupBy('annee_scolaire')
            ->pluck('nb_classes', 'annee_scolaire');

        return view('admin.annees.index', compact('annees', 'active', 'statsParAnnee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle'    => 'required|string|unique:annees_scolaires,libelle',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ], [
            'libelle.unique' => 'Cette année scolaire existe déjà.',
        ]);

        AnneeScolaire::create([
            'libelle'    => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'statut'     => 'fermee',
        ]);

        return redirect()->route('admin.annees.index')
            ->with('succes', "Année {$request->libelle} créée.");
    }

    public function update(Request $request, AnneeScolaire $annee)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        $annee->update([
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
        ]);

        return redirect()->route('admin.annees.index')
            ->with('succes', "Dates de {$annee->libelle} mises à jour.");
    }

    public function activer(AnneeScolaire $annee)
    {
        // Fermer toutes les autres
        AnneeScolaire::where('id', '!=', $annee->id)
            ->where('statut', 'en_cours')
            ->update(['statut' => 'fermee']);

        $annee->update(['statut' => 'en_cours', 'bulletins_ouverts' => false]);

        return redirect()->route('admin.annees.index')
            ->with('succes', "Année {$annee->libelle} activée. C'est maintenant l'année en cours.");
    }

    public function fermer(AnneeScolaire $annee)
    {
        $annee->update(['statut' => 'fermee', 'bulletins_ouverts' => false]);

        return redirect()->route('admin.annees.index')
            ->with('succes', "Année {$annee->libelle} clôturée.");
    }

    public function toggleBulletins(AnneeScolaire $annee)
    {
        $annee->update(['bulletins_ouverts' => !$annee->bulletins_ouverts]);

        $msg = $annee->bulletins_ouverts
            ? "Bulletins ouverts pour {$annee->libelle} — modifications possibles."
            : "Bulletins fermés pour {$annee->libelle}.";

        return redirect()->route('admin.annees.index')->with('succes', $msg);
    }

    /**
     * Clone la structure des classes (et leurs matières associées) d'une année source vers cette année cible.
     * Les élèves ne sont pas transférés — ils doivent être inscrits manuellement dans la nouvelle année.
     */
    public function initialiser(Request $request, AnneeScolaire $annee)
    {
        $request->validate([
            'source_libelle' => 'required|exists:annees_scolaires,libelle',
        ]);

        $sourceLibelle = $request->source_libelle;
        $classesSources = Classe::where('annee_scolaire', $sourceLibelle)
            ->with('matieres')
            ->get();

        if ($classesSources->isEmpty()) {
            return back()->with('error', "Aucune classe trouvée pour l'année {$sourceLibelle}.");
        }

        $nbClassesCrees = 0;
        $classeMap = [];

        foreach ($classesSources as $source) {
            // Créer la classe dans la nouvelle année si elle n'existe pas déjà
            $nouvelle = Classe::firstOrCreate(
                ['nom' => $source->nom, 'annee_scolaire' => $annee->libelle],
                [
                    'niveau'       => $source->niveau,
                    'categorie'    => $source->categorie,
                    'capacite'     => $source->capacite,
                    'description'  => $source->description,
                    'enseignant_id'=> $source->enseignant_id,
                ]
            );

            if ($nouvelle->wasRecentlyCreated) {
                $nbClassesCrees++;
                // Copier les associations matières
                $nouvelle->matieres()->syncWithoutDetaching(
                    $source->matieres->pluck('id')->toArray()
                );
            }

            $classeMap[$source->id] = $nouvelle->id;
        }

        $msg = "{$nbClassesCrees} classe(s) créée(s) pour {$annee->libelle} avec leurs matières associées.";

        return back()->with('succes', $msg);
    }

    /** Met à jour la période active (T1/T2/T3 pour collège/lycée, S1/S2/S3 pour élémentaire). */
    public function setPeriode(Request $request, AnneeScolaire $annee)
    {
        $request->validate(['trimestre_actuel' => 'required|in:T1,T2,T3,S1,S2,S3']);

        $annee->update(['trimestre_actuel' => $request->trimestre_actuel]);

        $label = AnneeScolaire::LABELS_PERIODES[$request->trimestre_actuel] ?? $request->trimestre_actuel;

        return redirect()->route('admin.annees.index')
            ->with('succes', "Période active : {$label} ({$annee->libelle}).");
    }
}
