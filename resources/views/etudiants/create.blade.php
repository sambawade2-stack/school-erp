@extends('layouts.app')
@section('titre', 'Ajouter un Etudiant')
@section('titre-page', 'Ajouter un Etudiant')
@section('breadcrumb', 'Etudiants / Nouveau')

@section('contenu')

<div class="flex items-center gap-2 mb-5">
    <a href="{{ route('etudiants.index') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-sm text-gray-500">Ajouter un etudiant</span>
</div>

<div class="max-w-3xl">
<form action="{{ route('etudiants.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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

        {{-- Avertissement doublon (informatif, n'empeche pas l'enregistrement) --}}
        <div id="alerte-doublon" style="display:none;" class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-700">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><span id="msg-doublon"></span> <em class="text-xs">(vous pouvez quand meme enregistrer)</em></span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prenom <span class="text-red-500">*</span></label>
                <input type="text" name="prenom" id="input-prenom" value="{{ old('prenom') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" id="input-nom" value="{{ old('nom') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sexe <span class="text-red-500">*</span></label>
                <select name="sexe" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    <option value="masculin" {{ old('sexe') === 'masculin' ? 'selected' : '' }}>Masculin</option>
                    <option value="feminin"  {{ old('sexe') === 'feminin'  ? 'selected' : '' }}>Feminin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de naissance</label>
                <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'inscription <span class="text-red-500">*</span></label>
            <input type="date" name="date_inscription" value="{{ old('date_inscription', today()->format('Y-m-d')) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <h3 class="font-semibold text-gray-700 border-b border-blue-100 pb-3 pt-2 text-blue-800">Inscription scolaire</h3>

        @if(!$anneeCourante)
        <div class="px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
            Aucune année scolaire active.
            <a href="{{ route('admin.annees.index') }}" class="underline font-medium">Configurer une année →</a>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau</label>
                <select name="niveau" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucun --</option>
                    @foreach(\App\Models\Classe::CATEGORIES as $catKey => $catLabel)
                    <option value="{{ $catKey }}" {{ old('niveau') === $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe</label>
                <select name="classe_id" id="classe_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune classe --</option>
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" data-categorie="{{ $classe->categorie }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire</label>
                <input type="text" name="annee_scolaire_inscription"
                       value="{{ old('annee_scolaire_inscription', $anneeCourante?->libelle ?? '') }}"
                       placeholder="ex: 2025-2026"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none
                              {{ $anneeCourante ? 'border-green-300 bg-green-50' : 'border-gray-300' }}">
                @if($anneeCourante)
                <p class="text-xs text-green-600 mt-1">Détectée automatiquement</p>
                @endif
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
            <textarea name="adresse" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('adresse') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Telephone</label>
            <input type="text" name="telephone" value="{{ old('telephone') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <h3 class="font-semibold text-gray-700 border-b border-gray-100 pb-3 pt-2">Informations parent / tuteur</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom du parent</label>
                <input type="text" name="nom_parent" value="{{ old('nom_parent') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telephone parent</label>
                <input type="text" name="tel_parent" value="{{ old('tel_parent') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <h3 class="font-semibold text-gray-700 border-b border-gray-100 pb-3 pt-2">Photo</h3>

        <x-webcam-photo name="photo" label="Photo de l'etudiant" />

    </div>

    <div class="flex items-center gap-3 mt-5">
        <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            Enregistrer l'etudiant
        </button>
        <a href="{{ route('etudiants.index') }}"
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
    var niveauSelect = document.querySelector('select[name="niveau"]');
    var classeSelect = document.getElementById('classe_id');
    var allOptions = Array.from(classeSelect.querySelectorAll('option[data-categorie]'));
    var oldClasseId = '{{ old("classe_id", "") }}';

    function filtrerClasses() {
        var niveau = niveauSelect.value;
        // Remove all class options
        allOptions.forEach(function(opt) { opt.remove(); });
        // Re-add matching ones
        allOptions.forEach(function(opt) {
            if (!niveau || opt.getAttribute('data-categorie') === niveau) {
                classeSelect.appendChild(opt);
            }
        });
        // Reset selection if current value is not in filtered list
        if (!classeSelect.querySelector('option[value="' + classeSelect.value + '"][data-categorie]')) {
            classeSelect.value = '';
        }
    }

    niveauSelect.addEventListener('change', filtrerClasses);

    // Apply filter on page load
    filtrerClasses();

    // Restore old value after filtering
    if (oldClasseId) {
        classeSelect.value = oldClasseId;
    }

    // Detection doublons (BUG-022)
    var inputPrenom = document.getElementById('input-prenom');
    var inputNom    = document.getElementById('input-nom');
    var alerteDiv   = document.getElementById('alerte-doublon');
    var msgSpan     = document.getElementById('msg-doublon');
    var timerDoublon;

    function verifierDoublon() {
        var prenom = (inputPrenom.value || '').trim();
        var nom    = (inputNom.value || '').trim();
        if (prenom.length < 2 || nom.length < 2) {
            alerteDiv.style.display = 'none';
            return;
        }
        clearTimeout(timerDoublon);
        timerDoublon = setTimeout(function() {
            fetch('{{ route("etudiants.verifier-doublon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ prenom: prenom, nom: nom })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.doublon) {
                    msgSpan.textContent = data.message;
                    alerteDiv.style.display = 'block';
                } else {
                    alerteDiv.style.display = 'none';
                }
            })
            .catch(function() { alerteDiv.style.display = 'none'; });
        }, 600);
    }

    inputPrenom.addEventListener('blur', verifierDoublon);
    inputNom.addEventListener('blur', verifierDoublon);
});
</script>
@endpush
