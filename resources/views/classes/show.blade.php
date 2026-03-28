@extends('layouts.app')
@section('titre', 'Classe ' . $classe->nom)
@section('titre-page', 'Classe ' . $classe->nom)

@section('contenu')

<x-btn-retour :href="route('classes.index')" label="Retour aux classes" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-blue-600 text-2xl mb-1">{{ $classe->nom }}</h3>
            <p class="text-gray-500 text-sm mb-4">{{ $classe->niveau }} — {{ $classe->annee_scolaire }}</p>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2"><span class="text-gray-400">Eleves actifs</span><span class="font-bold text-gray-800">{{ $classe->etudiants->count() }}</span></div>
                <div class="flex justify-between border-b pb-2"><span class="text-gray-400">Capacite max</span><span class="font-medium text-gray-700">{{ $classe->capacite }}</span></div>
                <div class="flex justify-between border-b pb-2"><span class="text-gray-400">Matieres</span><span class="font-medium text-gray-700">{{ $classe->matieres->count() }}</span></div>
                <div class="flex justify-between pb-2">
                    <span class="text-gray-400">Responsable</span>
                    @if($classe->responsable)
                    <a href="{{ route('enseignants.show', $classe->responsable) }}" class="font-medium text-blue-600 hover:underline">
                        {{ $classe->responsable->nom_complet }}
                    </a>
                    @else
                    <span class="text-gray-400 italic">Non assigné</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('classes.edit', $classe) }}" class="mt-4 block text-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">Modifier</a>
        </div>
    </div>
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">Liste des eleves</h3>
                <a href="{{ route('etudiants.create') }}" class="text-xs px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Ajouter</a>
            </div>
            @if($classe->etudiants->count())
            <table class="w-full text-sm">
                <thead><tr class="text-gray-400 text-xs border-b">
                    <th class="pb-2 text-left">Nom</th>
                    <th class="pb-2 text-left">Matricule</th>
                    <th class="pb-2 text-left">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($classe->etudiants as $etudiant)
                    <tr>
                        <td class="py-2 font-medium text-gray-700">{{ $etudiant->nom_complet }}</td>
                        <td class="py-2 text-gray-400 font-mono text-xs">{{ $etudiant->matricule }}</td>
                        <td class="py-2"><a href="{{ route('etudiants.show', $etudiant) }}" class="text-blue-600 hover:underline text-xs">Voir profil</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Aucun eleve dans cette classe</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Matieres</h3>
            @if($classe->matieres->count())
            <div class="divide-y divide-gray-50">
                @foreach($classe->matieres as $matiere)
                <div class="flex items-center justify-between py-2">
                    <span class="font-medium text-gray-700">{{ $matiere->nom }}</span>
                    <span class="text-sm text-gray-500">{{ $matiere->enseignant?->nom_complet ?? '—' }}</span>
                    <span class="text-xs text-gray-400">Coef. {{ $matiere->coefficient }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Aucune matiere assignee</p>
            @endif
        </div>
    </div>
</div>
@endsection
