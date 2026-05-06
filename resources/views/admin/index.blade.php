@extends('layouts.app')
@section('titre', 'Administration')
@section('titre-page', 'Administration')

@section('contenu')

@php $anneeActive = \App\Models\AnneeScolaire::active(); @endphp

{{-- Navigation admin par onglets --}}
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-4">
    <a href="{{ route('admin.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
              {{ request()->routeIs('admin.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Établissement
    </a>
    <a href="{{ route('admin.tarifs.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
              {{ request()->routeIs('admin.tarifs.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M9 14h.01M15 14h.01M12 14h.01M15 7h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Grille tarifaire
    </a>
    <a href="{{ route('admin.annees.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
              {{ request()->routeIs('admin.annees.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Années scolaires
        @if($anneeActive)
        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">{{ $anneeActive->libelle }}</span>
        @else
        <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-xs rounded-full font-semibold">!</span>
        @endif
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors
              {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Utilisateurs
    </a>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-lg text-gray-800 mb-5">Informations de l'Établissement</h3>

        <form action="{{ route('admin.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $etablissement->nom ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sigle</label>
                    <input type="text" name="sigle" value="{{ old('sigle', $etablissement->sigle ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                <textarea name="adresse" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('adresse', $etablissement->adresse ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $etablissement->telephone ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $etablissement->email ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Directeur</label>
                    <input type="text" name="directeur" value="{{ old('directeur', $etablissement->directeur ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pays <span class="text-red-500">*</span></label>
                    <input type="text" name="pays" value="{{ old('pays', $etablissement->pays ?? 'Maroc') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $etablissement->ville ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Code Postal</label>
                    <input type="text" name="code_postal" value="{{ old('code_postal', $etablissement->code_postal ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description', $etablissement->description ?? '') }}</textarea>
            </div>

            {{-- DATE LIMITE DE PAIEMENT --}}
            <div class="border border-orange-200 bg-orange-50 rounded-lg p-4">
                <label class="block text-sm font-semibold text-orange-800 mb-1">
                    Jour limite de paiement mensuel
                </label>
                <p class="text-xs text-orange-600 mb-3">
                    Jour du mois à partir duquel les élèves n'ayant pas payé leur mensualité apparaissent en alerte sur le tableau de bord.
                    Laisser vide pour désactiver l'alerte.
                </p>
                <div class="flex items-center gap-3">
                    <input type="number" name="jour_limite_paiement" min="1" max="28"
                           value="{{ old('jour_limite_paiement', $etablissement->jour_limite_paiement ?? '') }}"
                           placeholder="ex: 10"
                           class="w-28 border border-orange-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none bg-white">
                    <span class="text-sm text-orange-700">du mois</span>
                    @if($etablissement && $etablissement->jour_limite_paiement)
                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded-full font-medium">
                        Actuel : le {{ $etablissement->jour_limite_paiement }}
                    </span>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Logo</label>
                @if($etablissement && $etablissement->logo)
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <img src="{{ route('logo.etablissement') }}"
                         alt="Logo Établissement"
                         class="h-20 object-contain"
                         style="max-width: 100%;">
                </div>
                @endif
                <input type="file" name="logo" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF (Max 2 Mo)</p>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Enregistrer les modifications
                </button>
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
