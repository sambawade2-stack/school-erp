@extends('layouts.app')
@section('titre', 'Paiements')
@section('titre-page', 'Gestion des Paiements')

@section('contenu')

@php
    $moisNomFiltre = \Carbon\Carbon::create($anneeFiltre, $moisFiltre, 1)->locale('fr')->translatedFormat('F Y');
@endphp

@if(session('recu_groupe_ids'))
@php $recuIds = session('recu_groupe_ids'); @endphp
<div id="banner-recu" class="mb-4 flex items-center justify-between gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span>{{ session('succes') }} — Le reçu groupé est en cours de téléchargement…</span>
    </div>
    <a id="lien-recu-groupe" href="{{ route('paiements.recu-groupe', ['ids' => $recuIds]) }}"
       class="flex items-center gap-2 px-4 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 whitespace-nowrap flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Retélécharger
    </a>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lien = document.getElementById('lien-recu-groupe');
        if (lien) {
            // Téléchargement automatique via iframe invisible
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = lien.href;
            document.body.appendChild(iframe);
        }
    });
</script>
@endif

<div x-data="{
    selectedIds: [],
    allIds: {{ $paiements->pluck('id')->toJson() }},
    get allSelected() { return this.allIds.length > 0 && this.allIds.every(id => this.selectedIds.includes(id)); },
    toggleAll() {
        if (this.allSelected) { this.selectedIds = []; }
        else { this.selectedIds = [...this.allIds]; }
    },
    downloadGrouped() {
        const params = this.selectedIds.map(id => 'ids[]=' + id).join('&');
        window.location.href = '{{ route('paiements.recu-groupe') }}?' + params;
    }
}">

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h2 class="text-lg font-semibold text-gray-700">Paiements du mois de {{ $moisNomFiltre }}</h2>
    <div class="flex items-center gap-2">
        {{-- Barre d'actions sélection --}}
        <div x-show="selectedIds.length > 0" x-cloak
             class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg">
            <span class="text-sm text-indigo-700 font-medium" x-text="selectedIds.length + ' sélectionné(s)'"></span>
            <button @click="downloadGrouped()"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF groupé
            </button>
            <button @click="selectedIds = []" class="p-1 text-indigo-400 hover:text-indigo-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <a href="{{ route('paiements.export.pdf', ['mois' => $moisFiltre, 'annee' => $anneeFiltre, 'type' => request('type')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <a href="{{ route('paiements.export.csv', ['mois' => $moisFiltre, 'annee' => $anneeFiltre, 'type' => request('type')]) }}"
           class="flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            CSV
        </a>
        <a href="{{ route('paiements.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau paiement
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
    {{-- Total du mois --}}
    <div class="rounded-xl p-4" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <p class="text-xs font-medium" style="color: #16a34a;">Total du mois</p>
        <p class="text-xl font-bold mt-1" style="color: #15803d;">{{ number_format($totalFiltre, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: #16a34a; opacity: 0.7;">XOF</p>
    </div>
    {{-- Total année --}}
    <div class="rounded-xl p-4" style="background: #eff6ff; border: 1px solid #bfdbfe;">
        <p class="text-xs font-medium" style="color: #2563eb;">Total cette année</p>
        <p class="text-xl font-bold mt-1" style="color: #1d4ed8;">{{ number_format($totalAnnee, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: #2563eb; opacity: 0.7;">XOF</p>
    </div>
    {{-- Carte regroupée Inscription (4 types) --}}
    @if($totalInscription > 0)
    <div class="rounded-xl p-4" style="background: #f0f9ff; border: 1px solid #7dd3fc;">
        <p class="text-xs font-medium" style="color: #0369a1;">Inscription</p>
        <p class="text-xl font-bold mt-1" style="color: #0369a1;">{{ number_format($totalInscription, 0, ',', ' ') }}</p>
        <p class="text-xs mt-0.5" style="color: #0369a1; opacity: 0.7;">XOF</p>
    </div>
    @endif
    {{-- Autres types individuels (Mensualité, Logement, etc.) --}}
    @foreach($typesFrais as $typeKey => $typeLabel)
        @php $colors = $typeColors[$typeKey] ?? ['color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb']; @endphp
        @if(($totauxParType[$typeKey] ?? 0) > 0)
        <div class="rounded-xl p-4" style="background: {{ $colors['bg'] }}; border: 1px solid {{ $colors['border'] }};">
            <p class="text-xs font-medium" style="color: {{ $colors['color'] }};">{{ $typeLabel }}</p>
            <p class="text-xl font-bold mt-1" style="color: {{ $colors['color'] }};">{{ number_format($totauxParType[$typeKey], 0, ',', ' ') }}</p>
            <p class="text-xs mt-0.5" style="color: {{ $colors['color'] }}; opacity: 0.7;">XOF</p>
        </div>
        @endif
    @endforeach
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form action="{{ route('paiements.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Eleve ou num. recu..."
               class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">
        <select name="type" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:180px;">
            <option value="">Tous types</option>
            <option value="groupe_inscription" {{ request('type') === 'groupe_inscription' ? 'selected' : '' }}>Frais d'Inscription</option>
            @foreach($typesFrais as $typeKey => $typeLabel)
            <option value="{{ $typeKey }}" {{ request('type') === $typeKey ? 'selected' : '' }}>{{ $typeLabel }}</option>
            @endforeach
        </select>
        <select name="mois" class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:180px;">
            <option value="">Tous les mois</option>
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('fr')->translatedFormat('F') }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        @if(request()->hasAny(['recherche', 'type', 'mois']))
        <a href="{{ route('paiements.index') }}" class="px-4 py-2 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Effacer</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 w-8">
                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600"
                           :checked="allSelected"
                           @change="toggleAll()"
                           title="Tout sélectionner">
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Eleve</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Montant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Trimestre</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Facture</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($paiements as $paiement)
            <tr class="hover:bg-gray-50" :class="selectedIds.includes({{ $paiement->id }}) ? 'bg-indigo-50' : ''">
                <td class="px-4 py-3 w-8">
                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600"
                           :value="{{ $paiement->id }}"
                           x-model="selectedIds">
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $paiement->etudiant->nom_complet }}</td>
                <td class="px-5 py-3">
                    <span class="font-semibold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</span>
                    @if($paiement->montant_total && $paiement->montant_total != $paiement->montant)
                    <span class="text-xs text-gray-400 block">/ {{ number_format($paiement->montant_total, 0, ',', ' ') }}</span>
                    @endif
                    @php
                        $st = $paiement->statut ?? 'complet';
                        $stClass = $st === 'complet' ? 'bg-green-100 text-green-700' : ($st === 'partiel' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                        $stLabel = $st === 'complet' ? 'Complet' : ($st === 'partiel' ? 'Partiel' : 'Non payé');
                    @endphp
                    <span class="inline-block px-1.5 py-0.5 rounded text-xs font-medium {{ $stClass }}">{{ $stLabel }}</span>
                </td>
                <td class="px-5 py-3">
                    @php $tc = \App\Models\Tarif::TYPE_COLORS[$paiement->type_paiement] ?? ['color'=>'#6b7280','bg'=>'#f9fafb']; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                          style="background:{{ $tc['bg'] }}; color:{{ $tc['color'] }};">
                        {{ \App\Models\Tarif::TYPES[$paiement->type_paiement] ?? ucfirst($paiement->type_paiement) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">
                    {{ $paiement->date_paiement->format('d/m/Y') }}
                    <span class="block text-xs text-gray-400">{{ $paiement->created_at->format('H:i') }}</span>
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $paiement->trimestre ?? '—' }}</td>
                <td class="px-5 py-3 font-mono text-xs">
                    @if($paiement->numero_facture)
                    <span class="font-semibold text-blue-700">{{ $paiement->numero_facture }}</span>
                    <span class="block text-gray-400 text-xs">{{ $paiement->numero_recu }}</span>
                    @else
                    <span class="text-gray-400">{{ $paiement->numero_recu }}</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex gap-2">
                        @if($paiement->statut === 'partiel' && $paiement->montant_restant > 0)
                        {{-- Payer le solde restant --}}
                        <a href="{{ route('paiements.create', [
                               'etudiant_id'    => $paiement->etudiant_id,
                               'type_paiement'  => $paiement->type_paiement,
                               'trimestre'      => $paiement->trimestre,
                               'montant_restant'=> $paiement->montant_restant,
                           ]) }}"
                           title="Payer le solde restant : {{ number_format($paiement->montant_restant, 0, ',', ' ') }} XOF"
                           class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </a>
                        @endif
                        @if($paiement->numero_facture)
                        {{-- Aperçu reçu groupé --}}
                        <a href="{{ route('paiements.recu-groupe', ['facture' => $paiement->numero_facture, 'preview' => 1]) }}"
                           target="_blank"
                           title="Aperçu — {{ $paiement->numero_facture }}"
                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Voir
                        </a>
                        {{-- Télécharger reçu groupé --}}
                        <a href="{{ route('paiements.recu-groupe', ['facture' => $paiement->numero_facture]) }}"
                           title="Télécharger — {{ $paiement->numero_facture }}"
                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Reçu
                        </a>
                        @else
                        {{-- Voir paiement individuel --}}
                        <a href="{{ route('paiements.show', $paiement) }}"
                           title="Voir le paiement"
                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Voir
                        </a>
                        {{-- Télécharger reçu individuel --}}
                        <a href="{{ route('paiements.pdf', $paiement) }}"
                           title="Télécharger le reçu"
                           class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Reçu
                        </a>
                        @endif
                        <form action="{{ route('paiements.destroy', $paiement) }}" method="POST" onsubmit="return confirm('Supprimer ce paiement ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucun paiement trouve.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($paiements->hasPages())
    <div class="px-5 py-4 border-t">{{ $paiements->links() }}</div>
    @endif
</div>

</div>{{-- fin x-data --}}
@endsection
