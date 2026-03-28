@extends('layouts.app')
@section('titre', 'Nouvelle Chambre')
@section('titre-page', 'Ajouter une Chambre')

@section('contenu')

<x-btn-retour :href="route('chambres.index')" label="Retour aux chambres" breadcrumb="Nouvelle chambre" />

<div class="max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
        @endif

        <form action="{{ route('chambres.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Numéro / Nom de la chambre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="numero" value="{{ old('numero') }}" required
                       placeholder="ex: 101, A-02, Dortoir 1…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Capacité (nombre de lits) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="capacite" value="{{ old('capacite', 1) }}" min="1" max="50" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description (optionnel)</label>
                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder="Bâtiment A, 1er étage…"
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
</div>

@endsection
