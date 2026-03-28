@extends('layouts.app')
@section('titre', 'Devoirs')
@section('titre-page', 'Gestion des Devoirs')

@section('contenu')
<div class="flex items-center justify-between mb-5">
    <form action="{{ route('devoirs.index') }}" method="GET" class="flex gap-2">
        <select name="classe_id" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
            <option value="">Toutes les classes</option>
            @foreach($classes as $classe)
            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('devoirs.create') }}" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau devoir
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Devoir</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matiere</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Trimestre</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($devoirs as $devoir)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $devoir->intitule }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $devoir->matiere->nom }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $devoir->classe->nom }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $devoir->date_devoir->format('d/m/Y') }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $devoir->trimestre }}</span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $devoir->notes()->count() }} notes</td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('devoirs.show', $devoir) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('devoirs.edit', $devoir) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>
                        <form action="{{ route('devoirs.destroy', $devoir) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun devoir enregistre.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($devoirs->hasPages())
    <div class="px-5 py-4 border-t">{{ $devoirs->links() }}</div>
    @endif
</div>
@endsection
