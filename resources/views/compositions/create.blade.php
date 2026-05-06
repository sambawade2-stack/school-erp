@extends('layouts.app')
@section('titre', 'Nouvelle Composition')
@section('titre-page', 'Créer une Composition')

@section('contenu')

<x-btn-retour :href="route('examens.index')" label="Retour aux examens" breadcrumb="Nouvelle composition" />

<div class="max-w-xl">
<form action="{{ route('compositions.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Intitulé <span class="text-red-500">*</span></label>
            <input type="text" name="intitule" value="{{ old('intitule') }}" placeholder="ex: Contrôle de Mathématiques T1"
                   required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Matière <span class="text-red-500">*</span></label>
            <select name="matiere_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choisir --</option>
                @foreach($matieres as $matiere)
                <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                @endforeach
            </select>
        </div>

        {{-- Classes — multi-sélection par badges cochables --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Classes <span class="text-red-500">*</span></label>
            @php
                $oldIds = old('classe_ids', []);
                $grouped = $classes->groupBy('categorie');
                $niveauxLabels = ['elementaire' => 'Élémentaire', 'prescolaire' => 'Préscolaire', 'college' => 'Collège', 'lycee' => 'Lycée'];
            @endphp
            @if($classes->isEmpty())
                <p class="text-xs text-gray-400 italic">Aucune classe créée.</p>
            @else
            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 overflow-hidden">
                @foreach($niveauxLabels as $catKey => $catLabel)
                    @php $groupe = $grouped[$catKey] ?? collect(); @endphp
                    @if($groupe->isNotEmpty())
                    <div class="p-3" data-categorie="{{ $catKey }}">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $catLabel }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($groupe as $classe)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer text-sm transition-colors
                                {{ in_array($classe->id, $oldIds) ? 'border-amber-500 bg-amber-50 text-amber-700 font-medium' : 'border-gray-200 text-gray-700 hover:border-amber-300 hover:bg-amber-50' }}"
                                id="lbl-classe-{{ $classe->id }}"
                                data-categorie="{{ $classe->categorie }}">
                                <input type="checkbox" name="classe_ids[]" value="{{ $classe->id }}"
                                    {{ in_array($classe->id, $oldIds) ? 'checked' : '' }}
                                    class="accent-amber-500 classe-checkbox"
                                    data-categorie="{{ $classe->categorie }}"
                                    onchange="toggleClasse(this)">
                                {{ $classe->nom }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">Une composition distincte sera créée pour chaque classe cochée.</p>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_composition" value="{{ old('date_composition') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Note max</label>
                <input type="number" name="note_max" value="{{ old('note_max', 20) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        @php $anneeActive = \App\Models\AnneeScolaire::libelleActif() ?? '2025-2026'; @endphp
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" id="periode_label">Période <span class="text-red-500">*</span></label>
                <select name="trimestre" id="periode_select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="T1" {{ old('trimestre') === 'T1' ? 'selected' : '' }}>Trimestre 1</option>
                    <option value="T2" {{ old('trimestre') === 'T2' ? 'selected' : '' }}>Trimestre 2</option>
                    <option value="T3" {{ old('trimestre') === 'T3' ? 'selected' : '' }}>Trimestre 3</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" style="background:#d97706;" class="px-6 py-2.5 text-white rounded-lg text-sm font-medium hover:opacity-90">Créer la composition</button>
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
        lbl.classList.remove('border-amber-500', 'bg-amber-50', 'text-amber-700', 'font-medium');
        lbl.classList.add('border-gray-200', 'text-gray-700');
    }
    updatePeriodeFromCheckboxes();
}

function updatePeriodeFromCheckboxes() {
    var checked = document.querySelectorAll('.classe-checkbox:checked');
    if (checked.length === 0) return;

    // Détermine si toutes les classes cochées sont élémentaire/préscolaire
    var allElem = Array.from(checked).every(function(cb) {
        return cb.dataset.categorie === 'elementaire' || cb.dataset.categorie === 'prescolaire';
    });

    var sel = document.getElementById('periode_select');
    var label = document.getElementById('periode_label');
    var currentVal = sel.value;

    label.innerHTML = (allElem ? 'Semestre' : 'Trimestre') + ' <span class="text-red-500">*</span>';
    sel.innerHTML = allElem
        ? '<option value="S1">Semestre 1</option><option value="S2">Semestre 2</option><option value="S3">Semestre 3</option>'
        : '<option value="T1">Trimestre 1</option><option value="T2">Trimestre 2</option><option value="T3">Trimestre 3</option>';

    // Restaurer si possible
    for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === currentVal) { sel.selectedIndex = i; break; }
    }
}

// Restaurer état old()
document.querySelectorAll('.classe-checkbox:checked').forEach(function(cb) {
    var lbl = document.getElementById('lbl-classe-' + cb.value);
    if (lbl) {
        lbl.classList.add('border-amber-500', 'bg-amber-50', 'text-amber-700', 'font-medium');
        lbl.classList.remove('border-gray-200', 'text-gray-700');
    }
});
updatePeriodeFromCheckboxes();
</script>
@endpush
@endsection
