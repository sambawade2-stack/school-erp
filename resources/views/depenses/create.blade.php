@extends('layouts.app')
@section('titre', 'Nouvelle Dépense')
@section('titre-page', 'Enregistrer une Dépense')

@section('contenu')

<x-btn-retour :href="route('depenses.index')" label="Retour aux dépenses" breadcrumb="Nouvelle dépense" />

<div class="max-w-xl">
<form action="{{ route('depenses.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">

        {{-- Type de mouvement --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de mouvement <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-3 gap-2" id="type-mouvement-group">
                @php $oldType = old('type_mouvement', 'depense'); @endphp
                <label id="btn-depense" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $oldType === 'depense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:border-red-300' }}">
                    <input type="radio" name="type_mouvement" value="depense" {{ $oldType === 'depense' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Dépense
                </label>
                <label id="btn-depot" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $oldType === 'depot_banque' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-green-300' }}">
                    <input type="radio" name="type_mouvement" value="depot_banque" {{ $oldType === 'depot_banque' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Dépôt banque
                </label>
                <label id="btn-retrait" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $oldType === 'retrait_banque' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-600 hover:border-amber-300' }}">
                    <input type="radio" name="type_mouvement" value="retrait_banque" {{ $oldType === 'retrait_banque' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Retrait banque
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Libellé <span class="text-red-500">*</span></label>
            <input type="text" name="libelle" value="{{ old('libelle') }}" placeholder="ex: Achat de craies"
                   required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant (XOF) <span class="text-red-500">*</span></label>
                <input type="number" name="montant" value="{{ old('montant') }}" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div id="bloc-categorie">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" id="categorie" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="fournitures" {{ old('categorie', $categorie ?? '') === 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                    <option value="salaires" {{ old('categorie', $categorie ?? '') === 'salaires' ? 'selected' : '' }}>Salaires</option>
                    <option value="maintenance" {{ old('categorie', $categorie ?? '') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="transport" {{ old('categorie', $categorie ?? '') === 'transport' ? 'selected' : '' }}>Transport</option>
                    <option value="alimentation" {{ old('categorie', $categorie ?? '') === 'alimentation' ? 'selected' : '' }}>Alimentation</option>
                    <option value="autre" {{ old('categorie', $categorie ?? '') === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
        </div>

        {{-- Sélection du personnel (visible seulement pour Salaires) --}}
        <div id="bloc-personnel" style="display: none;">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Personnel <span class="text-red-500">*</span></label>
            <select id="personnel-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Sélectionner un membre du personnel --</option>
                @foreach($personnels as $p)
                <option value="{{ $p->nom_complet }}" {{ old('beneficiaire') === $p->nom_complet ? 'selected' : '' }}>
                    {{ $p->nom_complet }} ({{ \App\Models\Enseignant::TYPES[$p->type] ?? ucfirst($p->type) }})
                </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_depense" value="{{ old('date_depense', date('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div id="bloc-beneficiaire">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bénéficiaire / Référence</label>
                <input type="text" name="beneficiaire" id="beneficiaire" value="{{ old('beneficiaire') }}" placeholder="ex: Banque BDM"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description') }}</textarea>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" id="btn-submit" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors" style="background:#dc2626; color:#fff;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">Enregistrer</button>
        <a href="{{ route('depenses.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var radios      = document.querySelectorAll('input[name="type_mouvement"]');
    var btnDepense  = document.getElementById('btn-depense');
    var btnDepot    = document.getElementById('btn-depot');
    var btnRetrait  = document.getElementById('btn-retrait');
    var categorieEl = document.getElementById('categorie');
    var blocCateg   = document.getElementById('bloc-categorie');
    var blocPersonnel  = document.getElementById('bloc-personnel');
    var personnelSelect = document.getElementById('personnel-select');
    var beneficiaireInput = document.getElementById('beneficiaire');
    var blocBeneficiaire  = document.getElementById('bloc-beneficiaire');
    var btnSubmit   = document.getElementById('btn-submit');

    function applyTypeStyle() {
        var type = document.querySelector('input[name="type_mouvement"]:checked')?.value ?? 'depense';

        // Reset all button styles
        btnDepense.className = btnDepense.className.replace(/border-(red|green|amber)-500 bg-(red|green|amber)-50 text-(red|green|amber)-700/g, 'border-gray-200 text-gray-600');
        btnDepot.className   = btnDepot.className.replace(/border-(red|green|amber)-500 bg-(red|green|amber)-50 text-(red|green|amber)-700/g, 'border-gray-200 text-gray-600');
        btnRetrait.className = btnRetrait.className.replace(/border-(red|green|amber)-500 bg-(red|green|amber)-50 text-(red|green|amber)-700/g, 'border-gray-200 text-gray-600');

        var isDepense = (type === 'depense');
        var isDepot   = (type === 'depot_banque');
        var isRetrait = (type === 'retrait_banque');

        // Active button styling
        if (isDepense) {
            btnDepense.classList.add('border-red-500', 'bg-red-50', 'text-red-700');
            btnSubmit.style.backgroundColor = '#dc2626';
            btnSubmit.onmouseover = function() { this.style.backgroundColor = '#b91c1c'; };
            btnSubmit.onmouseout  = function() { this.style.backgroundColor = '#dc2626'; };
        } else if (isDepot) {
            btnDepot.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
            btnSubmit.style.backgroundColor = '#16a34a';
            btnSubmit.onmouseover = function() { this.style.backgroundColor = '#15803d'; };
            btnSubmit.onmouseout  = function() { this.style.backgroundColor = '#16a34a'; };
        } else {
            btnRetrait.classList.add('border-amber-500', 'bg-amber-50', 'text-amber-700');
            btnSubmit.style.backgroundColor = '#d97706';
            btnSubmit.onmouseover = function() { this.style.backgroundColor = '#b45309'; };
            btnSubmit.onmouseout  = function() { this.style.backgroundColor = '#d97706'; };
        }

        // Show/hide categorie for depense only
        blocCateg.style.display = isDepense ? 'block' : 'none';
        if (categorieEl) categorieEl.required = isDepense;

        // Personnel only for salaires within depense
        var isSalaires = isDepense && categorieEl.value === 'salaires';
        blocPersonnel.style.display    = isSalaires ? 'block' : 'none';
        blocBeneficiaire.style.display = isSalaires ? 'none' : 'block';
    }

    radios.forEach(function(r) { r.addEventListener('change', applyTypeStyle); });

    if (categorieEl) {
        categorieEl.addEventListener('change', function() {
            var isSalaires = (this.value === 'salaires');
            blocPersonnel.style.display    = isSalaires ? 'block' : 'none';
            blocBeneficiaire.style.display = isSalaires ? 'none' : 'block';
        });
    }

    personnelSelect.addEventListener('change', function() {
        beneficiaireInput.value = this.value;
    });

    applyTypeStyle();
});
</script>
@endpush
