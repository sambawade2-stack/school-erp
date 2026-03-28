@extends('layouts.app')
@section('titre', 'Internat')
@section('titre-page', 'Gestion de l\'Internat')

@section('contenu')

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-4">
        <div class="rounded-xl p-4" style="background: #eff6ff; border: 1px solid #bfdbfe;">
            <p class="text-xs font-medium" style="color: #2563eb;">Internes actifs</p>
            <p class="text-2xl font-bold mt-1" style="color: #1d4ed8;">{{ $totalActifs }}</p>
        </div>
    </div>
    <a href="{{ route('chambres.index') }}"
       class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        Chambres
    </a>
    <a href="{{ route('internes.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel interne
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form action="{{ route('internes.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Rechercher un élève..."
               class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
        <select name="statut" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:180px;">
            <option value="">Tous les statuts</option>
            <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
            <option value="sorti" {{ request('statut') === 'sorti' ? 'selected' : '' }}>Sorti</option>
        </select>
        <select name="chambre_id" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:200px;">
            <option value="">Toutes les chambres</option>
            @foreach($chambres as $ch)
            <option value="{{ $ch->id }}" {{ request('chambre_id') == $ch->id ? 'selected' : '' }}>
                Chambre {{ $ch->numero }} ({{ $ch->actifs_count }}/{{ $ch->capacite }})
            </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        @if(request()->hasAny(['recherche', 'statut', 'chambre_id']))
        <a href="{{ route('internes.index') }}" class="px-4 py-2 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Effacer</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Élève</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Chambre</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date d'entrée</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Année</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($internes as $interne)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">
                    <a href="{{ route('etudiants.show', $interne->etudiant) }}" class="hover:text-blue-600">
                        {{ $interne->etudiant->nom_complet }}
                    </a>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $interne->etudiant->classe?->nom ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-600 font-medium">
                    {{ $interne->chambreObj ? 'Chambre ' . $interne->chambreObj->numero : '—' }}
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $interne->date_entree->format('d/m/Y') }}</td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $interne->annee_scolaire }}</td>
                <td class="px-5 py-3">
                    @if($interne->statut === 'actif')
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Actif</span>
                    @else
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Sorti</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('internes.edit', $interne) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('internes.destroy', $interne) }}" method="POST" onsubmit="return confirm('Supprimer cet interne ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun interne enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($internes->hasPages())
    <div class="px-5 py-4 border-t">{{ $internes->links() }}</div>
    @endif
</div>
@endsection
