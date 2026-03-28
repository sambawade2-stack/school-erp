@extends('layouts.app')
@section('titre', $devoir->intitule)
@section('titre-page', 'Detail de l\'Devoir')

@section('contenu')

<x-btn-retour :href="route('examens.index')" label="Retour aux évaluations" :breadcrumb="$devoir->intitule" />

{{-- En-tête devoir --}}
<div class="bg-green-50 border border-blue-100 rounded-xl p-5 mb-5">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-bold text-blue-800">{{ $devoir->intitule }}</h2>
            <div class="flex flex-wrap gap-4 mt-2 text-sm text-blue-600">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <strong>{{ $devoir->matiere->nom }}</strong>
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Classe <strong>{{ $devoir->classe->nom }}</strong>
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $devoir->date_devoir->format('d/m/Y') }}
                </span>
                <span>Note max : <strong>{{ $devoir->note_max }}</strong></span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-200 text-blue-800">
                    {{ $devoir->trimestre }} — {{ $devoir->annee_scolaire }}
                </span>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('notes.saisir.devoir', $devoir) }}"
               style="background:#16a34a; color:#fff; display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:8px; font-size:0.875rem; font-weight:600; text-decoration:underline; text-underline-offset:3px;">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Saisir / modifier les notes
            </a>
            <a href="{{ route('devoirs.edit', $devoir) }}"
               class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Modifier
            </a>
        </div>
    </div>
</div>

{{-- Statistiques --}}
@php
    $notesValues = $devoir->notes->pluck('note');
    $nbNotes  = $notesValues->count();
    $moyenne  = $nbNotes > 0 ? round($notesValues->avg(), 2) : null;
    $noteMin  = $nbNotes > 0 ? $notesValues->min() : null;
    $noteMax  = $nbNotes > 0 ? $notesValues->max() : null;
    $nbReussi = $devoir->notes->where('note', '>=', $devoir->note_max / 2)->count();
@endphp

@if($nbNotes > 0)
<div class="grid grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs text-gray-400 mb-1">Notes saisies</p>
        <p class="text-2xl font-bold text-gray-800">{{ $nbNotes }} / {{ $etudiants->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs text-gray-400 mb-1">Moyenne</p>
        <p class="text-2xl font-bold {{ $moyenne >= $devoir->note_max / 2 ? 'text-green-600' : 'text-red-500' }}">
            {{ $moyenne }}/{{ $devoir->note_max }}
        </p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs text-gray-400 mb-1">Note min / max</p>
        <p class="text-lg font-bold text-gray-800">{{ $noteMin }} / {{ $noteMax }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs text-gray-400 mb-1">Taux de reussite</p>
        <p class="text-2xl font-bold {{ $nbReussi / $nbNotes >= 0.5 ? 'text-green-600' : 'text-orange-500' }}">
            {{ round($nbReussi / $nbNotes * 100) }}%
        </p>
    </div>
</div>
@endif

{{-- Tableau des notes --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
        <h3 class="font-semibold text-gray-700">
            Notes des eleves
            @if($nbNotes > 0)
            <span class="text-xs text-gray-400 font-normal ml-2">{{ $nbNotes }} note(s) saisie(s)</span>
            @endif
        </h3>
        @if($nbNotes === 0)
        <a href="{{ route('notes.saisir.devoir', $devoir) }}"
           style="background:#16a34a; color:#fff; padding:6px 14px; border-radius:8px; font-size:0.78rem; font-weight:600; text-decoration:underline; text-underline-offset:3px;">
            Saisir les notes
        </a>
        @endif
    </div>

    <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">/ {{ $devoir->note_max }}</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Mention</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Barre</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php
                $notesParEtudiant = $devoir->notes->keyBy('etudiant_id');
            @endphp
            @foreach($etudiants as $i => $etudiant)
            @php
                $note = $notesParEtudiant[$etudiant->id] ?? null;
                $reussi = $note && $note->note >= ($devoir->note_max / 2);
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-5 py-2.5">
                    <a href="{{ route('etudiants.show', $etudiant) }}"
                       class="font-medium text-gray-800 hover:text-blue-600 transition-colors">
                        {{ $etudiant->nom_complet }}
                    </a>
                </td>
                <td class="px-5 py-2.5 text-center">
                    @if($note)
                    <span class="text-lg font-bold {{ $reussi ? 'text-green-600' : 'text-red-500' }}">
                        {{ number_format($note->note, 2) }}
                    </span>
                    @else
                    <span class="text-gray-300 text-sm">—</span>
                    @endif
                </td>
                <td class="px-5 py-2.5 text-center text-gray-400 text-xs">{{ $devoir->note_max }}</td>
                <td class="px-5 py-2.5 text-center">
                    @if($note)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $note->note >= 16 ? 'bg-green-100 text-green-700' :
                          ($note->note >= 14 ? 'bg-teal-100 text-teal-700' :
                          ($note->note >= 12 ? 'bg-green-100 text-blue-700' :
                          ($note->note >= 10 ? 'bg-yellow-100 text-yellow-700' :
                           'bg-red-100 text-red-600'))) }}">
                        {{ $note->mention }}
                    </span>
                    @endif
                </td>
                <td class="px-5 py-2.5">
                    @if($note)
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $reussi ? 'bg-green-400' : 'bg-red-400' }}"
                             style="width: {{ min(100, ($note->note / $devoir->note_max) * 100) }}%"></div>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach

            @if($etudiants->isEmpty())
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                    Aucun eleve actif dans cette classe.
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($nbNotes > 0)
    <div class="px-5 py-3 border-t border-gray-50 bg-gray-50 flex justify-end">
        <a href="{{ route('notes.saisir.devoir', $devoir) }}"
           style="background:#16a34a; color:#fff; padding:6px 14px; border-radius:8px; font-size:0.78rem; font-weight:600; text-decoration:underline; text-underline-offset:3px;">
            Modifier les notes
        </a>
    </div>
    @endif
</div>

@endsection
