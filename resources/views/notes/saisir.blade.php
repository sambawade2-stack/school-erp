@extends('layouts.app')
@section('titre', 'Saisie des Notes')
@section('titre-page', 'Saisie des Notes')

@php
    $evaluation = $examen ?? $devoir ?? $composition;
    $dateField = $evaluation instanceof \App\Models\Examen ? 'date_examen' : ($evaluation instanceof \App\Models\Devoir ? 'date_devoir' : 'date_composition');
    $routeName = $evaluation instanceof \App\Models\Examen ? 'examens.index' : ($evaluation instanceof \App\Models\Devoir ? 'examens.index' : 'examens.index');
    $enregistrerRoute = $evaluation instanceof \App\Models\Examen ? 'notes.enregistrer' : ($evaluation instanceof \App\Models\Devoir ? 'notes.enregistrer.devoir' : 'notes.enregistrer.composition');
    $isExamen = $evaluation instanceof \App\Models\Examen;
    $isDevoir = $evaluation instanceof \App\Models\Devoir;
    $isCompo  = $evaluation instanceof \App\Models\Composition;
    // Couleurs statiques selon le type (jamais dynamiques pour Tailwind)
    $headerStyle  = $isExamen ? 'background:#eff6ff; border:1px solid #bfdbfe;' : ($isDevoir ? 'background:#f0fdf4; border:1px solid #bbf7d0;' : 'background:#fffbeb; border:1px solid #fde68a;');
    $titleColor   = $isExamen ? '#1e40af' : ($isDevoir ? '#166534' : '#92400e');
    $metaColor    = $isExamen ? '#2563eb' : ($isDevoir ? '#16a34a' : '#d97706');
@endphp

@section('contenu')

<x-btn-retour :href="route($routeName)" label="Retour" :breadcrumb="$evaluation->intitule" />

<div style="{{ $headerStyle }} border-radius:12px; padding:16px; margin-bottom:20px;">
    <h3 style="font-weight:600; color:{{ $titleColor }}; margin:0 0 6px 0;">{{ $evaluation->intitule }}</h3>
    <div style="display:flex; gap:24px; font-size:0.875rem; color:{{ $metaColor }}; flex-wrap:wrap;">
        <span>Matiere : <strong>{{ $evaluation->matiere->nom }}</strong></span>
        <span>Classe : <strong>{{ $evaluation->classe->nom }}</strong></span>
        <span>Date : <strong>{{ $evaluation->{$dateField}->format('d/m/Y') }}</strong></span>
        <span>Note max : <strong>{{ $evaluation->note_max }}</strong></span>
        <span>Trimestre : <strong>{{ $evaluation->trimestre }}</strong></span>
    </div>
</div>


<form action="{{ route($enregistrerRoute, $evaluation) }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Note /{{ $evaluation->note_max }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mention</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="notesTable">
                @foreach($etudiants as $i => $etudiant)
                @php $noteVal = $notes[$etudiant->id] ?? ''; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-5 py-2.5 font-medium text-gray-800">{{ $etudiant->nom_complet }}</td>
                    <td class="px-5 py-2.5 w-36">
                        <input type="number"
                               name="notes[{{ $etudiant->id }}]"
                               value="{{ $noteVal }}"
                               step="0.25"
                               min="0"
                               max="{{ $evaluation->note_max }}"
                               class="w-24 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-center focus:ring-2 focus:ring-blue-500 focus:outline-none note-input"
                               placeholder="—">
                    </td>
                    <td class="px-5 py-2.5 text-xs text-gray-400 mention-cell">
                        @if($noteVal !== '')
                        {{ $noteVal >= 16 ? 'Tres Bien' : ($noteVal >= 14 ? 'Bien' : ($noteVal >= 12 ? 'Assez Bien' : ($noteVal >= 10 ? 'Passable' : 'Insuffisant'))) }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t bg-gray-50 flex justify-between items-center">
            <p class="text-sm text-gray-500">{{ $etudiants->count() }} eleve(s)</p>
            <div class="flex gap-3">
                <a href="{{ route($routeName) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Retour</a>
                @if($evaluation instanceof \App\Models\Examen)
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Enregistrer les notes</button>
                @elseif($evaluation instanceof \App\Models\Devoir)
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">Enregistrer les notes</button>
                @else
                <button type="submit" class="px-6 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#d97706;">Enregistrer les notes</button>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection
