@extends('layouts.app')
@section('titre', 'Personnel')
@section('titre-page', 'Gestion du Personnel')

@section('contenu')

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form action="{{ route('enseignants.index') }}" method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Rechercher..."
               class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
        <select name="type" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:200px;">
            <option value="">Tous les types</option>
            @foreach(\App\Models\Enseignant::TYPES as $key => $label)
            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        @if(request()->hasAny(['recherche', 'type']))
        <a href="{{ route('enseignants.index') }}" class="px-4 py-2 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Effacer</a>
        @endif
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('enseignants.export.pdf', ['type' => request('type')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <a href="{{ route('enseignants.export.csv', ['type' => request('type')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            CSV
        </a>
        <a href="{{ route('enseignants.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter du personnel
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Photo</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nom complet</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Spécialité</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Téléphone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($enseignants as $enseignant)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    @if($enseignant->photo)
                    <img src="{{ $enseignant->photo_url }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-gray-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-200" style="display:none;">
                        {{ strtoupper(substr($enseignant->prenom, 0, 1) . substr($enseignant->nom, 0, 1)) }}
                    </div>
                    @else
                    <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-200">
                        {{ strtoupper(substr($enseignant->prenom, 0, 1) . substr($enseignant->nom, 0, 1)) }}
                    </div>
                    @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $enseignant->nom_complet }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: #eff6ff; color: #2563eb;">
                        {{ \App\Models\Enseignant::TYPES[$enseignant->type] ?? ucfirst($enseignant->type) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $enseignant->specialite ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $enseignant->telephone ?? '—' }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 {{ $enseignant->statut === 'actif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ ucfirst($enseignant->statut) }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('enseignants.show', $enseignant) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Voir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('enseignants.edit', $enseignant) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('enseignants.destroy', $enseignant) }}" method="POST" onsubmit="return confirm('Supprimer ce personnel ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun personnel enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($enseignants->hasPages())
    <div class="px-5 py-4 border-t">{{ $enseignants->links() }}</div>
    @endif
</div>
@endsection
