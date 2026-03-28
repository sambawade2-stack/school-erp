<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Etudiants</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 5px; }
        p.subtitle { color: #666; margin: 0 0 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background: #1d4ed8; color: white; }
        th { padding: 7px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge-actif { background: #d1fae5; color: #065f46; padding: 2px 6px; border-radius: 10px; font-size: 9px; }
        .badge-inactif { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 10px; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Liste des Etudiants</h1>
    <p class="subtitle">Genere le {{ now()->format('d/m/Y H:i') }} — {{ $etudiants->count() }} etudiant(s)</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom complet</th>
                <th>Matricule</th>
                <th>Classe</th>
                <th>Sexe</th>
                <th>Telephone</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($etudiants as $i => $etudiant)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $etudiant->nom_complet }}</strong></td>
                <td>{{ $etudiant->matricule }}</td>
                <td>{{ $etudiant->classe?->nom ?? '—' }}</td>
                <td>{{ ucfirst($etudiant->sexe) }}</td>
                <td>{{ $etudiant->telephone ?? '—' }}</td>
                <td><span class="{{ $etudiant->statut === 'actif' ? 'badge-actif' : 'badge-inactif' }}">{{ ucfirst($etudiant->statut) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
