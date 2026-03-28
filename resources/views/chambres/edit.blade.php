@extends('layouts.app')
@section('titre', 'Modifier Chambre')
@section('titre-page', 'Modifier la Chambre ' . $chambre->numero)

@section('contenu')

<x-btn-retour :href="route('chambres.index')" label="Retour aux chambres" breadcrumb="Modifier chambre" />

<div class="max-w-lg space-y-4">

    {{-- Formulaire modification --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <form action="{{ route('chambres.update', $chambre) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Numéro / Nom de la chambre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="numero" value="{{ old('numero', $chambre->numero) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Capacité (nombre de lits) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="capacite" value="{{ old('capacite', $chambre->capacite) }}" min="1" max="50" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description (optionnel)</label>
                <input type="text" name="description" value="{{ old('description', $chambre->description) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Enregistrer
                </button>
                <a href="{{ route('chambres.index') }}"
                   class="px-5 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    {{-- Élèves actuellement dans cette chambre --}}
    @if($chambre->internes->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">
            Élèves actuellement dans cette chambre
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $chambre->internes->count() }}</span>
        </h3>
        <ul class="divide-y divide-gray-50">
            @foreach($chambre->internes as $interne)
            <li class="py-2 flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-800">{{ $interne->etudiant->nom_complet }}</span>
                    <span class="text-xs text-gray-400 ml-2">{{ $interne->etudiant->classe?->nom ?? '—' }}</span>
                </div>
                <span class="text-xs text-gray-400">Depuis {{ $interne->date_entree->format('d/m/Y') }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

</div>

@endsection
