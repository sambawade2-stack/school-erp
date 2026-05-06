@extends('layouts.app')
@section('titre', 'Modifier Dépense')
@section('titre-page', 'Modifier une Dépense')

@section('contenu')

<x-btn-retour :href="route('depenses.index')" label="Retour aux dépenses" breadcrumb="Modifier dépense" />

<div class="max-w-xl">
<form action="{{ route('depenses.update', $depense) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">

        {{-- Type de mouvement --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de mouvement <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-3 gap-2">
                @php $currentType = old('type_mouvement', $depense->type_mouvement ?? 'depense'); @endphp
                <label id="btn-depense" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $currentType === 'depense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:border-red-300' }}">
                    <input type="radio" name="type_mouvement" value="depense" {{ $currentType === 'depense' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Dépense
                </label>
                <label id="btn-depot" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $currentType === 'depot_banque' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-green-300' }}">
                    <input type="radio" name="type_mouvement" value="depot_banque" {{ $currentType === 'depot_banque' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Dépôt banque
                </label>
                <label id="btn-retrait" class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer text-sm font-medium transition-all
                    {{ $currentType === 'retrait_banque' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-600 hover:border-amber-300' }}">
                    <input type="radio" name="type_mouvement" value="retrait_banque" {{ $currentType === 'retrait_banque' ? 'checked' : '' }} class="sr-only">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Retrait banque
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Libellé <span class="text-red-500">*</span></label>
            <input type="text" name="libelle" value="{{ old('libelle', $depense->libelle) }}"
                   required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant (XOF) <span class="text-red-500">*</span></label>
                <input type="number" name="montant" value="{{ old('montant', $depense->montant) }}" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div id="bloc-categorie">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" id="categorie" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="fournitures" {{ old('categorie', $depense->categorie) === 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                    <option value="salaires" {{ old('categorie', $depense->categorie) === 'salaires' ? 'selected' : '' }}>Salaires</option>
                    <option value="maintenance" {{ old('categorie', $depense->categorie) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="transport" {{ old('categorie', $depense->categorie) === 'transport' ? 'selected' : '' }}>Transport</option>
                    <option value="alimentation" {{ old('categorie', $depense->categorie) === 'alimentation' ? 'selected' : '' }}>Alimentation</option>
                    <option value="autre" {{ old('categorie', $depense->categorie) === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_depense" value="{{ old('date_depense', $depense->date_depense->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bénéficiaire / Référence</label>
                <input type="text" name="beneficiaire" value="{{ old('beneficiaire', $depense->beneficiaire) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $depense->annee_scolaire) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description', $depense->description) }}</textarea>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" id="btn-submit" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors" style="background:#dc2626; color:#fff;">Mettre à jour</button>
        <a href="{{ route('depenses.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var radios     = document.querySelectorAll('input[name="type_mouvement"]');
    var btnDepense = document.getElementById('btn-depense');
    var btnDepot   = document.getElementById('btn-depot');
    var btnRetrait = document.getElementById('btn-retrait');
    var blocCateg  = document.getElementById('bloc-categorie');
    var categorieEl = document.getElementById('categorie');
    var btnSubmit  = document.getElementById('btn-submit');

    function applyTypeStyle() {
        var type = document.querySelector('input[name="type_mouvement"]:checked')?.value ?? 'depense';
        var isDepense = (type === 'depense');
        var isDepot   = (type === 'depot_banque');

        // Reset classes
        [btnDepense, btnDepot, btnRetrait].forEach(function(el) {
            el.classList.remove(
                'border-red-500','bg-red-50','text-red-700',
                'border-green-500','bg-green-50','text-green-700',
                'border-amber-500','bg-amber-50','text-amber-700'
            );
            el.classList.add('border-gray-200','text-gray-600');
        });

        if (isDepense) {
            btnDepense.classList.remove('border-gray-200','text-gray-600');
            btnDepense.classList.add('border-red-500','bg-red-50','text-red-700');
            btnSubmit.style.backgroundColor = '#dc2626';
        } else if (isDepot) {
            btnDepot.classList.remove('border-gray-200','text-gray-600');
            btnDepot.classList.add('border-green-500','bg-green-50','text-green-700');
            btnSubmit.style.backgroundColor = '#16a34a';
        } else {
            btnRetrait.classList.remove('border-gray-200','text-gray-600');
            btnRetrait.classList.add('border-amber-500','bg-amber-50','text-amber-700');
            btnSubmit.style.backgroundColor = '#d97706';
        }

        blocCateg.style.display = isDepense ? 'block' : 'none';
        if (categorieEl) categorieEl.required = isDepense;
    }

    radios.forEach(function(r) { r.addEventListener('change', applyTypeStyle); });
    applyTypeStyle();
});
</script>
@endpush
