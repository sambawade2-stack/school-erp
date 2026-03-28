@extends('layouts.app')
@section('titre', 'Modifier Interne')
@section('titre-page', 'Modifier un Interne')

@section('contenu')

<x-btn-retour :href="route('internes.index')" label="Retour à l'internat" breadcrumb="Modifier interne" />

<div class="max-w-xl">
<form action="{{ route('internes.update', $interne) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Élève</label>
            <input type="text" value="{{ $interne->etudiant->nom_complet }}" disabled
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Chambre</label>
                <select name="chambre_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune --</option>
                    @foreach($chambres as $ch)
                    <option value="{{ $ch->id }}"
                            {{ old('chambre_id', $interne->chambre_id) == $ch->id ? 'selected' : '' }}
                            {{ ($ch->pleine && $interne->chambre_id != $ch->id) ? 'disabled' : '' }}>
                        Chambre {{ $ch->numero }}
                        ({{ $ch->actifs_count }}/{{ $ch->capacite }})
                        {{ ($ch->pleine && $interne->chambre_id != $ch->id) ? '— Pleine' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="actif" {{ old('statut', $interne->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="sorti" {{ old('statut', $interne->statut) === 'sorti' ? 'selected' : '' }}>Sorti</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'entrée <span class="text-red-500">*</span></label>
                <input type="date" name="date_entree" value="{{ old('date_entree', $interne->date_entree->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de sortie</label>
                <input type="date" name="date_sortie" value="{{ old('date_sortie', $interne->date_sortie?->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $interne->annee_scolaire) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Remarque</label>
            <textarea name="remarque" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('remarque', $interne->remarque) }}</textarea>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Mettre à jour</button>
        <a href="{{ route('internes.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
