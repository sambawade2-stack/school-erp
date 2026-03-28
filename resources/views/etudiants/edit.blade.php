@extends('layouts.app')
@section('titre', 'Modifier - ' . $etudiant->nom_complet)
@section('titre-page', 'Modifier un Etudiant')

@section('contenu')

<div class="flex items-center gap-2 mb-5">
    <a href="{{ route('etudiants.show', $etudiant) }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour au profil
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-sm text-gray-500">{{ $etudiant->nom_complet }}</span>
</div>

<div class="max-w-3xl">
<form action="{{ route('etudiants.update', $etudiant) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h4 class="text-sm font-semibold text-red-700">Veuillez corriger les erreurs suivantes :</h4>
        </div>
        <ul class="text-sm text-red-600 list-disc pl-7 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

        <h3 class="font-semibold text-gray-700 border-b border-gray-100 pb-3">Informations personnelles</h3>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prenom <span class="text-red-500">*</span></label>
                <input type="text" name="prenom" value="{{ old('prenom', $etudiant->prenom) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $etudiant->nom) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sexe <span class="text-red-500">*</span></label>
                <select name="sexe" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="masculin" {{ old('sexe', $etudiant->sexe) === 'masculin' ? 'selected' : '' }}>Masculin</option>
                    <option value="feminin"  {{ old('sexe', $etudiant->sexe) === 'feminin'  ? 'selected' : '' }}>Feminin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de naissance</label>
                <input type="date" name="date_naissance" value="{{ old('date_naissance', $etudiant->date_naissance?->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau</label>
                <select name="niveau" id="niveau" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucun --</option>
                    @foreach(\App\Models\Classe::CATEGORIES as $catKey => $catLabel)
                    <option value="{{ $catKey }}" {{ old('niveau', $etudiant->classe?->categorie) === $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe</label>
                <select name="classe_id" id="classe_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune classe --</option>
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" data-categorie="{{ $classe->categorie }}" {{ old('classe_id', $etudiant->classe_id) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="actif"   {{ old('statut', $etudiant->statut) === 'actif'   ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ old('statut', $etudiant->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    <option value="archive" {{ old('statut', $etudiant->statut) === 'archive' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $etudiant->telephone) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'inscription</label>
                <input type="date" name="date_inscription" value="{{ old('date_inscription', $etudiant->date_inscription?->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom du parent</label>
                <input type="text" name="nom_parent" value="{{ old('nom_parent', $etudiant->nom_parent) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tel parent</label>
                <input type="text" name="tel_parent" value="{{ old('tel_parent', $etudiant->tel_parent) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <x-webcam-photo name="photo" label="Photo (laisser vide pour conserver l'actuelle)" :current-url="$etudiant->photo ? $etudiant->photo_url : null" />

    </div>

    <div class="flex items-center gap-3 mt-5">
        <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            Enregistrer les modifications
        </button>
        <a href="{{ route('etudiants.show', $etudiant) }}"
           class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
            Annuler
        </a>
    </div>
</form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var niveauSelect = document.getElementById('niveau');
    var classeSelect = document.getElementById('classe_id');
    var allOptions = Array.from(classeSelect.querySelectorAll('option[data-categorie]'));
    var oldClasseId = '{{ old("classe_id", $etudiant->classe_id) }}';

    function filtrerClasses() {
        var niveau = niveauSelect.value;
        allOptions.forEach(function(opt) { opt.remove(); });
        allOptions.forEach(function(opt) {
            if (!niveau || opt.getAttribute('data-categorie') === niveau) {
                classeSelect.appendChild(opt);
            }
        });
        if (!classeSelect.querySelector('option[value="' + classeSelect.value + '"][data-categorie]')) {
            classeSelect.value = '';
        }
    }

    niveauSelect.addEventListener('change', filtrerClasses);
    filtrerClasses();

    if (oldClasseId) {
        classeSelect.value = oldClasseId;
    }
});
</script>
@endpush
