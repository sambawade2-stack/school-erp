<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Paiements</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        .subtitle { color: #666; margin: 0 0 5px; }
        .total { font-size: 14px; font-weight: bold; color: #16a34a; margin: 10px 0 15px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1d4ed8; color: white; }
        th { padding: 7px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Rapport de Paiements</h1>
    <p class="subtitle">Du {{ \Carbon\Carbon::parse($debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}</p>
    <p class="total">Total : {{ number_format($total, 2, ',', ' ') }} XOF</p>
    <table>
        <thead>
            <tr><th>Eleve</th><th>Montant</th><th>Type</th><th>Date</th><th>N° Recu</th></tr>
        </thead>
        <tbody>
            @foreach($paiements as $p)
            <tr>
                <td>{{ $p->etudiant->nom_complet }}</td>
                <td><strong>{{ number_format($p->montant, 2, ',', ' ') }} XOF</strong></td>
                <td>{{ ucfirst($p->type_paiement) }}</td>
                <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                <td>{{ $p->numero_recu }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
