@extends('layouts.app')
@section('titre', 'Modifier - ' . $classe->nom)
@section('titre-page', 'Modifier la Classe')

@section('contenu')

<x-btn-retour :href="route('classes.index')" label="Retour aux classes" />

<div class="max-w-xl">
<form action="{{ route('classes.update', $classe) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $classe->nom) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                <select name="categorie" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach(\App\Models\Classe::CATEGORIES as $val => $label)
                    <option value="{{ $val }}" {{ old('categorie', $classe->categorie) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau <span class="text-red-500">*</span></label>
            <input type="text" name="niveau" value="{{ old('niveau', $classe->niveau) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Capacite</label>
                <input type="number" name="capacite" value="{{ old('capacite', $classe->capacite) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Annee scolaire</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $classe->annee_scolaire) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Responsable de classe</label>
            <select name="enseignant_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">— Aucun responsable —</option>
                @foreach($enseignants as $enseignant)
                <option value="{{ $enseignant->id }}" {{ old('enseignant_id', $classe->enseignant_id) == $enseignant->id ? 'selected' : '' }}>
                    {{ $enseignant->nom_complet }} @if($enseignant->specialite)({{ $enseignant->specialite }})@endif
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('classes.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
