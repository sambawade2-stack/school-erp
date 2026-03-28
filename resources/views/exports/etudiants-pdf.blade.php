<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Élèves{{ $classeNom ? ' — ' . $classeNom : '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10.5px; color: #000; margin: 20px 25px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #333; margin-bottom: 2px; }
        .header h3 { font-size: 12px; font-weight: normal; color: #555; }
        .separator { border-top: 2px solid #000; margin: 10px 0 15px; }
        .stats { margin-bottom: 10px; font-size: 10px; color: #333; }
        .stats span { margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 6px; }
        th { background: #f0f0f0; font-size: 9.5px; text-transform: uppercase; font-weight: bold; text-align: left; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .footer { text-align: center; font-size: 9px; margin-top: 20px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT') }}</h1>
        @if($classeNom ?? null)
            <h2>Liste des Élèves — Classe : {{ $classeNom }}</h2>
            <h3>{{ $etudiants->count() }} élève(s)</h3>
        @else
            <h2>Liste Complète des Élèves — {{ $etudiants->count() }} élève(s)</h2>
        @endif
    </div>
    <div class="separator"></div>

    @php
        $nbGarcons = $etudiants->where('sexe', 'masculin')->count();
        $nbFilles  = $etudiants->where('sexe', 'feminin')->count();
    @endphp
    <div class="stats">
        <span><strong>Total :</strong> {{ $etudiants->count() }}</span>
        <span><strong>Garçons :</strong> {{ $nbGarcons }}</span>
        <span><strong>Filles :</strong> {{ $nbFilles }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 12%;">Matricule</th>
                <th style="width: 18%;">Nom complet</th>
                <th style="width: 5%;">Sexe</th>
                <th style="width: 8%;">Naissance</th>
                @if(!($classeNom ?? null))
                <th style="width: 12%;">Classe</th>
                @endif
                <th style="width: 11%;">Téléphone</th>
                <th style="width: 14%;">Parent</th>
                <th style="width: 11%;">Tél Parent</th>
                <th style="width: 7%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($etudiants as $i => $e)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-size: 9px;">{{ $e->matricule }}</td>
                <td>{{ $e->nom_complet }}</td>
                <td class="text-center">{{ $e->sexe === 'masculin' ? 'M' : 'F' }}</td>
                <td class="text-center">{{ $e->date_naissance?->format('d/m/Y') ?? '—' }}</td>
                @if(!($classeNom ?? null))
                <td>{{ $e->classe?->nom ?? '—' }}</td>
                @endif
                <td>{{ $e->telephone ?? '—' }}</td>
                <td>{{ $e->nom_parent ?? '—' }}</td>
                <td>{{ $e->tel_parent ?? '—' }}</td>
                <td class="text-center">{{ ucfirst($e->statut) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $etablissement->nom ?? '' }}
    </div>
</body>
</html>
