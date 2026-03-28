@extends('layouts.app')
@section('titre', 'Presences')
@section('titre-page', 'Feuille de Presence')

@section('contenu')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
    <form action="{{ route('presences.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Classe</label>
            <select name="classe_id" required onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
                <option value="">-- Choisir une classe --</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ $classeId == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                   class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="mt-5">
            <a href="{{ route('presences.rapport') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Rapport</a>
        </div>
    </form>
</div>

@if($classeId && $etudiants->count())
<form action="{{ route('presences.store') }}" method="POST">
    @csrf
    <input type="hidden" name="classe_id" value="{{ $classeId }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-3 bg-blue-50 border-b border-blue-100 flex-wrap gap-2">
            <h3 class="font-semibold text-blue-800">
                Presence du {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </h3>
            <div class="flex gap-2 text-xs">
                <button type="button" onclick="setAll('present')"  class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">Tous presents</button>
                <button type="button" onclick="setAll('absent')"   class="px-3 py-1 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Tous absents</button>
            </div>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Present</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Absent</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Retard</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Excuse</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($etudiants as $i => $etudiant)
                @php $statut = $presences[$etudiant->id] ?? 'present'; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-5 py-2.5 font-medium text-gray-800">{{ $etudiant->nom_complet }}</td>
                    @foreach(['present', 'absent', 'retard', 'excuse'] as $opt)
                    <td class="px-5 py-2.5">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="presences[{{ $etudiant->id }}]" value="{{ $opt }}"
                                   {{ $statut === $opt ? 'checked' : '' }}
                                   class="radio-{{ $opt }} w-4 h-4 {{ $opt === 'present' ? 'text-green-500' : ($opt === 'absent' ? 'text-red-500' : ($opt === 'retard' ? 'text-yellow-500' : 'text-blue-400')) }}">
                        </label>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div class="px-5 py-4 border-t bg-gray-50 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-500">{{ $etudiants->count() }} eleve(s)</p>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Enregistrer les presences
            </button>
        </div>
    </div>
</form>
@elseif($classeId)
<div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400">
    Aucun eleve actif dans cette classe.
</div>
@else
<div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400">
    Selectionnez une classe pour saisir les presences.
</div>
@endif

@push('scripts')
<script>
function setAll(statut) {
    document.querySelectorAll('.radio-' + statut).forEach(r => r.checked = true);
}
</script>
@endpush

@endsection
