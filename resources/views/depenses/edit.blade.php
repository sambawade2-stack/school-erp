@extends('layouts.app')
@section('titre', 'Modifier Dépense')
@section('titre-page', 'Modifier une Dépense')

@section('contenu')

<x-btn-retour :href="route('depenses.index')" label="Retour aux dépenses" breadcrumb="Modifier dépense" />

<div class="max-w-xl">
<form action="{{ route('depenses.update', $depense) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bénéficiaire</label>
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
        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">Mettre à jour</button>
        <a href="{{ route('depenses.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
