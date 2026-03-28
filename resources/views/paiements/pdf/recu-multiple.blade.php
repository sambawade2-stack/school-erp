<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #1f2937; font-size: 14px; }

        /* Header */
        .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 22px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 0; }

        .logo-img { width: 160px; height: 120px; object-fit: contain; }
        .etab-nom { font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 3px 0; letter-spacing: 0.3px; }
        .etab-sigle { font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 3px 0; }
        .etab-detail { font-size: 12px; color: #6b7280; margin: 2px 0; }

        .recit-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.12em; color: #9ca3af; margin-bottom: 6px; }
        .numero-facture { font-size: 22px; font-weight: 800; color: #1d4ed8; font-family: monospace; letter-spacing: 1px; }

        /* Infos élève */
        .eleve-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
        .eleve-table { width: 100%; border-collapse: collapse; }
        .eleve-table td { padding: 4px 8px; font-size: 13px; }
        .eleve-table .lbl { color: #6b7280; width: 130px; }
        .eleve-table .val { font-weight: 600; color: #111827; }

        /* Tableau paiements */
        .paiements-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .paiements-table thead tr { background: #1d4ed8; color: white; }
        .paiements-table thead td { padding: 10px 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .paiements-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .paiements-table tbody tr:nth-child(even) { background: #f9fafb; }
        .paiements-table tbody td { padding: 10px 12px; font-size: 13px; }
        .badge-statut { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
        .badge-complet { background: #dcfce7; color: #166534; }
        .badge-partiel { background: #fef3c7; color: #92400e; }

        /* Totaux */
        .totaux table { width: 100%; border-collapse: collapse; }
        .totaux td { padding: 7px 12px; font-size: 14px; }
        .totaux td:first-child { color: #6b7280; }
        .totaux td:last-child { text-align: right; font-weight: 700; }
        .total-row td { background: #f0fdf4; color: #15803d; font-size: 17px; border-top: 2px solid #16a34a; }

        /* Remarque */
        .remarque { margin-top: 16px; padding: 10px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; }
        .remarque .lbl { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 4px; }
        .remarque .txt { font-size: 13px; color: #78350f; }

        /* Signature */
        .signature { margin-top: 28px; text-align: right; }
        .sig-box { display: inline-block; width: 200px; text-align: center; }
        .sig-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .sig-sub { font-size: 11px; color: #9ca3af; margin-bottom: 28px; }
        .sig-line { border-top: 1px solid #d1d5db; }

        .footer { text-align: center; margin-top: 24px; font-size: 11px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 10px; }
    </style>
</head>
<body>

    {{-- En-tête établissement --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="col-left" style="vertical-align: top;">
                    @if($etablissement && $etablissement->logo)
                    <img src="{{ storage_path('app/public/logo/' . basename($etablissement->logo)) }}"
                         alt="Logo" class="logo-img" style="display:block; margin-bottom: 24px;">
                    @endif
                    <p class="etab-nom">{{ $etablissement?->nom ?? 'ÉTABLISSEMENT' }}</p>
                    @if($etablissement?->sigle)
                    <p class="etab-sigle">{{ $etablissement->sigle }}</p>
                    @endif
                    @if($etablissement?->adresse)
                    <p class="etab-detail">{{ $etablissement->adresse }}</p>
                    @endif
                    @if($etablissement?->telephone)
                    <p class="etab-detail">Tél : {{ $etablissement->telephone }}</p>
                    @endif
                    @if($etablissement?->email)
                    <p class="etab-detail">{{ $etablissement->email }}</p>
                    @endif
                </td>
                <td class="col-numero" style="vertical-align: top; text-align: right; width: 220px;">
                    <div class="recit-label">Reçu de Paiement</div>
                    @php $numeroFacture = $paiementsCreated->first()->numero_facture ?? $paiementsCreated->first()->numero_recu; @endphp
                    <div class="numero-facture">{{ $numeroFacture }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Infos élève --}}
    <div class="eleve-info">
        <table class="eleve-table">
            <tr>
                <td class="lbl">Élève</td>
                <td class="val">{{ $etudiant->nom_complet }}</td>
                <td class="lbl" style="width:120px;">Classe</td>
                <td class="val">{{ $etudiant->classe?->nom ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Année scolaire</td>
                <td class="val">{{ $paiementsCreated->first()->annee_scolaire }}</td>
                <td class="lbl">Date</td>
                <td class="val">{{ $paiementsCreated->first()->date_paiement->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Tableau des paiements --}}
    <table class="paiements-table">
        <thead>
            <tr>
                <td>Type de frais</td>
                <td>Trimestre</td>
                <td style="text-align:right;">Total dû</td>
                <td style="text-align:right;">Montant versé</td>
                <td style="text-align:center;">Statut</td>
            </tr>
        </thead>
        <tbody>
            @foreach($paiementsCreated as $p)
            <tr>
                <td style="font-weight: 600;">{{ \App\Models\Tarif::TYPES[$p->type_paiement] ?? ucfirst($p->type_paiement) }}</td>
                <td>{{ $p->trimestre ?? '—' }}</td>
                <td style="text-align:right; color: #374151;">
                    {{ $p->montant_total ? number_format($p->montant_total, 0, ',', ' ') . ' XOF' : '—' }}
                </td>
                <td style="text-align:right; font-weight: 700; color: #15803d;">
                    {{ number_format($p->montant, 0, ',', ' ') }} XOF
                </td>
                <td style="text-align:center;">
                    <span class="badge-statut {{ $p->statut === 'complet' ? 'badge-complet' : 'badge-partiel' }}">
                        {{ ucfirst($p->statut) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totaux">
        <table>
            @php
                $totalVerse = $paiementsCreated->sum(fn($p) => (float) $p->montant);
                $totalDu    = $paiementsCreated->sum(fn($p) => (float) ($p->montant_total ?? $p->montant));
                $resteTotal = max(0, $totalDu - $totalVerse);
            @endphp
            @if($totalDu != $totalVerse)
            <tr>
                <td>Total dû</td>
                <td>{{ number_format($totalDu, 0, ',', ' ') }} XOF</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total versé</td>
                <td>{{ number_format($totalVerse, 0, ',', ' ') }} XOF</td>
            </tr>
            @if($resteTotal > 0)
            <tr>
                <td style="color: #dc2626;">Restant dû</td>
                <td style="color: #dc2626; text-align: right;">{{ number_format($resteTotal, 0, ',', ' ') }} XOF</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Remarque --}}
    @if($paiementsCreated->first()->remarque)
    <div class="remarque">
        <div class="lbl">Remarque</div>
        <div class="txt">{{ $paiementsCreated->first()->remarque }}</div>
    </div>
    @endif

    {{-- Signature --}}
    <div class="signature">
        <div class="sig-box">
            <div class="sig-label">Le Responsable</div>
            <div class="sig-sub">Signature &amp; cachet</div>
            <div class="sig-line"></div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

</body>
</html>
