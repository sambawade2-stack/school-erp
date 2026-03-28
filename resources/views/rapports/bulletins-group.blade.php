@extends('layouts.app')
@section('titre', 'Générer les Bulletins par Classe')
@section('titre-page', 'Générer les Bulletins par Classe')

@section('contenu')

<x-btn-retour :href="route('rapports.index')" label="Retour aux rapports" breadcrumb="Bulletins par classe" />

@php
    $annees = \App\Models\AnneeScolaire::orderByDesc('date_debut')->get();
    $anneeActive = \App\Models\AnneeScolaire::libelleActif();
    $trimestreActif = \App\Models\AnneeScolaire::trimestreActif();
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <h3 class="font-bold text-lg text-gray-800 mb-4">Générer les bulletins d'une classe</h3>
    <p class="text-sm text-gray-500 mb-5">Tous les bulletins des élèves de la classe seront regroupés dans un seul PDF, prêt à imprimer.</p>

    <form action="{{ route('bulletins.download-all') }}" method="GET" class="space-y-4">

        {{-- Classe --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe <span class="text-red-500">*</span></label>
            <select name="classe_id" id="classe_select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Sélectionner une classe --</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}"
                        data-categorie="{{ $classe->categorie }}"
                        data-nb="{{ $classe->etudiants()->where('statut','actif')->count() }}">
                    {{ $classe->nom }}
                    ({{ $classe->etudiants()->where('statut','actif')->count() }} élèves)
                </option>
                @endforeach
            </select>
        </div>

        {{-- Période (dynamique selon classe) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5" id="periode_label">Période <span class="text-red-500">*</span></label>
            <select name="trimestre" id="periode_select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Sélectionner --</option>
                <option value="T1">Trimestre 1</option>
                <option value="T2">Trimestre 2</option>
                <option value="T3">Trimestre 3</option>
            </select>
        </div>

        {{-- Année scolaire --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire <span class="text-red-500">*</span></label>
            <select name="annee_scolaire" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach($annees as $a)
                <option value="{{ $a->libelle }}" {{ $a->libelle === $anneeActive ? 'selected' : '' }}>
                    {{ $a->libelle }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" id="info_box">
            <p class="text-sm text-blue-800">
                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <span id="info_text">Sélectionnez une classe pour générer un PDF unique avec tous les bulletins regroupés.</span>
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Générer le PDF
            </button>
            <a href="{{ route('rapports.index') }}" class="flex-1 px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 text-center transition-colors">
                Annuler
            </a>
        </div>
    </form>
</div>

{{-- Génération par lot (toutes les classes) --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl mt-5">
    <h3 class="font-bold text-lg text-gray-800 mb-2">Générer pour toutes les classes</h3>
    <p class="text-sm text-gray-500 mb-4">Génère un ZIP contenant un PDF par classe. Chaque PDF regroupe les bulletins de tous les élèves.</p>

    <form action="{{ route('bulletins.download-all') }}" method="GET" class="flex items-end gap-3">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Période</label>
            <select name="trimestre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="T1" {{ $trimestreActif === 'T1' ? 'selected' : '' }}>T1 / S1</option>
                <option value="T2" {{ $trimestreActif === 'T2' ? 'selected' : '' }}>T2 / S2</option>
                <option value="T3" {{ $trimestreActif === 'T3' ? 'selected' : '' }}>T3 / S3</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année</label>
            <select name="annee_scolaire" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach($annees as $a)
                <option value="{{ $a->libelle }}" {{ $a->libelle === $anneeActive ? 'selected' : '' }}>{{ $a->libelle }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors whitespace-nowrap">
            Télécharger le ZIP
        </button>
    </form>
</div>

<script>
document.getElementById('classe_select').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const cat = opt.dataset.categorie || '';
    const nb  = opt.dataset.nb || '0';
    const isElem = (cat === 'elementaire' || cat === 'prescolaire');
    const sel = document.getElementById('periode_select');
    const label = document.getElementById('periode_label');

    // Mettre à jour le label
    label.innerHTML = isElem
        ? 'Semestre <span class="text-red-500">*</span>'
        : 'Trimestre <span class="text-red-500">*</span>';

    // Mettre à jour les options
    sel.innerHTML = '<option value="">-- Sélectionner --</option>';
    if (isElem) {
        sel.innerHTML += '<option value="S1">Semestre 1</option><option value="S2">Semestre 2</option><option value="S3">Semestre 3</option>';
    } else {
        sel.innerHTML += '<option value="T1">Trimestre 1</option><option value="T2">Trimestre 2</option><option value="T3">Trimestre 3</option>';
    }

    // Mettre à jour l'info
    const info = document.getElementById('info_text');
    if (this.value) {
        info.textContent = 'Le PDF contiendra les bulletins de ' + nb + ' élève(s) de la classe ' + opt.text.split('(')[0].trim() + ', regroupés dans un seul fichier.';
    }
});
</script>

@endsection
