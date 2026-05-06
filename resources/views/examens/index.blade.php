@extends('layouts.app')
@section('titre', 'Examens & Notes')
@section('titre-page', 'Examens et Notes')

@section('contenu')

@php
    $ongletDefaut = request('tab', 'compositions');
    if (!in_array($ongletDefaut, ['examens', 'devoirs', 'compositions'])) {
        $ongletDefaut = 'compositions';
    }
@endphp

<div x-data="{ onglet: '{{ $ongletDefaut }}' }">

    {{-- Barre de navigation par onglets --}}
    <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 mb-0 justify-between">

        {{-- Filtres (à gauche) --}}
        <form action="{{ route('examens.index') }}" method="GET" class="flex flex-wrap items-center gap-2 mr-4">
            <input type="hidden" name="tab" :value="onglet">
            <select name="classe_id" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    style="min-width:180px;">
                <option value="">Toutes les classes</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
            <select name="trimestre" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    style="min-width:160px;">
                <option value="">Toutes les périodes</option>
                <optgroup label="Trimestres">
                    @foreach(['T1' => 'Trimestre 1', 'T2' => 'Trimestre 2', 'T3' => 'Trimestre 3'] as $val => $label)
                    <option value="{{ $val }}" {{ request('trimestre', $trimestreActuel) === $val ? 'selected' : '' }}>
                        {{ $label }}@if($val === $trimestreActuel) ★@endif
                    </option>
                    @endforeach
                </optgroup>
                <optgroup label="Semestres">
                    @foreach(['S1' => 'Semestre 1', 'S2' => 'Semestre 2', 'S3' => 'Semestre 3'] as $val => $label)
                    <option value="{{ $val }}" {{ request('trimestre', $trimestreActuel) === $val ? 'selected' : '' }}>
                        {{ $label }}@if($val === $trimestreActuel) ★@endif
                    </option>
                    @endforeach
                </optgroup>
            </select>
        </form>

        {{-- Onglets : Compositions → Examens → Devoirs --}}
        <button @click="onglet='compositions'"
            :class="onglet==='compositions'
                ? 'border-b-2 border-amber-500 text-amber-600 bg-amber-50/50'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-3 text-sm font-medium transition-all -mb-px rounded-t-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Compositions
            <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold"
                :class="onglet==='compositions' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'">
                {{ $compositions->count() }}
            </span>
        </button>

        <button @click="onglet='examens'"
            :class="onglet==='examens'
                ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50/50'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-3 text-sm font-medium transition-all -mb-px rounded-t-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Examens
            <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold"
                :class="onglet==='examens' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'">
                {{ $examens->count() }}
            </span>
        </button>

        <button @click="onglet='devoirs'"
            :class="onglet==='devoirs'
                ? 'border-b-2 border-green-600 text-green-600 bg-green-50/50'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-3 text-sm font-medium transition-all -mb-px rounded-t-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Devoirs
            <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold"
                :class="onglet==='devoirs' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                {{ $devoirs->count() }}
            </span>
        </button>

        {{-- Bouton d'ajout contextuel selon l'onglet actif --}}
        <div class="ml-auto pl-3 flex items-center">
            <a x-show="onglet==='compositions'" href="{{ route('compositions.create') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 text-white rounded-lg text-xs font-medium hover:opacity-90 transition-opacity"
               style="background:#d97706;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle composition
            </a>
            <a x-show="onglet==='examens'" href="{{ route('examens.create') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvel examen
            </a>
            <a x-show="onglet==='devoirs'" href="{{ route('devoirs.create') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau devoir
            </a>
        </div>
    </div>

    {{-- ═══════════════ EXAMENS ═══════════════ --}}
    <div x-show="onglet==='examens'" x-cloak class="bg-white rounded-b-xl rounded-tr-xl shadow-sm border border-gray-100 border-t-0">
        {{-- En-tête de section --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-blue-50/40">
            <div>
                <h3 class="font-semibold text-blue-800 text-base">Examens</h3>
                <p class="text-xs text-blue-500 mt-0.5">Épreuves formelles, contrôles en salle</p>
            </div>
            <a href="{{ route('examens.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvel examen
            </a>
        </div>
        {{-- Table --}}
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Examen</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Période</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($examens as $examen)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $examen->intitule }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $examen->matiere->nom }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $examen->classe->nom }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $examen->date_examen->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $examen->trimestre }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $examen->notes()->count() }} notes</td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('examens.show', $examen) }}" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('notes.saisir', $examen) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Saisir notes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('examens.edit', $examen) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('examens.destroy', $examen) }}" method="POST" onsubmit="return confirm('Supprimer cet examen ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="text-gray-300 mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm">Aucun examen enregistré</p>
                        <a href="{{ route('examens.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Créer le premier examen
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    {{-- ═══════════════ DEVOIRS ═══════════════ --}}
    <div x-show="onglet==='devoirs'" x-cloak class="bg-white rounded-b-xl rounded-tr-xl shadow-sm border border-gray-100 border-t-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-green-50/40">
            <div>
                <h3 class="font-semibold text-green-800 text-base">Devoirs</h3>
                <p class="text-xs text-green-500 mt-0.5">Travaux à la maison, contrôles continus</p>
            </div>
            <a href="{{ route('devoirs.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau devoir
            </a>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Devoir</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Période</th>
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
                            <a href="{{ route('devoirs.show', $devoir) }}" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('notes.saisir.devoir', $devoir) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Saisir notes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('devoirs.edit', $devoir) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('devoirs.destroy', $devoir) }}" method="POST" onsubmit="return confirm('Supprimer ce devoir ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="text-gray-300 mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm">Aucun devoir enregistré</p>
                        <a href="{{ route('devoirs.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Créer le premier devoir
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    {{-- ═══════════════ COMPOSITIONS ═══════════════ --}}
    <div x-show="onglet==='compositions'" x-cloak class="bg-white rounded-b-xl rounded-tr-xl shadow-sm border border-gray-100 border-t-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100" style="background: #fffbeb;">
            <div>
                <h3 class="font-semibold text-base" style="color:#92400e;">Compositions</h3>
                <p class="text-xs mt-0.5" style="color:#d97706;">Épreuves de fin de période (trimestre / semestre)</p>
            </div>
            <a href="{{ route('compositions.create') }}"
               class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium hover:opacity-90 transition-opacity"
               style="background:#d97706;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle composition
            </a>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Composition</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Période</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($compositions as $composition)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $composition->intitule }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $composition->matiere->nom }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $composition->classe->nom }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $composition->date_composition->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $composition->trimestre }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $composition->notes()->count() }} notes</td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('compositions.show', $composition) }}" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('notes.saisir.composition', $composition) }}" class="p-1.5 hover:bg-amber-50 rounded-lg" style="color:#d97706;" title="Saisir notes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('compositions.edit', $composition) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('compositions.destroy', $composition) }}" method="POST" onsubmit="return confirm('Supprimer cette composition ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="text-gray-300 mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm">Aucune composition enregistrée</p>
                        <a href="{{ route('compositions.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 text-white rounded-lg text-xs font-medium hover:opacity-90" style="background:#d97706;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Créer la première composition
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

</div>

{{-- Préserver l'onglet actif lors du filtrage par classe --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form[action*="examens"]');
    forms.forEach(function (form) {
        const input = form.querySelector('input[name="tab"]');
        if (input) {
            // Sync avec Alpine.js
            const container = document.querySelector('[x-data]');
            if (container && container._x_dataStack) {
                const data = container._x_dataStack[0];
                if (data) input.value = data.onglet;
            }
            form.addEventListener('change', function () {
                if (input) {
                    const container = document.querySelector('[x-data]');
                    if (container && container._x_dataStack) {
                        const data = container._x_dataStack[0];
                        if (data) input.value = data.onglet;
                    }
                }
            });
        }
    });
});
</script>
@endsection
