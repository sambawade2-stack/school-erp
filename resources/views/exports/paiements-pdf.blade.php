<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Paiements - {{ $periode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px 25px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #333; }
        .header p { font-size: 10px; color: #666; margin-top: 2px; }
        .separator { border-top: 2px solid #000; margin: 10px 0 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 6px; }
        th { background: #f0f0f0; font-size: 9.5px; text-transform: uppercase; font-weight: bold; text-align: left; }
        td { font-size: 10.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e8f0fe; font-weight: bold; font-size: 12px; }
        .footer { text-align: center; font-size: 9px; margin-top: 20px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT') }}</h1>
        <h2>Rapport des Paiements — {{ $periode }}</h2>
        @if($etablissement->telephone ?? false)
        <p>Tél : {{ $etablissement->telephone }} | {{ $etablissement->email ?? '' }}</p>
        @endif
    </div>
    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 12%;">N° Reçu</th>
                <th style="width: 20%;">Élève</th>
                <th style="width: 12%;">Classe</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 12%;" class="text-right">Montant (XOF)</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 8%;">Trim.</th>
                <th style="width: 10%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-size: 9.5px;">{{ $p->numero_recu }}</td>
                <td>{{ $p->etudiant->nom_complet }}</td>
                <td>{{ $p->etudiant->classe?->nom ?? '—' }}</td>
                <td>{{ ucfirst($p->type_paiement) }}</td>
                <td class="text-right">{{ number_format($p->montant, 0, ',', ' ') }}</td>
                <td class="text-center">{{ $p->date_paiement->format('d/m/Y') }}</td>
                <td class="text-center">{{ $p->trimestre ?? '—' }}</td>
                <td class="text-center">{{ ucfirst($p->statut) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($total, 0, ',', ' ') }} XOF</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $etablissement->nom ?? '' }}
    </div>
</body>
</html>
