@extends('layouts.app')
@section('titre', 'Reçu - ' . $paiement->numero_recu)
@section('titre-page', 'Reçu de Paiement')

@push('scripts')
<style>
    @media print {
        aside, header, .no-print { display: none !important; }
        body { background: white !important; }
        main { padding: 0 !important; overflow: visible !important; }
        .print-card { box-shadow: none !important; border: none !important; max-width: 100% !important; }
    }
</style>
@endpush

@section('contenu')

<div class="no-print">
    <x-btn-retour :href="route('paiements.index')" label="Retour aux paiements" />
</div>

<div class="max-w-xl mt-6 print-card">
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- ENTÊTE ÉTABLISSEMENT --}}
    <div style="padding: 24px 28px; border-bottom: 3px solid #1d4ed8; display: flex; align-items: flex-start; gap: 16px;">
        @if($etablissement && $etablissement->logo)
        <img src="{{ url('storage/logo/' . $etablissement->logo) }}" alt="Logo" style="height: 56px; object-fit: contain; flex-shrink: 0;">
        @else
        <div style="width: 52px; height: 52px; background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700; color: #1d4ed8; flex-shrink: 0;">{{ substr($etablissement?->sigle ?? 'E', 0, 1) }}</div>
        @endif
        <div style="flex: 1;">
            <div style="font-size: 1.1rem; font-weight: 700; color: #111827; margin: 0 0 3px 0; font-family: 'Poppins', sans-serif;">{{ $etablissement?->nom ?? 'ÉTABLISSEMENT' }}</div>
            @if($etablissement?->sigle)
            <div style="font-size: 0.8rem; color: #6b7280; margin: 2px 0;">{{ $etablissement->sigle }}</div>
            @endif
            @if($etablissement?->adresse)
            <div style="font-size: 0.78rem; color: #6b7280; margin: 2px 0;">{{ $etablissement->adresse }}</div>
            @endif
            @if($etablissement?->telephone || $etablissement?->email)
            <div style="font-size: 0.78rem; color: #6b7280; margin: 2px 0;">
                @if($etablissement->telephone)Tél: {{ $etablissement->telephone }}@endif
                @if($etablissement->telephone && $etablissement->email) &nbsp;·&nbsp; @endif
                @if($etablissement->email){{ $etablissement->email }}@endif
            </div>
            @endif
        </div>
        <div style="text-align: right; flex-shrink: 0;">
            <div style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px;">Reçu de Paiement</div>
            <div style="font-family: monospace; font-size: 1rem; font-weight: 700; color: #1d4ed8; letter-spacing: 0.05em;">{{ $paiement->numero_recu }}</div>
        </div>
    </div>

    {{-- CORPS DU REÇU --}}
    <div style="padding: 24px 28px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <tbody>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280; width: 45%;">Élève</td>
                    <td style="padding: 10px 0; font-weight: 600; color: #111827; text-align: right;">{{ $paiement->etudiant->nom_complet }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280;">Classe</td>
                    <td style="padding: 10px 0; font-weight: 500; color: #374151; text-align: right;">{{ $paiement->etudiant->classe?->nom ?? '—' }}</td>
                </tr>
                {{-- Détail des frais sélectionnés --}}
                @if($paiement->lignes && count($paiement->lignes) > 1)
                @foreach($paiement->lignes as $ligne)
                <tr style="border-bottom: 1px dashed #e5e7eb;">
                    <td style="padding: 8px 0 8px 8px; color: #374151; font-size: 0.85rem;">
                        <span style="display:inline-block;font-size:0.7rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-right:6px;">{{ \App\Models\Tarif::TYPES[$ligne['type']] ?? ucfirst($ligne['type']) }}</span>
                        {{ $ligne['libelle'] }}
                    </td>
                    <td style="padding: 8px 8px 8px 0; font-weight: 600; color: #1d4ed8; text-align: right; font-size: 0.85rem;">{{ number_format($ligne['montant'], 0, ',', ' ') }} XOF</td>
                </tr>
                @endforeach
                @endif

                <tr style="border-bottom: 1px solid #f3f4f6; background: #f0fdf4;">
                    <td style="padding: 12px 0; color: #6b7280; padding-left: 8px;">Montant versé</td>
                    <td style="padding: 12px 0; font-size: 1.5rem; font-weight: 700; color: #16a34a; text-align: right; padding-right: 8px;">{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</td>
                </tr>
                @if($paiement->montant_total && $paiement->montant_total != $paiement->montant)
                <tr style="border-bottom: 1px solid #f3f4f6; background: #fefce8;">
                    <td style="padding: 10px 0; color: #6b7280; padding-left: 8px;">Montant total dû</td>
                    <td style="padding: 10px 0; font-weight: 700; color: #d97706; text-align: right; padding-right: 8px;">{{ number_format($paiement->montant_total, 0, ',', ' ') }} XOF</td>
                </tr>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280; padding-left: 8px;">Restant dû</td>
                    <td style="padding: 10px 0; font-weight: 700; color: #dc2626; text-align: right; padding-right: 8px;">{{ number_format($paiement->montant_restant, 0, ',', ' ') }} XOF</td>
                </tr>
                @endif
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280;">Type de paiement</td>
                    <td style="padding: 10px 0; font-weight: 500; color: #374151; text-align: right;">{{ ucfirst($paiement->type_paiement) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280;">Date</td>
                    <td style="padding: 10px 0; font-weight: 500; color: #374151; text-align: right;">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px 0; color: #6b7280;">Année scolaire</td>
                    <td style="padding: 10px 0; font-weight: 500; color: #374151; text-align: right;">{{ $paiement->annee_scolaire }}</td>
                </tr>
                @if($paiement->trimestre)
                <tr>
                    <td style="padding: 10px 0; color: #6b7280;">Trimestre</td>
                    <td style="padding: 10px 0; font-weight: 500; color: #374151; text-align: right;">{{ $paiement->trimestre }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($paiement->remarque)
        <div style="margin-top: 16px; padding: 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
            <div style="font-size: 0.78rem; font-weight: 600; color: #92400e; margin-bottom: 4px;">Remarque</div>
            <div style="font-size: 0.85rem; color: #78350f;">{{ $paiement->remarque }}</div>
        </div>
        @endif

        {{-- Statut --}}
        @php
            $statutClass = match($paiement->statut ?? 'complet') {
                'complet'  => 'background:#dcfce7;color:#166534;',
                'partiel'  => 'background:#fef9c3;color:#713f12;',
                'non_paye' => 'background:#fee2e2;color:#991b1b;',
                default    => 'background:#f3f4f6;color:#374151;',
            };
            $statutLabel = match($paiement->statut ?? 'complet') {
                'complet'  => 'Payé intégralement',
                'partiel'  => 'Paiement partiel',
                'non_paye' => 'Non payé',
                default    => ucfirst($paiement->statut ?? ''),
            };
        @endphp
        <div style="margin-top: 14px; display: flex; justify-content: center;">
            <span style="padding: 4px 14px; border-radius: 9999px; font-size: 0.78rem; font-weight: 600; {{ $statutClass }}">{{ $statutLabel }}</span>
        </div>

        {{-- Zone de signature --}}
        <div style="margin-top: 28px; display: flex; justify-content: flex-end;">
            <div style="width: 200px; text-align: center;">
                <div style="font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Le Responsable</div>
                <div style="font-size: 0.7rem; color: #9ca3af; margin-bottom: 30px;">Signature & cachet</div>
                <div style="border-top: 1px solid #d1d5db;"></div>
            </div>
        </div>

        <div style="margin-top: 20px; font-size: 0.72rem; color: #9ca3af; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 14px;">
            Document généré le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    {{-- BOUTONS (cachés à l'impression) --}}
    <div class="no-print" style="padding: 16px 28px; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; gap: 10px;">
        <button onclick="window.print()" style="flex: 1; padding: 10px; background: #1d4ed8; color: white; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer;">Imprimer</button>
        <a href="{{ route('paiements.pdf', $paiement) }}" style="flex: 1; padding: 10px; background: #dc2626; color: white; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-align: center; text-decoration: none;">PDF</a>
        <a href="{{ route('paiements.edit', $paiement) }}" style="flex: 1; padding: 10px; background: #d97706; color: white; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-align: center; text-decoration: none;">Modifier</a>
        <a href="{{ route('paiements.index') }}" style="flex: 1; padding: 10px; background: white; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-align: center; text-decoration: none;">Retour</a>
    </div>
</div>
</div>

{{-- SECTION TRANCHES (paiements partiels) --}}
@if($paiement->montant_total && $paiement->montant_restant > 0)
<div class="max-w-xl mt-4 no-print">
    <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ajouter une tranche de paiement
        </h3>
        <p class="text-xs text-gray-500 mb-3">Restant dû : <strong class="text-red-600">{{ number_format($paiement->montant_restant, 0, ',', ' ') }} XOF</strong></p>
        <form action="{{ route('tranches.store', $paiement) }}" method="POST" class="flex gap-2 items-end">
            @csrf
            @if($errors->has('montant'))
            <div class="col-span-full text-xs text-red-600 mb-1">{{ $errors->first('montant') }}</div>
            @endif
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Montant (XOF)</label>
                <input type="number" name="montant" step="0.01" min="0.01" max="{{ $paiement->montant_restant }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                <input type="date" name="date_paiement" value="{{ today()->format('Y-m-d') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1">Remarque</label>
                <input type="text" name="remarque" placeholder="Optionnel"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <button type="submit" style="background:#d97706;" class="px-4 py-2 text-white rounded-lg text-sm font-medium hover:opacity-90 whitespace-nowrap">+ Ajouter</button>
        </form>
    </div>
</div>
@endif

{{-- HISTORIQUE DES TRANCHES --}}
@if($paiement->tranches->count() > 0)
<div class="max-w-xl mt-3 no-print">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-3">Historique des versements</h3>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-500 uppercase">
                    <th class="px-3 py-2 text-left font-semibold">N° Reçu</th>
                    <th class="px-3 py-2 text-left font-semibold">Date</th>
                    <th class="px-3 py-2 text-left font-semibold">Montant</th>
                    <th class="px-3 py-2 text-left font-semibold">Remarque</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($paiement->tranches as $tranche)
                <tr>
                    <td class="px-3 py-2 font-mono text-xs text-gray-600">{{ $tranche->numero_recu }}</td>
                    <td class="px-3 py-2 text-gray-500">{{ $tranche->date_paiement->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 font-semibold text-green-600">{{ number_format($tranche->montant, 0, ',', ' ') }} XOF</td>
                    <td class="px-3 py-2 text-gray-400 text-xs">{{ $tranche->remarque ?? '—' }}</td>
                    <td class="px-3 py-2">
                        <form action="{{ route('tranches.destroy', $tranche) }}" method="POST" onsubmit="return confirm('Supprimer ce versement ?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
