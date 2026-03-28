@extends('layouts.app')
@section('titre', 'Nouvelle Classe')
@section('titre-page', 'Creer une Classe')

@section('contenu')

<x-btn-retour :href="route('classes.index')" label="Retour aux classes" breadcrumb="Nouvelle classe" />

<div class="max-w-xl">
<form action="{{ route('classes.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de la classe <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" placeholder="ex: 5eme A" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach(\App\Models\Classe::CATEGORIES as $val => $label)
                    <option value="{{ $val }}" {{ old('categorie') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau <span class="text-red-500">*</span></label>
            <input type="text" name="niveau" value="{{ old('niveau') }}" placeholder="ex: 5ème" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Capacite <span class="text-red-500">*</span></label>
                <input type="number" name="capacite" value="{{ old('capacite', 30) }}" min="1" max="100" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Annee scolaire <span class="text-red-500">*</span></label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive?->libelle ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Responsable de classe</label>
            <select name="enseignant_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">— Aucun responsable —</option>
                @foreach($enseignants as $enseignant)
                <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                    {{ $enseignant->nom_complet }} @if($enseignant->specialite)({{ $enseignant->specialite }})@endif
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description') }}</textarea>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Creer la classe</button>
        <a href="{{ route('classes.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
