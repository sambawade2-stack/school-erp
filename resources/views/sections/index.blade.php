@extends('layouts.app')
@section('titre', 'Sections / Domaines')
@section('titre-page', 'Gestion des Sections')

@section('contenu')

<div class="flex flex-wrap justify-between items-center gap-3 mb-5">
    <p class="text-sm text-gray-500">Les sections permettent de regrouper les matières par domaine sur les bulletins.</p>
    <a href="{{ route('sections.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter une section
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ordre</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Section</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Couleur</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Niveau</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Matières</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($sections as $section)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 text-gray-400 font-mono text-xs">{{ $section->ordre }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <span style="background-color: {{ $section->couleur }}20; color: {{ $section->couleur }};"
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                            <span style="background-color: {{ $section->couleur }};" class="w-2 h-2 rounded-full mr-1.5"></span>
                            {{ $section->nom }}
                        </span>
                    </div>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <span style="background-color: {{ $section->couleur }};" class="w-5 h-5 rounded border border-gray-200 inline-block"></span>
                        <span class="text-xs text-gray-400 font-mono">{{ $section->couleur }}</span>
                    </div>
                </td>
                <td class="px-5 py-3 text-gray-600">
                    @if($section->niveau)
                        {{ match($section->niveau) {
                            'elementaire' => 'Élémentaire',
                            'college' => 'Collège',
                            'lycee' => 'Lycée',
                            default => ucfirst($section->niveau)
                        } }}
                    @else
                        <span class="text-gray-400">Tous niveaux</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        {{ $section->matieres_count }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('sections.edit', $section) }}"
                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">Modifier</a>
                        @if($section->matieres_count === 0)
                        <form action="{{ route('sections.destroy', $section) }}" method="POST" class="inline"
                              onsubmit="return confirm('Supprimer cette section ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Supprimer</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune section créée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection
