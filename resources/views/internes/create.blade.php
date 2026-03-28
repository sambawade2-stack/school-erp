@extends('layouts.app')
@section('titre', 'Nouvel Interne')
@section('titre-page', 'Enregistrer un Interne')

@section('contenu')

<x-btn-retour :href="route('internes.index')" label="Retour à l'internat" breadcrumb="Nouvel interne" />

<div class="max-w-xl">
<form action="{{ route('internes.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Élève <span class="text-red-500">*</span></label>
            <select name="etudiant_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choisir un élève --</option>
                @foreach($etudiants as $etudiant)
                <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                    {{ $etudiant->nom_complet }} ({{ $etudiant->classe?->nom ?? 'Sans classe' }})
                </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Chambre</label>
                <select name="chambre_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune --</option>
                    @foreach($chambres as $ch)
                    <option value="{{ $ch->id }}"
                            {{ old('chambre_id') == $ch->id ? 'selected' : '' }}
                            {{ $ch->pleine ? 'disabled' : '' }}>
                        Chambre {{ $ch->numero }}
                        ({{ $ch->actifs_count }}/{{ $ch->capacite }})
                        {{ $ch->pleine ? '— Pleine' : '' }}
                    </option>
                    @endforeach
                </select>
                @if($chambres->where('pleine', false)->isEmpty())
                <p class="text-xs text-amber-600 mt-1">Toutes les chambres sont pleines. <a href="{{ route('chambres.create') }}" class="underline">Ajouter une chambre</a></p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'entrée <span class="text-red-500">*</span></label>
                <input type="date" name="date_entree" value="{{ old('date_entree', date('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Remarque</label>
            <textarea name="remarque" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('remarque') }}</textarea>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Enregistrer</button>
        <a href="{{ route('internes.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
