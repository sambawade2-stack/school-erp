@extends('layouts.app')
@section('titre', 'Nouvel Devoir')
@section('titre-page', 'Creer un Devoir')

@section('contenu')

<x-btn-retour :href="route('examens.index')" label="Retour aux devoirs" breadcrumb="Nouvel devoir" />

<div class="max-w-xl">
<form action="{{ route('devoirs.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Intitule <span class="text-red-500">*</span></label>
            <input type="text" name="intitule" value="{{ old('intitule') }}" placeholder="ex: Controle de Mathematiques T1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Matiere <span class="text-red-500">*</span></label>
                <select name="matiere_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach($matieres as $matiere)
                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe <span class="text-red-500">*</span></label>
                <select name="classe_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach($classes->where('categorie', '!=', 'elementaire') as $classe)
                    <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Les classes élémentaires ne sont pas affichées (compositions uniquement).</p>
            </div>
        </div>
        @php $anneeActive = \App\Models\AnneeScolaire::libelleActif() ?? '2025-2026'; @endphp
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_devoir" value="{{ old('date_devoir') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Note max</label>
                <input type="number" name="note_max" value="{{ old('note_max', 20) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Trimestre</label>
                <select name="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="T1" {{ old('trimestre') === 'T1' ? 'selected' : '' }}>Trimestre 1</option>
                    <option value="T2" {{ old('trimestre') === 'T2' ? 'selected' : '' }}>Trimestre 2</option>
                    <option value="T3" {{ old('trimestre') === 'T3' ? 'selected' : '' }}>Trimestre 3</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Creer l'devoir</button>
        <a href="{{ route('examens.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
