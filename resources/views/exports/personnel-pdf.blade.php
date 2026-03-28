<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste du Personnel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 20px 30px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #333; }
        .separator { border-top: 2px solid #000; margin: 10px 0 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        th { background: #f0f0f0; font-size: 10px; text-transform: uppercase; font-weight: bold; text-align: left; }
        td { font-size: 11px; }
        .text-center { text-align: center; }
        .footer { text-align: center; font-size: 9px; margin-top: 20px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT') }}</h1>
        <h2>Liste du Personnel — {{ $personnels->count() }} membre(s)</h2>
    </div>
    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 22%;">Nom complet</th>
                <th style="width: 13%;">Type</th>
                <th style="width: 15%;">Spécialité</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 12%;">Téléphone</th>
                <th style="width: 10%;">Embauche</th>
                <th style="width: 8%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personnels as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $p->nom_complet }}</td>
                <td>{{ \App\Models\Enseignant::TYPES[$p->type] ?? ucfirst($p->type) }}</td>
                <td>{{ $p->specialite ?? '—' }}</td>
                <td>{{ $p->email ?? '—' }}</td>
                <td>{{ $p->telephone ?? '—' }}</td>
                <td class="text-center">{{ $p->date_embauche?->format('d/m/Y') ?? '—' }}</td>
                <td class="text-center">{{ ucfirst($p->statut) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $etablissement->nom ?? '' }}
    </div>
</body>
</html>
