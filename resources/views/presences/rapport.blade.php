@extends('layouts.app')
@section('titre', 'Rapport de Presence')
@section('titre-page', 'Rapport de Presence')

@section('contenu')

<x-btn-retour :href="route('presences.index')" label="Retour aux presences" breadcrumb="Rapport" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
    <form action="{{ route('presences.rapport') }}" method="GET" class="flex items-center gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Classe</label>
            <select name="classe_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choisir --</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ $classeId == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
            <input type="date" name="debut" value="{{ $debut }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
            <input type="date" name="fin" value="{{ $fin }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <button type="submit" class="mt-5 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Generer</button>
    </form>
</div>

@if($rapport)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-green-600 uppercase">Presents</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-red-500 uppercase">Absents</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-yellow-500 uppercase">Retards</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-blue-400 uppercase">Excuses</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Taux</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($rapport as $ligne)
            @php $taux = $ligne['total'] > 0 ? round(($ligne['present'] / $ligne['total']) * 100) : 0; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $ligne['etudiant']->nom_complet }}</td>
                <td class="px-5 py-2.5 text-center text-green-600 font-semibold">{{ $ligne['present'] }}</td>
                <td class="px-5 py-2.5 text-center text-red-500 font-semibold">{{ $ligne['absent'] }}</td>
                <td class="px-5 py-2.5 text-center text-yellow-500 font-semibold">{{ $ligne['retard'] }}</td>
                <td class="px-5 py-2.5 text-center text-blue-400 font-semibold">{{ $ligne['excuse'] }}</td>
                <td class="px-5 py-2.5 text-center text-gray-600">{{ $ligne['total'] }}</td>
                <td class="px-5 py-2.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $taux >= 80 ? 'bg-green-100 text-green-700' : ($taux >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600') }}">
                        {{ $taux }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
