@extends('layouts.app')
@section('titre', 'Élèves Impayés')
@section('titre-page', 'Liste des Impayés')

@section('contenu')

<div class="mb-6">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour au tableau de bord
    </a>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('impayes') }}" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ $anneeScolaire }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-40">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Période</label>
            <select name="trimestre" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-32">
                <optgroup label="Trimestres">
                    <option value="T1" {{ $trimestreCourant === 'T1' ? 'selected' : '' }}>T1</option>
                    <option value="T2" {{ $trimestreCourant === 'T2' ? 'selected' : '' }}>T2</option>
                    <option value="T3" {{ $trimestreCourant === 'T3' ? 'selected' : '' }}>T3</option>
                </optgroup>
                <optgroup label="Semestres">
                    <option value="S1" {{ $trimestreCourant === 'S1' ? 'selected' : '' }}>S1</option>
                    <option value="S2" {{ $trimestreCourant === 'S2' ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ $trimestreCourant === 'S3' ? 'selected' : '' }}>S3</option>
                </optgroup>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe</label>
            <select name="classe_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-48">
                <option value="">Toutes les classes</option>
                <option value="sans_classe" {{ request('classe_id') === 'sans_classe' ? 'selected' : '' }}>Sans classe</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id && request('classe_id') !== 'sans_classe' ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            Filtrer
        </button>
    </form>
</div>

{{-- Résumé --}}
<div class="flex items-center gap-3 mb-4">
    <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        {{ $etudiants->count() }} élève(s) impayé(s) — {{ $trimestreCourant }} · {{ $anneeScolaire }}
    </span>
</div>

{{-- Tableau --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-gray-500 text-xs uppercase tracking-wider">
                <th class="px-5 py-3 font-medium">#</th>
                <th class="px-5 py-3 font-medium">Nom complet</th>
                <th class="px-5 py-3 font-medium">Classe</th>
                <th class="px-5 py-3 font-medium">Montant à payer</th>
                <th class="px-5 py-3 font-medium">Téléphone</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($etudiants as $i => $etudiant)
            @php
                $niveau = $etudiant->classe?->categorie ?? 'autre';
                $montant = $tarifsScolarite[$niveau] ?? null;
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('etudiants.show', $etudiant) }}" class="font-medium text-gray-800 hover:text-blue-600">
                        {{ $etudiant->nom_complet }}
                    </a>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $etudiant->classe?->nom ?? '—' }}</td>
                <td class="px-5 py-3">
                    @if($montant)
                    <span class="font-semibold text-red-600">{{ number_format($montant, 0, ',', ' ') }} XOF</span>
                    @else
                    <span class="text-gray-400 text-xs">Non défini</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-400">{{ $etudiant->telephone ?? '—' }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('paiements.create', ['etudiant_id' => $etudiant->id]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Payer
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Aucun impayé pour cette période.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
