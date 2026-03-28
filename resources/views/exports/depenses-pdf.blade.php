<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Dépenses - {{ $periode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px 30px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #333; }
        .header p { font-size: 10px; color: #666; margin-top: 2px; }
        .separator { border-top: 2px solid #000; margin: 10px 0 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        th { background: #f0f0f0; font-size: 10px; text-transform: uppercase; font-weight: bold; text-align: left; }
        td { font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e8f0fe; font-weight: bold; font-size: 12px; }
        .footer { text-align: center; font-size: 9px; margin-top: 20px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT') }}</h1>
        <h2>Rapport des Dépenses — {{ $periode }}</h2>
        @if($etablissement->telephone ?? false)
        <p>Tél : {{ $etablissement->telephone }} | {{ $etablissement->email ?? '' }}</p>
        @endif
    </div>
    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Libellé</th>
                <th style="width: 15%;">Catégorie</th>
                <th style="width: 15%;">Bénéficiaire</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 15%;" class="text-right">Montant (XOF)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($depenses as $i => $d)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $d->libelle }}</td>
                <td>{{ ucfirst($d->categorie) }}</td>
                <td>{{ $d->beneficiaire ?? '—' }}</td>
                <td class="text-center">{{ $d->date_depense->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($d->montant, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($total, 0, ',', ' ') }} XOF</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $etablissement->nom ?? '' }}
    </div>
</body>
</html>
