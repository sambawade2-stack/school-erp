@extends('layouts.app')
@section('titre', 'Rapport Paiements')
@section('titre-page', 'Rapport de Paiements')

@section('contenu')

<x-btn-retour :href="route('rapports.index')" label="Retour aux rapports" breadcrumb="Paiements" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
    <form action="{{ route('rapports.paiements') }}" method="GET" class="flex gap-3 items-center">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
            <input type="date" name="debut" value="{{ $debut }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
            <input type="date" name="fin" value="{{ $fin }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="mt-5">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Generer</button>
        </div>
        <div class="mt-5">
            <a href="{{ route('rapports.paiements', ['debut' => $debut, 'fin' => $fin, 'format' => 'pdf']) }}"
               class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </a>
        </div>
    </form>
</div>

<div class="grid grid-cols-2 gap-4 mb-5">
    <div class="bg-green-50 border border-green-100 rounded-xl p-4">
        <p class="text-xs text-green-600 font-medium">Total sur la periode</p>
        <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($total, 0, ',', ' ') }} XOF</p>
        <p class="text-xs text-green-600 mt-1">{{ $paiements->count() }} paiement(s)</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Montant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Recu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($paiements as $paiement)
            <tr>
                <td class="px-5 py-2.5 font-medium text-gray-800">{{ $paiement->etudiant->nom_complet }}</td>
                <td class="px-5 py-2.5 font-semibold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</td>
                <td class="px-5 py-2.5 text-gray-500">{{ ucfirst($paiement->type_paiement) }}</td>
                <td class="px-5 py-2.5 text-gray-500">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                <td class="px-5 py-2.5 text-gray-400 font-mono text-xs">{{ $paiement->numero_recu }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Aucun paiement sur cette periode</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
