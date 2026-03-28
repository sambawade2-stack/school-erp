@extends('layouts.app')
@section('titre', 'Chambres')
@section('titre-page', 'Gestion des Chambres')

@section('contenu')

<div class="flex flex-wrap items-start justify-between gap-3 mb-5">
    <div class="flex flex-wrap items-center gap-3">
        <div class="rounded-xl p-4" style="background: #eff6ff; border: 1px solid #bfdbfe;">
            <p class="text-xs font-medium" style="color: #2563eb;">Total chambres</p>
            <p class="text-2xl font-bold mt-1" style="color: #1d4ed8;">{{ $chambres->count() }}</p>
        </div>
        <div class="rounded-xl p-4" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
            <p class="text-xs font-medium" style="color: #16a34a;">Places totales</p>
            <p class="text-2xl font-bold mt-1" style="color: #15803d;">{{ $chambres->sum('capacite') }}</p>
        </div>
        <div class="rounded-xl p-4" style="background: #fefce8; border: 1px solid #fde68a;">
            <p class="text-xs font-medium" style="color: #92400e;">Occupées</p>
            <p class="text-2xl font-bold mt-1" style="color: #92400e;">{{ $chambres->sum('actifs_count') }}</p>
        </div>
    </div>
    <a href="{{ route('chambres.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle chambre
    </a>
</div>

@if(session('succes'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('succes') }}</div>
@endif
@if($errors->any())
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
@endif

@if($chambres->isEmpty())
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
    </svg>
    <p class="text-gray-400 text-sm">Aucune chambre enregistrée.</p>
    <a href="{{ route('chambres.create') }}" class="mt-3 inline-block text-blue-600 text-sm hover:underline">Ajouter une chambre</a>
</div>
@else
<div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
    @foreach($chambres as $chambre)
    @php
        $occupation  = $chambre->actifs_count;
        $capacite    = $chambre->capacite;
        $pourcent    = $capacite > 0 ? min(100, round($occupation / $capacite * 100)) : 0;
        $pleine      = $occupation >= $capacite;
        $barColor    = $pleine ? '#dc2626' : ($pourcent >= 75 ? '#f59e0b' : '#16a34a');
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Chambre {{ $chambre->numero }}</h3>
                @if($chambre->description)
                <p class="text-xs text-gray-400 mt-0.5">{{ $chambre->description }}</p>
                @endif
            </div>
            @if($pleine)
            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Pleine</span>
            @else
            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $capacite - $occupation }} place(s)</span>
            @endif
        </div>

        {{-- Barre d'occupation --}}
        <div class="mb-3">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>{{ $occupation }} / {{ $capacite }} occupants</span>
                <span>{{ $pourcent }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all" style="width: {{ $pourcent }}%; background: {{ $barColor }};"></div>
            </div>
        </div>

        <div class="flex gap-2 justify-end mt-3 pt-3 border-t border-gray-50">
            <a href="{{ route('chambres.edit', $chambre) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <form action="{{ route('chambres.destroy', $chambre) }}" method="POST"
                  onsubmit="return confirm('Supprimer la chambre {{ $chambre->numero }} ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-red-500 hover:bg-red-50 rounded-lg text-xs font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
