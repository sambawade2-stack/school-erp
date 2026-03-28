@extends('layouts.app')
@section('titre', 'Modifier - ' . $section->nom)
@section('titre-page', 'Modifier une Section')

@section('contenu')

<x-btn-retour :href="route('sections.index')" label="Retour aux sections" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
<form action="{{ route('sections.update', $section) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')

    @if($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de la section <span class="text-red-500">*</span></label>
        <input type="text" name="nom" value="{{ old('nom', $section->nom) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Couleur du badge</label>
        <div class="flex items-center gap-3">
            <input type="color" name="couleur" value="{{ old('couleur', $section->couleur) }}" class="w-10 h-10 rounded border border-gray-300 cursor-pointer">
            <span style="background-color: {{ $section->couleur }}20; color: {{ $section->couleur }};"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                <span style="background-color: {{ $section->couleur }};" class="w-2 h-2 rounded-full mr-1.5"></span>
                {{ $section->nom }}
            </span>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau (optionnel)</label>
        <select name="niveau" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Tous niveaux</option>
            <option value="elementaire" {{ old('niveau', $section->niveau) === 'elementaire' ? 'selected' : '' }}>Élémentaire</option>
            <option value="college" {{ old('niveau', $section->niveau) === 'college' ? 'selected' : '' }}>Collège</option>
            <option value="lycee" {{ old('niveau', $section->niveau) === 'lycee' ? 'selected' : '' }}>Lycée</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ordre d'affichage</label>
        <input type="number" name="ordre" value="{{ old('ordre', $section->ordre) }}" min="0"
               class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            Enregistrer
        </button>
        <a href="{{ route('sections.index') }}" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm text-center hover:bg-gray-50 transition-colors">
            Annuler
        </a>
    </div>
</form>
</div>

@endsection
