@extends('layouts.app')
@section('titre', $etudiant->nom_complet)
@section('titre-page', 'Profil Etudiant')
@section('breadcrumb', 'Etudiants / ' . $etudiant->nom_complet)

@section('contenu')

<x-btn-retour :href="route('etudiants.index')" label="Retour aux etudiants" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Carte profil --}}
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            @if($etudiant->photo)
            <img src="{{ $etudiant->photo_url }}"
                 class="w-24 h-24 rounded-full object-cover border-4 border-blue-100 mx-auto mb-3"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-blue-100 mx-auto mb-3" style="display:none;">
                {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
            </div>
            @else
            <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-blue-100 mx-auto mb-3">
                {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
            </div>
            @endif
            <h2 class="font-bold text-gray-800 text-lg">{{ $etudiant->nom_complet }}</h2>
            <p class="text-sm text-gray-500">{{ $etudiant->matricule }}</p>
            @php
                $badgeClasses = match($etudiant->statut) {
                    'actif'   => 'bg-green-100 text-green-700',
                    'archive' => 'bg-orange-100 text-orange-600',
                    default   => 'bg-red-100 text-red-600',
                };
            @endphp
            <span class="inline-flex mt-2 items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                {{ $etudiant->statut === 'archive' ? 'Archivé' : ucfirst($etudiant->statut) }}
            </span>

            <div class="mt-5 text-left space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Classe</span>
                    <span class="font-medium text-gray-700">{{ $etudiant->classe?->nom ?? '—' }}</span>
                </div>
                @php $derniereInscription = $etudiant->inscriptions->sortByDesc('created_at')->first(); @endphp
                @if($derniereInscription?->niveau)
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Niveau</span>
                    <span class="font-medium text-gray-700">{{ \App\Models\Classe::CATEGORIES[$derniereInscription->niveau] ?? ucfirst($derniereInscription->niveau) }}</span>
                </div>
                @endif
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Sexe</span>
                    <span class="font-medium text-gray-700">{{ ucfirst($etudiant->sexe) }}</span>
                </div>
                @if($etudiant->date_naissance)
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Age</span>
                    <span class="font-medium text-gray-700">{{ $etudiant->age }} ans</span>
                </div>
                @endif
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Telephone</span>
                    <span class="font-medium text-gray-700">{{ $etudiant->telephone ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="text-gray-400">Parent</span>
                    <span class="font-medium text-gray-700">{{ $etudiant->nom_parent ?? '—' }}</span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-gray-400">Tel parent</span>
                    <span class="font-medium text-gray-700">{{ $etudiant->tel_parent ?? '—' }}</span>
                </div>
            </div>

            <div class="mt-5 flex gap-2">
                <a href="{{ route('etudiants.edit', $etudiant) }}"
                   class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm text-center hover:bg-blue-700 transition-colors">
                    Modifier
                </a>
                @if($etudiant->classe_id)
                <a href="{{ route('notes.bulletin', $etudiant) }}"
                   class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm text-center hover:bg-gray-50 transition-colors">
                    Bulletin
                </a>
                @endif
            </div>

            @if($etudiant->statut === 'archive')
            <form action="{{ route('etudiants.restaurer', $etudiant) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Restaurer cet eleve
                </button>
            </form>
            @elseif($etudiant->statut !== 'archive')
            <div x-data="{ ouvert: false }" class="mt-3">
                <button type="button" @click="ouvert = true" class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-orange-300 text-orange-600 rounded-lg text-sm hover:bg-orange-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archiver
                </button>
                <div x-show="ouvert" x-cloak @keydown.escape.window="ouvert = false"
                     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6" @click.stop @click.outside="ouvert = false">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Archiver cet eleve</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Les donnees de <strong>{{ $etudiant->nom_complet }}</strong> seront conservees. Vous pourrez le restaurer a tout moment.</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="ouvert = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</button>
                            <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg" style="background:#ea580c;">Archiver</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($etudiant->classe_id)
            @php
                $anneeScolaireActive = \App\Models\AnneeScolaire::libelleActif();
                $trimestreActif      = \App\Models\AnneeScolaire::trimestreActif();
                $isElem = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);
                $periodeActive = $isElem ? str_replace('T', 'S', $trimestreActif) : $trimestreActif;
            @endphp
            <a href="{{ route('notes.bulletin.pdf', ['etudiant' => $etudiant->id, 'annee_scolaire' => $anneeScolaireActive, 'trimestre' => $periodeActive]) }}"
               class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Générer le bulletin</span>
                <span class="text-xs opacity-75 ml-1">({{ $periodeActive }} · {{ $anneeScolaireActive }})</span>
            </a>
            @else
            <p class="mt-3 text-xs text-center text-gray-400">Aucune classe assignée — bulletin non disponible</p>
            @endif
        </div>
    </div>

    {{-- Onglets --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Paiements --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">Paiements</h3>
                <a href="{{ route('paiements.create', ['etudiant_id' => $etudiant->id]) }}"
                   class="text-xs px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    + Paiement
                </a>
            </div>
            @if($etudiant->paiements->count())
            <table class="w-full text-sm">
                <thead><tr class="text-gray-400 text-xs border-b">
                    <th class="pb-2 text-left">Date</th>
                    <th class="pb-2 text-left">Type</th>
                    <th class="pb-2 text-left">Montant</th>
                    <th class="pb-2 text-left">Recu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($etudiant->paiements->sortByDesc('date_paiement')->take(5) as $p)
                    <tr>
                        <td class="py-2 text-gray-600">{{ $p->date_paiement->format('d/m/Y') }}</td>
                        <td class="py-2 text-gray-500">{{ ucfirst($p->type_paiement) }}</td>
                        <td class="py-2 font-semibold text-green-600">{{ number_format($p->montant, 0, ',', ' ') }} XOF</td>
                        <td class="py-2 text-gray-400 text-xs font-mono">{{ $p->numero_recu }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-gray-400 text-center py-3">Aucun paiement enregistre</p>
            @endif
        </div>

        {{-- Notes recentes --}}
        @if($etudiant->classe_id)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">Dernières notes</h3>
                <a href="{{ route('notes.bulletin', $etudiant) }}" class="text-xs text-blue-600 hover:underline">Voir bulletin</a>
            </div>
            @if($etudiant->notes->count())
            <table class="w-full text-sm">
                <thead><tr class="text-gray-400 text-xs border-b">
                    <th class="pb-2 text-left">Matiere</th>
                    <th class="pb-2 text-left">Examen</th>
                    <th class="pb-2 text-left">Note</th>
                    <th class="pb-2 text-left">Mention</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($etudiant->notes->take(5) as $note)
                    @php
                        $src     = $note->examen ?? $note->devoir ?? $note->composition;
                        $matiere = $src?->matiere;
                    @endphp
                    <tr>
                        <td class="py-2 text-gray-600">{{ $matiere?->nom ?? '—' }}</td>
                        <td class="py-2 text-gray-500">{{ $src?->intitule ?? '—' }}</td>
                        <td class="py-2 font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $note->note }}/{{ $src?->note_max ?? '?' }}
                        </td>
                        <td class="py-2 text-gray-400 text-xs">{{ $note->mention }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-gray-400 text-center py-3">Aucune note enregistrée</p>
            @endif
        </div>
        @endif

    </div>
</div>

@endsection
