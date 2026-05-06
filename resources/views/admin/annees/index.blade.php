@extends('layouts.app')
@section('titre', 'Années Scolaires')
@section('titre-page', 'Gestion des Années Scolaires')

@section('contenu')

@php $anneeActive = \App\Models\AnneeScolaire::active(); @endphp
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-4">
    <a href="{{ route('admin.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Établissement
    </a>
    <a href="{{ route('admin.tarifs.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M9 14h.01M15 14h.01M12 14h.01M15 7h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Grille tarifaire
    </a>
    <a href="{{ route('admin.annees.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Années scolaires
        @if($anneeActive)<span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">{{ $anneeActive->libelle }}</span>@else<span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-xs rounded-full font-semibold">!</span>@endif
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Utilisateurs
    </a>
</div>

<div class="grid grid-cols-3 gap-5">

    {{-- Formulaire nouvelle année --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Créer une nouvelle année</h3>
        <form action="{{ route('admin.annees.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle') }}" placeholder="ex: 2025-2026" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('libelle')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de début</label>
                <input type="date" name="date_debut" value="{{ old('date_debut') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de fin</label>
                <input type="date" name="date_fin" value="{{ old('date_fin') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                + Créer l'année
            </button>
        </form>
    </div>

    {{-- Liste des années --}}
    <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Toutes les années scolaires</h3>

        @if($annees->isEmpty())
        <p class="text-sm text-gray-400 text-center py-6">Aucune année scolaire créée.</p>
        @else
        <div class="space-y-3">
            @foreach($annees as $annee)
            @php
                $estActive  = $annee->statut === 'en_cours';
                $estFermee  = $annee->statut === 'fermee';
                $bulletins  = $annee->bulletins_ouverts;
            @endphp
            <div class="rounded-xl border overflow-hidden
                {{ $estActive ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">

                {{-- Ligne principale --}}
                <div class="p-4 flex items-center gap-4">
                    {{-- Badge statut --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                        {{ $estActive ? 'bg-green-200' : 'bg-gray-200' }}">
                        @if($estActive)
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @else
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800 text-base">{{ $annee->libelle }}</span>
                            @if($estActive)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-200 text-green-800">En cours</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Clôturée</span>
                            @endif
                            @if($bulletins)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:#fef9c3;color:#713f12;">Bulletins ouverts</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $annee->date_debut->format('d/m/Y') }} → {{ $annee->date_fin->format('d/m/Y') }}
                            @if($estActive && $annee->trimestre_actuel)
                            &nbsp;·&nbsp;
                            <span class="font-medium text-blue-700">
                                {{ \App\Models\AnneeScolaire::LABELS_PERIODES[$annee->trimestre_actuel] ?? $annee->trimestre_actuel }} en cours
                            </span>
                            @endif
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap justify-end">

                        {{-- Sélecteur de période (T1/T2/T3 ou S1/S2/S3) --}}
                        @if($estActive)
                        <form action="{{ route('admin.annees.set-periode', $annee) }}" method="POST" class="flex items-center gap-1">
                            @csrf
                            @php
                                $periodeActive = $annee->trimestre_actuel ?? 'T1';
                                $labels = \App\Models\AnneeScolaire::LABELS_PERIODES;
                            @endphp
                            <select name="trimestre_actuel" onchange="this.form.submit()"
                                    class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                    style="min-width: 140px;">
                                <optgroup label="Trimestres">
                                    @foreach(['T1','T2','T3'] as $p)
                                    <option value="{{ $p }}" {{ $periodeActive === $p ? 'selected' : '' }}>
                                        {{ $labels[$p] }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Semestres (Élémentaire)">
                                    @foreach(['S1','S2','S3'] as $p)
                                    <option value="{{ $p }}" {{ $periodeActive === $p ? 'selected' : '' }}>
                                        {{ $labels[$p] }}
                                    </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </form>
                        @endif

                        {{-- Bouton modifier dates --}}
                        <button type="button"
                                onclick="toggleEdit({{ $annee->id }})"
                                class="px-3 py-1.5 border border-blue-300 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-50">
                            Modifier dates
                        </button>

                        @if($estFermee)
                        <form action="{{ route('admin.annees.activer', $annee) }}" method="POST"
                              onsubmit="return confirm('Activer cette année ? Elle deviendra l\'année en cours.')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700">
                                Activer
                            </button>
                        </form>
                        @endif

                        @if($estActive)
                        <form action="{{ route('admin.annees.fermer', $annee) }}" method="POST"
                              onsubmit="return confirm('Clôturer cette année scolaire ? Vous pourrez la réouvrir pour les bulletins.')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-medium hover:bg-red-600">
                                Clôturer
                            </button>
                        </form>
                        @endif

                        @if($estFermee)
                        <form action="{{ route('admin.annees.toggle-bulletins', $annee) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $bulletins ? 'bg-amber-500 text-white hover:bg-amber-600' : 'border border-amber-400 text-amber-700 hover:bg-amber-50' }}">
                                {{ $bulletins ? 'Fermer bulletins' : 'Réouvrir bulletins' }}
                            </button>
                        </form>
                        @endif

                        {{-- Bouton initialiser (transition d'année) --}}
                        @php $nbClassesAnnee = $statsParAnnee[$annee->libelle] ?? 0; @endphp
                        <button type="button"
                                onclick="toggleInit({{ $annee->id }})"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-indigo-300 text-indigo-600 hover:bg-indigo-50">
                            ↗ Initialiser
                        </button>
                    </div>
                </div>

                {{-- Panneau d'initialisation (transition d'année) --}}
                <div id="init-{{ $annee->id }}" class="hidden border-t border-dashed border-indigo-200 bg-indigo-50/60 px-4 py-4">
                    <p class="text-sm font-medium text-indigo-800 mb-3">
                        Copier les classes et élèves d'une autre année vers <strong>{{ $annee->libelle }}</strong>
                        @if($nbClassesAnnee > 0)
                        <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">{{ $nbClassesAnnee }} classe(s) déjà présente(s) — sera ignorées si même nom</span>
                        @endif
                    </p>
                    <form action="{{ route('admin.annees.initialiser', $annee) }}" method="POST"
                          onsubmit="return confirm('Copier les classes et transférer les élèves actifs vers {{ $annee->libelle }} ?')">
                        @csrf
                        <div class="flex items-end gap-3 flex-wrap">
                            <div>
                                <label class="block text-xs font-medium text-indigo-700 mb-1">Année source (copier depuis)</label>
                                <select name="source_libelle" required
                                        class="border border-indigo-300 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-indigo-400 focus:outline-none" style="min-width:160px;">
                                    <option value="">-- Choisir --</option>
                                    @foreach($annees->where('id', '!=', $annee->id)->sortByDesc('date_debut') as $src)
                                    <option value="{{ $src->libelle }}">
                                        {{ $src->libelle }}
                                        ({{ $statsParAnnee[$src->libelle] ?? 0 }} classes)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-xs text-indigo-600 leading-relaxed max-w-xs">
                                <strong>Ce qui sera copié :</strong><br>
                                ✓ Structure des classes (nom, niveau, catégorie)<br>
                                ✓ Associations matières–classes<br>
                                ✗ Élèves (à inscrire manuellement)
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                        class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                    Lancer l'initialisation
                                </button>
                                <button type="button" onclick="toggleInit({{ $annee->id }})"
                                        class="px-4 py-1.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Formulaire édition dates (caché par défaut) --}}
                <div id="edit-{{ $annee->id }}" class="hidden border-t border-dashed border-gray-300 bg-white px-4 py-3">
                    <form action="{{ route('admin.annees.update', $annee) }}" method="POST"
                          class="flex items-end gap-3 flex-wrap">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Date de début</label>
                            <input type="date" name="date_debut"
                                   value="{{ $annee->date_debut->format('Y-m-d') }}"
                                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Date de fin</label>
                            <input type="date" name="date_fin"
                                   value="{{ $annee->date_fin->format('Y-m-d') }}"
                                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Enregistrer
                            </button>
                            <button type="button" onclick="toggleEdit({{ $annee->id }})"
                                    class="px-4 py-1.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Info explication --}}
<div class="mt-5 bg-blue-50 border border-blue-200 rounded-xl p-4">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800 space-y-1">
            <p><strong>En cours :</strong> Une seule année peut être active. Elle est utilisée pour les inscriptions, paiements et notes.</p>
            <p><strong>Clôturer :</strong> Ferme l'année en cours. Créez et activez la nouvelle année après.</p>
            <p><strong>Réouvrir bulletins :</strong> Permet de modifier les notes et bulletins d'une année clôturée sans la réactiver.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleEdit(id) {
    document.getElementById('edit-' + id).classList.toggle('hidden');
}
function toggleInit(id) {
    document.getElementById('init-' + id).classList.toggle('hidden');
}
</script>
@endpush

@endsection
