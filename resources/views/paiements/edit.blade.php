@extends('layouts.app')
@section('titre', 'Modifier Paiement - ' . $paiement->numero_recu)
@section('titre-page', 'Modifier le Paiement')

@section('contenu')

<x-btn-retour :href="route('paiements.show', $paiement)" label="Retour au recu" breadcrumb="Modifier paiement" />

<div class="max-w-xl">
<form action="{{ route('paiements.update', $paiement) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Eleve <span class="text-red-500">*</span></label>
            <select name="etudiant_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choisir un eleve --</option>
                @foreach($etudiants as $etu)
                <option value="{{ $etu->id }}" {{ old('etudiant_id', $paiement->etudiant_id) == $etu->id ? 'selected' : '' }}>
                    {{ $etu->nom_complet }} {{ $etu->classe ? '(' . $etu->classe->nom . ')' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant (DH) <span class="text-red-500">*</span></label>
                <input type="number" name="montant" value="{{ old('montant', $paiement->montant) }}" step="0.01" min="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                <select name="type_paiement" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach(\App\Models\Tarif::TYPES as $val => $label)
                    <option value="{{ $val }}" {{ old('type_paiement', $paiement->type_paiement) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date_paiement" value="{{ old('date_paiement', $paiement->date_paiement->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Trimestre</label>
                <select name="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucun --</option>
                    <option value="T1" {{ old('trimestre', $paiement->trimestre) === 'T1' ? 'selected' : '' }}>T1</option>
                    <option value="T2" {{ old('trimestre', $paiement->trimestre) === 'T2' ? 'selected' : '' }}>T2</option>
                    <option value="T3" {{ old('trimestre', $paiement->trimestre) === 'T3' ? 'selected' : '' }}>T3</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Annee scolaire <span class="text-red-500">*</span></label>
            <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $paiement->annee_scolaire) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Enregistrer les modifications</button>
        <a href="{{ route('paiements.show', $paiement) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Annuler</a>
    </div>
</form>
</div>
@endsection
