@extends('layouts.app')
@section('titre', 'Rapports')
@section('titre-page', 'Centre de Rapports')

@section('contenu')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <a href="{{ route('rapports.etudiants') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-200 transition-all group">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors">
            <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Liste des Etudiants</h3>
        <p class="text-sm text-gray-500">Exporter la liste complete des etudiants par classe</p>
    </a>

    <a href="{{ route('rapports.paiements') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-green-200 transition-all group">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-600 transition-colors">
            <svg class="w-6 h-6 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Rapport de Paiements</h3>
        <p class="text-sm text-gray-500">Historique et statistiques des paiements par periode</p>
    </a>

    <a href="{{ route('rapports.presences') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-orange-200 transition-all group">
        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-orange-600 transition-colors">
            <svg class="w-6 h-6 text-orange-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Rapport de Presences</h3>
        <p class="text-sm text-gray-500">Taux de presence par classe et par periode</p>
    </a>

    <a href="{{ route('rapports.bulletins') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-purple-200 transition-all group">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-600 transition-colors">
            <svg class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Télécharger Bulletins</h3>
        <p class="text-sm text-gray-500">Télécharger les bulletins en masse ou individuellement</p>
    </a>

</div>
@endsection
