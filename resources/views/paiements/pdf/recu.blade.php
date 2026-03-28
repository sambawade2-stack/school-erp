<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu {{ $paiement->numero_recu }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; }
        .header-logo { width: 110px; height: auto; max-height: 110px; }
        .header-info { flex: 1; }
        .header-info h1 { margin: 0 0 4px 0; font-size: 16px; color: #111827; }
        .header-info p { margin: 2px 0; font-size: 11px; color: #6b7280; }
        .header-ref { text-align: right; }
        .header-ref .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin-bottom: 4px; }
        .header-ref .numero { font-family: monospace; font-size: 15px; font-weight: 700; color: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        tr { border-bottom: 1px solid #f3f4f6; }
        td { padding: 10px 4px; }
        td:first-child { color: #6b7280; width: 45%; }
        td:last-child { font-weight: 600; color: #111827; text-align: right; }
        .montant-row { background: #f0fdf4; }
        .montant-row td:first-child { padding-left: 8px; }
        .montant-row td:last-child { font-size: 22px; font-weight: 700; color: #16a34a; padding-right: 8px; }
        .remarque { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; }
        .remarque .label { font-size: 11px; font-weight: 700; color: #92400e; margin-bottom: 4px; }
        .remarque .text { font-size: 12px; color: #78350f; }
        .footer { text-align: center; margin-top: 32px; font-size: 10px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        @if($etablissement && $etablissement->logo)
        <img src="{{ storage_path('app/public/logo/' . basename($etablissement->logo)) }}" alt="Logo" class="header-logo">
        @endif
        <div class="header-info">
            <h1>{{ $etablissement?->nom ?? 'ÉTABLISSEMENT' }}</h1>
            @if($etablissement?->sigle)<p>{{ $etablissement->sigle }}</p>@endif
            @if($etablissement?->adresse)<p>{{ $etablissement->adresse }}</p>@endif
            @if($etablissement?->telephone || $etablissement?->email)
            <p>
                @if($etablissement->telephone)Tél : {{ $etablissement->telephone }}@endif
                @if($etablissement->telephone && $etablissement->email) &nbsp;·&nbsp; @endif
                @if($etablissement->email){{ $etablissement->email }}@endif
            </p>
            @endif
        </div>
        <div class="header-ref">
            <div class="label">Reçu de Paiement</div>
            <div class="numero">{{ $paiement->numero_recu }}</div>
        </div>
    </div>

    <table>
        <tbody>
            <tr>
                <td>Élève</td>
                <td>{{ $paiement->etudiant->nom_complet }}</td>
            </tr>
            <tr>
                <td>Classe</td>
                <td>{{ $paiement->etudiant->classe?->nom ?? '—' }}</td>
            </tr>
            {{-- Détail des frais sélectionnés --}}
            @if($paiement->lignes && count($paiement->lignes) > 1)
            @foreach($paiement->lignes as $ligne)
            <tr>
                <td style="font-size: 0.82rem; color: #555;">
                    <span style="font-weight:600;text-transform:uppercase;font-size:0.7rem;color:#888;margin-right:4px;">{{ \App\Models\Tarif::TYPES[$ligne['type']] ?? ucfirst($ligne['type']) }}</span>
                    {{ $ligne['libelle'] }}
                </td>
                <td style="font-size: 0.82rem; color: #1d4ed8; font-weight: 600;">{{ number_format($ligne['montant'], 0, ',', ' ') }} XOF</td>
            </tr>
            @endforeach
            @endif

            <tr class="montant-row">
                <td>Montant versé</td>
                <td>{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</td>
            </tr>
            @if($paiement->montant_total && $paiement->montant_total != $paiement->montant)
            <tr>
                <td>Total dû</td>
                <td>{{ number_format($paiement->montant_total, 0, ',', ' ') }} XOF</td>
            </tr>
            <tr>
                <td style="color: #dc2626;">Restant dû</td>
                <td style="color: #dc2626; font-weight: 700;">{{ number_format($paiement->montant_restant, 0, ',', ' ') }} XOF</td>
            </tr>
            @endif
            <tr>
                <td>Type de paiement</td>
                <td>{{ \App\Models\Tarif::TYPES[$paiement->type_paiement] ?? ucfirst($paiement->type_paiement) }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Année scolaire</td>
                <td>{{ $paiement->annee_scolaire }}</td>
            </tr>
            @if($paiement->trimestre)
            <tr>
                <td>Trimestre</td>
                <td>{{ $paiement->trimestre }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($paiement->remarque)
    <div class="remarque">
        <div class="label">Remarque</div>
        <div class="text">{{ $paiement->remarque }}</div>
    </div>
    @endif

    {{-- Zone de signature --}}
    <div style="margin-top: 30px; text-align: right;">
        <div style="display: inline-block; width: 200px; text-align: center;">
            <div style="font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Le Responsable</div>
            <div style="font-size: 0.68rem; color: #9ca3af; margin-bottom: 30px;">Signature &amp; cachet</div>
            <div style="border-top: 1px solid #d1d5db;"></div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
