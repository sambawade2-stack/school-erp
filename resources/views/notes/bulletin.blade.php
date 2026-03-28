@extends('layouts.app')
@section('titre', 'Bulletin - ' . $etudiant->nom_complet)
@section('titre-page', 'Bulletin de Notes')

@section('contenu')

<x-btn-retour :href="route('etudiants.show', $etudiant)" label="Retour au profil" :breadcrumb="'Bulletin - ' . $etudiant->nom_complet" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
    <div class="flex items-start gap-6">
        @if($etablissement && $etablissement->logo)
        <img src="{{ '/storage/logo/' . $etablissement->logo }}" alt="Logo" class="h-16 object-contain"
             onerror="this.style.display='none'">
        @endif
        <div class="flex-1">
            <h2 class="text-lg font-heading font-bold text-gray-900">{{ $etablissement->nom ?? 'ÉTABLISSEMENT' }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $etablissement->sigle ?? '' }}</p>
            @if($etablissement->adresse)
            <p class="text-sm text-gray-600">{{ $etablissement->adresse }}</p>
            @endif
            @if($etablissement->telephone || $etablissement->email)
            <p class="text-sm text-gray-600">
                @if($etablissement->telephone){{ $etablissement->telephone }}@endif
                @if($etablissement->telephone && $etablissement->email) | @endif
                @if($etablissement->email){{ $etablissement->email }}@endif
            </p>
            @endif
        </div>
    </div>
</div>

@php
    $annees = \App\Models\AnneeScolaire::orderByDesc('date_debut')->pluck('libelle')->toArray();
    if (empty($annees)) $annees = [$anneeScolaire];
    $isElementaire = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);
    $periodes = $isElementaire ? ['S1', 'S2', 'S3'] : ['T1', 'T2', 'T3'];
    $periodeLabel = $isElementaire ? 'Semestre' : 'Trimestre';
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-3">
        <select onchange="window.location=this.value" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @foreach($annees as $as)
            <option value="{{ route('notes.bulletin', ['etudiant' => $etudiant->id, 'annee_scolaire' => $as, 'trimestre' => $trimestre]) }}" {{ $anneeScolaire === $as ? 'selected' : '' }}>
                {{ $as }}
            </option>
            @endforeach
        </select>
        <select onchange="window.location='{{ route('notes.bulletin', $etudiant) }}?annee_scolaire={{ $anneeScolaire }}&trimestre='+this.value"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @foreach($periodes as $t)
            <option value="{{ $t }}" {{ $trimestre === $t ? 'selected' : '' }}>{{ $periodeLabel }} {{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('notes.bulletin.pdf', ['etudiant' => $etudiant->id, 'annee_scolaire' => $anneeScolaire, 'trimestre' => $trimestre]) }}"
           class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background: #dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Télécharger PDF
        </a>
        <a href="{{ route('notes.bulletin.print', ['etudiant' => $etudiant->id, 'annee_scolaire' => $anneeScolaire, 'trimestre' => $trimestre]) }}" target="_blank"
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimer
        </a>
        <a href="{{ route('etudiant.certificat', $etudiant) }}"
           class="flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Certificat
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="p-5 border-b bg-blue-50">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($etudiant->photo)
                <img src="{{ $etudiant->photo_url }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-200"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold border-2 border-blue-200" style="display:none;">
                    {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                </div>
                @else
                <div class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold border-2 border-blue-200">
                    {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">{{ $etudiant->nom_complet }}</h3>
                    <p class="text-sm text-gray-500">Classe : {{ $etudiant->classe?->nom ?? '—' }} | {{ $anneeScolaire }} | {{ $trimestre }}</p>
                    @if($moyenne > 0)
                    <p class="text-sm font-semibold {{ $moyenne >= 10 ? 'text-green-600' : 'text-red-500' }} mt-1">
                        Moyenne generale : {{ number_format($moyenne, 2) }}/20
                    </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('notes.bulletin.pdf', ['etudiant' => $etudiant->id, 'annee_scolaire' => $anneeScolaire, 'trimestre' => $trimestre]) }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Générer le bulletin
            </a>
        </div>
    </div>

    {{-- EXAMENS --}}
    @if($notesExamen->count() > 0)
    <div class="px-5 py-3 bg-blue-50 border-b font-semibold text-blue-900">Notes d'Examens</div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matiere</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note max</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Mention</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($notesExamen as $matiere => $notesMatiere)
            @foreach($notesMatiere as $note)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $matiere }}</td>
                <td class="px-5 py-2.5 text-center font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-500' }}">{{ $note->note }}</td>
                <td class="px-5 py-2.5 text-center text-gray-400">{{ $note->examen->note_max }}</td>
                <td class="px-5 py-2.5 text-center text-xs text-gray-500">{{ $note->mention }}</td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Aucune note d'examen</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- COMPOSITIONS --}}
    @if($notesComposition->count() > 0)
    <div class="px-5 py-3 bg-amber-50 border-b border-t font-semibold text-amber-900">Notes de Compositions</div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matiere</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note max</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Mention</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($notesComposition as $matiere => $notesMatiere)
            @foreach($notesMatiere as $note)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $matiere }}</td>
                <td class="px-5 py-2.5 text-center font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-500' }}">{{ $note->note }}</td>
                <td class="px-5 py-2.5 text-center text-gray-400">{{ $note->composition->note_max }}</td>
                <td class="px-5 py-2.5 text-center text-xs text-gray-500">{{ $note->mention }}</td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Aucune note de composition</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    {{-- DEVOIRS --}}
    @if($notesDevoir->count() > 0)
    <div class="px-5 py-3 bg-green-50 border-b border-t font-semibold text-green-900">Notes de Devoirs</div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matiere</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Note max</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Mention</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($notesDevoir as $matiere => $notesMatiere)
            @foreach($notesMatiere as $note)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $matiere }}</td>
                <td class="px-5 py-2.5 text-center font-bold {{ $note->note >= 10 ? 'text-green-600' : 'text-red-500' }}">{{ $note->note }}</td>
                <td class="px-5 py-2.5 text-center text-gray-400">{{ $note->devoir->note_max }}</td>
                <td class="px-5 py-2.5 text-center text-xs text-gray-500">{{ $note->mention }}</td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Aucune note de devoir</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($notesExamen->count() == 0 && $notesComposition->count() == 0 && $notesDevoir->count() == 0)
    <div class="px-5 py-6 text-center text-gray-400">Aucune note pour cette periode</div>
    @endif
</div>
@endsection
