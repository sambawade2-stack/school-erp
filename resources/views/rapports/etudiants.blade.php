@extends('layouts.app')
@section('titre', 'Rapport Etudiants')
@section('titre-page', 'Rapport - Liste des Etudiants')

@section('contenu')

<x-btn-retour :href="route('rapports.index')" label="Retour aux rapports" breadcrumb="Liste des etudiants" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
    <form action="{{ route('rapports.etudiants') }}" method="GET" class="flex gap-3 items-center">
        <select name="classe_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Toutes les classes</option>
            @foreach($classes as $classe)
            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
            @endforeach
        </select>
        <select name="statut" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Tous statuts</option>
            <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actifs</option>
            <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactifs</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Filtrer</button>
        <a href="{{ route('rapports.etudiants', array_merge(request()->query(), ['format' => 'pdf'])) }}"
           class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
        </a>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b flex items-center justify-between">
        <h3 class="font-semibold text-gray-700">{{ $etudiants->count() }} etudiant(s)</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nom complet</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matricule</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sexe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Telephone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($etudiants as $i => $etudiant)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $etudiant->nom_complet }}</td>
                <td class="px-5 py-2.5 text-gray-400 font-mono text-xs">{{ $etudiant->matricule }}</td>
                <td class="px-5 py-2.5 text-gray-500">{{ $etudiant->classe?->nom ?? '—' }}</td>
                <td class="px-5 py-2.5 text-gray-500">{{ ucfirst($etudiant->sexe) }}</td>
                <td class="px-5 py-2.5 text-gray-500">{{ $etudiant->telephone ?? '—' }}</td>
                <td class="px-5 py-2.5">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $etudiant->statut === 'actif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ ucfirst($etudiant->statut) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
