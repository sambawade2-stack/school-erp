@extends('layouts.app')
@section('titre', 'Parametres')
@section('titre-page', 'Parametres et Sauvegardes')

@section('contenu')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Sauvegarde --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Sauvegarde de la base</h3>
                <p class="text-xs text-gray-400">Copie de securite des donnees</p>
            </div>
        </div>

        <form action="{{ route('parametres.sauvegarder') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Creer une sauvegarde maintenant
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-3 text-center">
            La sauvegarde sera stockee dans <code class="bg-gray-100 px-1 rounded">storage/app/sauvegardes/</code>
        </p>
    </div>

    {{-- Restauration --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Restaurer une sauvegarde</h3>
                <p class="text-xs text-gray-400 text-orange-500">Attention : remplacera toutes les donnees actuelles</p>
            </div>
        </div>

        @if(count($sauvegardes) > 0)
        <form action="{{ route('parametres.restaurer') }}" method="POST" onsubmit="return confirm('Attention ! Cette action remplacera TOUTES les donnees actuelles par la sauvegarde selectionnee. Continuer ?')">
            @csrf
            <select name="fichier" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none mb-3">
                <option value="">-- Choisir une sauvegarde --</option>
                @foreach($sauvegardes as $sauvegarde)
                <option value="{{ $sauvegarde['nom'] }}">{{ $sauvegarde['nom'] }} ({{ $sauvegarde['date'] }}) - {{ $sauvegarde['size'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full px-4 py-3 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                Restaurer la sauvegarde
            </button>
        </form>
        @else
        <p class="text-sm text-gray-400 text-center py-4">Aucune sauvegarde disponible</p>
        @endif
    </div>

    {{-- Mot de passe --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Changer le mot de passe</h3>
                <p class="text-xs text-gray-400">Modifier le mot de passe de votre compte</p>
            </div>
        </div>

        @if($errors->has('mot_de_passe_actuel'))
        <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600">
            {{ $errors->first('mot_de_passe_actuel') }}
        </div>
        @endif

        <form action="{{ route('parametres.mot-de-passe') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Mot de passe actuel</label>
                <input type="password" name="mot_de_passe_actuel" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nouveau mot de passe</label>
                <input type="password" name="nouveau_mot_de_passe" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Confirmer le nouveau mot de passe</label>
                <input type="password" name="nouveau_mot_de_passe_confirmation" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                @error('nouveau_mot_de_passe')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                    class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                Enregistrer le nouveau mot de passe
            </button>
        </form>
    </div>

    {{-- Info systeme --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
        <h3 class="font-semibold text-gray-800 mb-4">Informations Systeme</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Version PHP</p>
                <p class="font-semibold text-gray-700">{{ PHP_VERSION }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Version Laravel</p>
                <p class="font-semibold text-gray-700">{{ app()->version() }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Base de donnees</p>
                <p class="font-semibold text-gray-700">SQLite</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Nombre de sauvegardes</p>
                <p class="font-semibold text-gray-700">{{ count($sauvegardes) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Date et heure</p>
                <p class="font-semibold text-gray-700">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-400 text-xs mb-1">Taille DB</p>
                <p class="font-semibold text-gray-700">{{ $dbSize }}</p>
            </div>
        </div>
    </div>

    {{-- Liste des sauvegardes --}}
    @if(count($sauvegardes) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
        <h3 class="font-semibold text-gray-800 mb-4">Historique des Sauvegardes</h3>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fichier</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Taille</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($sauvegardes as $sauvegarde)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5 font-mono text-xs text-gray-700">{{ $sauvegarde['nom'] }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $sauvegarde['date'] }}</td>
                    <td class="px-4 py-2.5 text-gray-400">{{ $sauvegarde['size'] }}</td>
                    <td class="px-4 py-2.5">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('parametres.telecharger', $sauvegarde['nom']) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Télécharger
                            </a>
                            <form action="{{ route('parametres.supprimer-sauvegarde') }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette sauvegarde ?')" class="inline">
                                @csrf
                                <input type="hidden" name="fichier" value="{{ $sauvegarde['nom'] }}">
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
    </div>
    @endif

</div>
@endsection
