@extends('layouts.app')
@section('titre', 'Classes')
@section('titre-page', 'Gestion des Classes')

@section('contenu')

<div class="flex justify-end mb-5">
    <a href="{{ route('classes.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle classe
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($classes as $classe)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">{{ $classe->nom }}</h3>
                <p class="text-sm text-gray-500">{{ $classe->niveau }}</p>
            </div>
            <span class="text-2xl font-bold text-blue-600">{{ $classe->etudiants_count }}</span>
        </div>
        <div class="text-xs text-gray-400 space-y-1 mb-4">
            <div class="flex justify-between">
                <span>Capacite max.</span>
                <span class="font-medium">{{ $classe->capacite }} places</span>
            </div>
            <div class="flex justify-between">
                <span>Annee scolaire</span>
                <span class="font-medium">{{ $classe->annee_scolaire }}</span>
            </div>
            <div class="flex justify-between">
                <span>Responsable</span>
                <span class="font-medium text-gray-600">{{ $classe->responsable?->nom_complet ?? '—' }}</span>
            </div>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min(100, ($classe->etudiants_count / $classe->capacite) * 100) }}%"></div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('classes.show', $classe) }}" class="flex-1 text-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm hover:bg-blue-100 transition-colors">Voir</a>
            <a href="{{ route('classes.edit', $classe) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-sm hover:bg-gray-100 transition-colors">Modifier</a>
            <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Supprimer cette classe ?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-500 rounded-lg text-sm hover:bg-red-100 transition-colors">Sup.</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-400">Aucune classe enregistree.</div>
    @endforelse
</div>
@endsection
