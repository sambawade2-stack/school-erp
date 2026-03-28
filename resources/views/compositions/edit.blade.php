@extends('layouts.app')
@section('titre', 'Modifier Composition')
@section('titre-page', 'Modifier un Composition')

@section('contenu')

<x-btn-retour :href="route('compositions.index')" label="Retour aux compositions" />

<div class="max-w-xl">
<form action="{{ route('compositions.update', $composition) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Intitule <span class="text-red-500">*</span></label>
            <input type="text" name="intitule" value="{{ old('intitule', $composition->intitule) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Matiere <span class="text-red-500">*</span></label>
                <select name="matiere_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($matieres as $matiere)
                    <option value="{{ $matiere->id }}" {{ old('matiere_id', $composition->matiere_id) == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe <span class="text-red-500">*</span></label>
                <select name="classe_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" {{ old('classe_id', $composition->classe_id) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                <input type="date" name="date_composition" value="{{ old('date_composition', $composition->date_composition->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Note max</label>
                <input type="number" name="note_max" value="{{ old('note_max', $composition->note_max) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Période</label>
                <select name="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <optgroup label="Trimestres">
                        @foreach(['T1' => 'Trimestre 1', 'T2' => 'Trimestre 2', 'T3' => 'Trimestre 3'] as $val => $label)
                        <option value="{{ $val }}" {{ old('trimestre', $composition->trimestre) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Semestres (Élémentaire)">
                        @foreach(['S1' => 'Semestre 1', 'S2' => 'Semestre 2', 'S3' => 'Semestre 3'] as $val => $label)
                        <option value="{{ $val }}" {{ old('trimestre', $composition->trimestre) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $composition->annee_scolaire) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" style="background:#d97706;" class="px-6 py-2.5 text-white rounded-lg text-sm font-medium hover:opacity-90">Enregistrer</button>
        <a href="{{ route('examens.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
