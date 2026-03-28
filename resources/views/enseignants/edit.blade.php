@extends('layouts.app')
@section('titre', 'Modifier - ' . $enseignant->nom_complet)
@section('titre-page', 'Modifier le Personnel')

@section('contenu')

<x-btn-retour :href="route('enseignants.show', $enseignant)" label="Retour au profil" :breadcrumb="$enseignant->nom_complet" />

<div class="max-w-2xl">
<form action="{{ route('enseignants.update', $enseignant) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                <input type="text" name="prenom" value="{{ old('prenom', $enseignant->prenom) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $enseignant->nom) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach(\App\Models\Enseignant::TYPES as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $enseignant->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Spécialité</label>
                <input type="text" name="specialite" value="{{ old('specialite', $enseignant->specialite) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $enseignant->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $enseignant->telephone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'embauche</label>
                <input type="date" name="date_embauche" value="{{ old('date_embauche', $enseignant->date_embauche?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="actif" {{ old('statut', $enseignant->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ old('statut', $enseignant->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
            <textarea name="adresse" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('adresse', $enseignant->adresse) }}</textarea>
        </div>
        <x-webcam-photo name="photo" label="Photo (laisser vide pour conserver l'actuelle)" :current-url="$enseignant->photo ? $enseignant->photo_url : null" />
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Enregistrer</button>
        <a href="{{ route('enseignants.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
