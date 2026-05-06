@extends('layouts.app')
@section('titre', 'Ajouter une Matiere')
@section('titre-page', 'Ajouter une Matiere')

@section('contenu')

<x-btn-retour :href="route('matieres.index')" label="Retour aux matieres" breadcrumb="Ajouter une matiere" />

<div class="max-w-xl">
<form action="{{ route('matieres.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="ex: MATH" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Coefficient <span class="text-red-500">*</span></label>
                <input type="number" name="coefficient" value="{{ old('coefficient', 1) }}" step="0.5" min="0.5" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Section <span class="text-red-500">*</span></label>
                <select name="section" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Sélectionner --</option>
                    @foreach(\App\Models\Section::ordonnes()->get() as $sec)
                    <option value="{{ $sec->nom }}" {{ old('section') == $sec->nom ? 'selected' : '' }}>
                        {{ $sec->nom }}
                        @if($sec->niveau) ({{ match($sec->niveau) { 'elementaire'=>'Élém.', 'college'=>'Collège', 'lycee'=>'Lycée', default=>'' } }}) @endif
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Enseignant</label>
            <select name="enseignant_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Aucun --</option>
                @foreach($enseignants as $ens)
                <option value="{{ $ens->id }}" {{ old('enseignant_id') == $ens->id ? 'selected' : '' }}>{{ $ens->nom_complet }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Classes</label>
            @php
                $niveauxLabels = ['elementaire' => 'Élémentaire', 'college' => 'Collège', 'terminal' => 'Terminal'];
                $oldIds = old('classe_ids', []);
            @endphp
            @if($classes->isEmpty())
                <p class="text-xs text-gray-400 italic">Aucune classe créée.</p>
            @else
            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 overflow-hidden">
                @foreach($niveauxLabels as $niveauKey => $niveauLabel)
                    @php $groupe = $classes[$niveauKey] ?? collect(); @endphp
                    @if($groupe->isNotEmpty())
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $niveauLabel }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($groupe as $classe)
                            <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer text-sm transition-colors
                                {{ in_array($classe->id, $oldIds) ? 'border-blue-500 bg-blue-50 text-blue-700 font-medium' : 'border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50' }}"
                                id="label-classe-{{ $classe->id }}">
                                <input type="checkbox" name="classe_ids[]" value="{{ $classe->id }}"
                                    {{ in_array($classe->id, $oldIds) ? 'checked' : '' }}
                                    class="accent-blue-600"
                                    onchange="toggleClasseLabel(this, {{ $classe->id }})">
                                {{ $classe->nom }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1">Laissez tout décoché pour une matière commune à toutes les classes.</p>
            @endif
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('matieres.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function toggleClasseLabel(checkbox, id) {
    const label = document.getElementById('label-classe-' + id);
    if (checkbox.checked) {
        label.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700', 'font-medium');
        label.classList.remove('border-gray-200', 'text-gray-700');
    } else {
        label.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700', 'font-medium');
        label.classList.add('border-gray-200', 'text-gray-700');
    }
}
</script>
@endpush
