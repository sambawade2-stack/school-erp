@extends('layouts.app')
@section('titre', 'Modifier - ' . $matiere->nom)
@section('titre-page', 'Modifier une Matiere')

@section('contenu')

<x-btn-retour :href="route('matieres.index')" label="Retour aux matieres" />

<div class="max-w-xl">
<form action="{{ route('matieres.update', $matiere) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $matiere->nom) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Code</label>
                <input type="text" name="code" value="{{ old('code', $matiere->code) }}" placeholder="ex: MATH" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Coefficient <span class="text-red-500">*</span></label>
                <input type="number" name="coefficient" value="{{ old('coefficient', $matiere->coefficient) }}" step="0.5" min="0.5" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Section <span class="text-red-500">*</span></label>
                <select name="section" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Sélectionner --</option>
                    @foreach(\App\Models\Section::ordonnes()->get() as $sec)
                    <option value="{{ $sec->nom }}" {{ old('section', $matiere->section) == $sec->nom ? 'selected' : '' }}>
                        {{ $sec->nom }}
                        @if($sec->niveau) ({{ match($sec->niveau) { 'elementaire'=>'Élém.', 'college'=>'Collège', 'lycee'=>'Lycée', default=>'' } }}) @endif
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Enseignant</label>
            <select name="enseignant_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Aucun --</option>
                @foreach($enseignants as $ens)
                <option value="{{ $ens->id }}" {{ old('enseignant_id', $matiere->enseignant_id) == $ens->id ? 'selected' : '' }}>{{ $ens->nom_complet }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe</label>
            <select name="classe_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Toutes les classes --</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ old('classe_id', $matiere->classe_id) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('matieres.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
