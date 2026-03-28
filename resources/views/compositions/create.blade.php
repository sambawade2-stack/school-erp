@extends('layouts.app')
@section('titre', 'Nouvel Composition')
@section('titre-page', 'Creer un Composition')

@section('contenu')

<x-btn-retour :href="route('compositions.index')" label="Retour aux compositions" breadcrumb="Nouvel composition" />

<div class="max-w-xl">
<form action="{{ route('compositions.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Intitule <span class="text-red-500">*</span></label>
            <input type="text" name="intitule" value="{{ old('intitule') }}" placeholder="ex: Controle de Mathematiques T1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Matiere <span class="text-red-500">*</span></label>
                <select name="matiere_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach($matieres as $matiere)
                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe <span class="text-red-500">*</span></label>
                <select name="classe_id" id="classe_select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" data-categorie="{{ $classe->categorie }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_composition" value="{{ old('date_composition') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Note max</label>
                <input type="number" name="note_max" value="{{ old('note_max', 20) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" style="background:#d97706;" class="px-6 py-2.5 text-white rounded-lg text-sm font-medium hover:opacity-90">Créer la composition</button>
        <a href="{{ route('examens.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
<script>
function updatePeriode(classeSelect, oldTrimestre) {
    const opt = classeSelect.options[classeSelect.selectedIndex];
    const cat = opt ? (opt.dataset.categorie || '') : '';
    const isElem = (cat === 'elementaire' || cat === 'prescolaire');
    const sel = document.getElementById('periode_select');
    const label = document.getElementById('periode_label');

    label.textContent = isElem ? 'Semestre' : 'Trimestre';
    sel.innerHTML = isElem
        ? '<option value="S1">Semestre 1</option><option value="S2">Semestre 2</option><option value="S3">Semestre 3</option>'
        : '<option value="T1">Trimestre 1</option><option value="T2">Trimestre 2</option><option value="T3">Trimestre 3</option>';

    // Restaurer la valeur old() si elle correspond aux options disponibles
    if (oldTrimestre) {
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === oldTrimestre) {
                sel.selectedIndex = i;
                break;
            }
        }
    }
}

const classeSelect = document.getElementById('classe_select');
const oldTrimestre = '{{ old('trimestre') }}';

// Déclencher au chargement pour restaurer les bonnes options
if (classeSelect.value) {
    updatePeriode(classeSelect, oldTrimestre);
}

classeSelect.addEventListener('change', function() {
    updatePeriode(this, null);
});
</script>
@endsection
