@extends('layouts.app')
@section('titre', 'Dépenses')
@section('titre-page', 'Gestion des Dépenses')

@section('contenu')

@php
    $catInfos = [
        'fournitures'   => ['label' => 'Fournitures',   'color' => '#3b82f6', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
        'salaires'      => ['label' => 'Salaires',      'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
        'maintenance'   => ['label' => 'Maintenance',   'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fde68a'],
        'transport'     => ['label' => 'Transport',     'color' => '#06b6d4', 'bg' => '#ecfeff', 'border' => '#a5f3fc'],
        'alimentation'  => ['label' => 'Alimentation',  'color' => '#10b981', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
        'autre'         => ['label' => 'Autre',         'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
    ];
    $moisNomFiltre = \Carbon\Carbon::create($anneeFiltre, $moisFiltre, 1)->locale('fr')->translatedFormat('F Y');
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h2 class="text-lg font-semibold text-gray-700">Dépenses du mois de {{ $moisNomFiltre }}</h2>
    <div class="flex items-center gap-2">
        <a href="{{ route('depenses.export.pdf', ['mois' => $moisFiltre, 'annee' => $anneeFiltre, 'categorie' => request('categorie')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <a href="{{ route('depenses.export.csv', ['mois' => $moisFiltre, 'annee' => $anneeFiltre, 'categorie' => request('categorie')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            CSV
        </a>
        <a href="{{ route('depenses.create') }}"
           class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle dépense
        </a>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="rounded-xl p-4" style="background: #fef2f2; border: 1px solid #fecaca;">
        <p class="text-xs font-medium" style="color: #dc2626;">Total du mois</p>
        <p class="text-xl font-bold mt-1" style="color: #b91c1c;">{{ number_format($totalMois, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: #dc2626; opacity: 0.7;">XOF</p>
    </div>
    <div class="rounded-xl p-4" style="background: #fef2f2; border: 1px solid #fecaca;">
        <p class="text-xs font-medium" style="color: #dc2626;">Total cette année</p>
        <p class="text-xl font-bold mt-1" style="color: #b91c1c;">{{ number_format($totalAnnee, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: #dc2626; opacity: 0.7;">XOF</p>
    </div>
    @foreach(['fournitures', 'salaires'] as $catKey)
    <a href="{{ route('depenses.create', ['categorie' => $catKey]) }}" class="rounded-xl p-4 block transition-opacity hover:opacity-80" style="background: {{ $catInfos[$catKey]['bg'] }}; border: 1px solid {{ $catInfos[$catKey]['border'] }}; text-decoration:none;">
        <p class="text-xs font-medium" style="color: {{ $catInfos[$catKey]['color'] }};">{{ $catInfos[$catKey]['label'] }}</p>
        <p class="text-xl font-bold mt-1" style="color: {{ $catInfos[$catKey]['color'] }};">{{ number_format($totauxParCategorie[$catKey] ?? 0, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: {{ $catInfos[$catKey]['color'] }}; opacity: 0.7;">XOF</p>
        <p class="text-xs mt-2 font-medium" style="color: {{ $catInfos[$catKey]['color'] }};">+ Dépenser</p>
    </a>
    @endforeach
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @foreach(['maintenance', 'transport', 'alimentation', 'autre'] as $catKey)
    <a href="{{ route('depenses.create', ['categorie' => $catKey]) }}" class="rounded-xl p-4 block transition-opacity hover:opacity-80" style="background: {{ $catInfos[$catKey]['bg'] }}; border: 1px solid {{ $catInfos[$catKey]['border'] }}; text-decoration:none;">
        <p class="text-xs font-medium" style="color: {{ $catInfos[$catKey]['color'] }};">{{ $catInfos[$catKey]['label'] }}</p>
        <p class="text-xl font-bold mt-1" style="color: {{ $catInfos[$catKey]['color'] }};">{{ number_format($totauxParCategorie[$catKey] ?? 0, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: {{ $catInfos[$catKey]['color'] }}; opacity: 0.7;">XOF</p>
        <p class="text-xs mt-2 font-medium" style="color: {{ $catInfos[$catKey]['color'] }};">+ Dépenser</p>
    </a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form action="{{ route('depenses.index') }}" method="GET" class="flex gap-3 items-center flex-wrap">
        <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Libellé ou bénéficiaire..."
               class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
        <select name="categorie" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:200px;">
            <option value="">Toutes catégories</option>
            @foreach($catInfos as $key => $info)
            <option value="{{ $key }}" {{ request('categorie') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
        </select>
        <select name="mois" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:180px;">
            <option value="">Tous les mois</option>
            @for($m = 1; $m <= now()->month; $m++)
            <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('fr')->translatedFormat('F') }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        @if(request()->hasAny(['recherche', 'categorie', 'mois']))
        <a href="{{ route('depenses.index') }}" class="px-4 py-2 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Effacer</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Libellé</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Montant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catégorie</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bénéficiaire</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($depenses as $depense)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $depense->libelle }}</td>
                <td class="px-5 py-3">
                    <span class="font-semibold text-red-600">{{ number_format($depense->montant, 0, ',', ' ') }} XOF</span>
                </td>
                <td class="px-5 py-3">
                    @php $ci = $catInfos[$depense->categorie] ?? $catInfos['autre']; @endphp
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium" style="background: {{ $ci['bg'] }}; color: {{ $ci['color'] }};">
                        {{ $ci['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $depense->date_depense->format('d/m/Y') }}</td>
                <td class="px-5 py-3 text-gray-400">{{ $depense->beneficiaire ?? '—' }}</td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('depenses.edit', $depense) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('depenses.destroy', $depense) }}" method="POST" onsubmit="return confirm('Supprimer cette dépense ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune dépense enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($depenses->hasPages())
    <div class="px-5 py-4 border-t">{{ $depenses->links() }}</div>
    @endif
</div>
@endsection
