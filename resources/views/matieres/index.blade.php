@extends('layouts.app')
@section('titre', 'Matieres')
@section('titre-page', 'Gestion des Matieres')

@section('contenu')
<div class="flex justify-end mb-5">
    <a href="{{ route('matieres.create') }}" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter une matiere
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matiere</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Section</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Coefficient</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Enseignant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($matieres as $matiere)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $matiere->nom }}</td>
                <td class="px-5 py-3">
                    @php $secObj = \App\Models\Section::where('nom', $matiere->section)->first(); @endphp
                    <span style="background-color: {{ ($secObj->couleur ?? '#6b7280') . '20' }}; color: {{ $secObj->couleur ?? '#6b7280' }};"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                        <span style="background-color: {{ $secObj->couleur ?? '#6b7280' }};" class="w-2 h-2 rounded-full mr-1.5"></span>
                        {{ $matiere->section }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $matiere->code ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $matiere->coefficient }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $matiere->enseignant?->nom_complet ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-500">
                    @forelse($matiere->classes as $cl)
                        <span class="inline-block text-xs bg-gray-100 text-gray-600 rounded px-1.5 py-0.5 mr-0.5">{{ $cl->nom }}</span>
                    @empty
                        <span class="text-gray-300 text-xs italic">Toutes</span>
                    @endforelse
                </td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('matieres.edit', $matiere) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Supprimer cette matiere ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucune matiere enregistree.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($matieres->hasPages())
    <div class="px-5 py-4 border-t">{{ $matieres->links() }}</div>
    @endif
</div>
@endsection
