@extends('layouts.app')
@section('titre', 'Nouvelle Dépense')
@section('titre-page', 'Enregistrer une Dépense')

@section('contenu')

<x-btn-retour :href="route('depenses.index')" label="Retour aux dépenses" breadcrumb="Nouvelle dépense" />

<div class="max-w-xl">
<form action="{{ route('depenses.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" id="categorie" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                <option value="{{ $p->nom_complet }}" data-type="{{ \App\Models\Enseignant::TYPES[$p->type] ?? ucfirst($p->type) }}" {{ old('beneficiaire') === $p->nom_complet ? 'selected' : '' }}>
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bénéficiaire</label>
                <input type="text" name="beneficiaire" id="beneficiaire" value="{{ old('beneficiaire') }}" placeholder="ex: Fournisseur X"
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
        <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors" style="background:#dc2626; color:#fff;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">Enregistrer</button>
        <a href="{{ route('depenses.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categorieSelect = document.getElementById('categorie');
    var blocPersonnel = document.getElementById('bloc-personnel');
    var personnelSelect = document.getElementById('personnel-select');
    var beneficiaireInput = document.getElementById('beneficiaire');
    var blocBeneficiaire = document.getElementById('bloc-beneficiaire');

    function togglePersonnel() {
        var isSalaires = categorieSelect.value === 'salaires';
        blocPersonnel.style.display = isSalaires ? 'block' : 'none';
        blocBeneficiaire.style.display = isSalaires ? 'none' : 'block';

        if (isSalaires) {
            // Set beneficiaire from personnel select
            if (personnelSelect.value) {
                beneficiaireInput.value = personnelSelect.value;
            }
        }
    }

    personnelSelect.addEventListener('change', function() {
        beneficiaireInput.value = this.value;
    });

    categorieSelect.addEventListener('change', togglePersonnel);

    // Init on page load
    togglePersonnel();
});
</script>
@endpush
