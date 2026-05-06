@extends('layouts.app')
@section('titre', 'Modifier Composition')
@section('titre-page', 'Modifier une Composition')

@section('contenu')

<x-btn-retour :href="route('examens.index')" label="Retour aux compositions" />

<div class="max-w-xl">
<form action="{{ route('compositions.update', $composition) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Intitulé <span class="text-red-500">*</span></label>
            <input type="text" name="intitule" value="{{ old('intitule', $composition->intitule) }}"
                   required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Matière <span class="text-red-500">*</span></label>
            <select name="matiere_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach($matieres as $matiere)
                <option value="{{ $matiere->id }}" {{ old('matiere_id', $composition->matiere_id) == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                @endforeach
            </select>
        </div>

        {{-- Classes — la classe actuelle est pré-cochée, les supplémentaires créent de nouvelles compositions --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Classes <span class="text-red-500">*</span></label>
            @php
                $oldIds = old('classe_ids', [$composition->classe_id]);
                $grouped = $classes->groupBy('categorie');
                $niveauxLabels = ['elementaire' => 'Élémentaire', 'prescolaire' => 'Préscolaire', 'college' => 'Collège', 'lycee' => 'Lycée'];
            @endphp
            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 overflow-hidden">
                @foreach($niveauxLabels as $catKey => $catLabel)
                    @php $groupe = $grouped[$catKey] ?? collect(); @endphp
                    @if($groupe->isNotEmpty())
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $catLabel }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($groupe as $classe)
                            @php $isCurrent = ($classe->id == $composition->classe_id); @endphp
                            <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer text-sm transition-colors
                                {{ in_array($classe->id, $oldIds) ? 'border-amber-500 bg-amber-50 text-amber-700 font-medium' : 'border-gray-200 text-gray-700 hover:border-amber-300 hover:bg-amber-50' }}"
                                id="lbl-classe-{{ $classe->id }}"
                                data-categorie="{{ $classe->categorie }}">
                                <input type="checkbox" name="classe_ids[]" value="{{ $classe->id }}"
                                    {{ in_array($classe->id, $oldIds) ? 'checked' : '' }}
                                    class="accent-amber-500 classe-checkbox"
                                    data-categorie="{{ $classe->categorie }}"
                                    data-current="{{ $isCurrent ? '1' : '0' }}"
                                    onchange="toggleClasse(this)">
                                {{ $classe->nom }}
                                @if($isCurrent)
                                <span class="text-xs text-amber-500 font-normal">(actuelle)</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">La classe actuelle sera mise à jour. Les autres classes cochées créeront de nouvelles compositions.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_composition" value="{{ old('date_composition', $composition->date_composition->format('Y-m-d')) }}"
                       required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Note max</label>
                <input type="number" name="note_max" value="{{ old('note_max', $composition->note_max) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Période <span class="text-red-500">*</span></label>
                <select name="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <optgroup label="Trimestres">
                        @foreach(['T1' => 'Trimestre 1', 'T2' => 'Trimestre 2', 'T3' => 'Trimestre 3'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('trimestre', $composition->trimestre) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Semestres (Élémentaire)">
                        @foreach(['S1' => 'Semestre 1', 'S2' => 'Semestre 2', 'S3' => 'Semestre 3'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('trimestre', $composition->trimestre) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $composition->annee_scolaire) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" style="background:#d97706;" class="px-6 py-2.5 text-white rounded-lg text-sm font-medium hover:opacity-90">Enregistrer</button>
        <a href="{{ route('examens.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>

@push('scripts')
<script>
function toggleClasse(checkbox) {
    var lbl = document.getElementById('lbl-classe-' + checkbox.value);
    if (checkbox.checked) {
        lbl.classList.add('border-amber-500', 'bg-amber-50', 'text-amber-700', 'font-medium');
        lbl.classList.remove('border-gray-200', 'text-gray-700');
    } else {
        // Ne pas décocher la classe actuelle
        if (checkbox.dataset.current === '1') {
            checkbox.checked = true;
            return;
        }
        lbl.classList.remove('border-amber-500', 'bg-amber-50', 'text-amber-700', 'font-medium');
        lbl.classList.add('border-gray-200', 'text-gray-700');
    }
}
</script>
@endpush
@endsection
