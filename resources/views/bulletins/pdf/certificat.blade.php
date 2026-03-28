<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Scolarité</title>
    <style>
        body { font-family: 'Georgia', serif; margin: 30px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 24px; color: #0066cc; margin: 10px 0; }
        .header p { font-size: 13px; margin: 3px 0; }
        .logo { text-align: center; margin-bottom: 20px; font-size: 14px; font-weight: bold; }
        .title { text-align: center; font-size: 20px; font-weight: bold; color: #0066cc; margin: 40px 0; text-decoration: underline; }
        .content { font-size: 14px; line-height: 1.8; margin: 30px 0; }
        .content p { margin: 15px 0; text-align: justify; }
        .student-name { font-weight: bold; font-size: 16px; color: #000; }
        .signature-section { margin-top: 60px; }
        .signature { display: inline-block; width: 200px; text-align: center; margin-right: 40px; }
        .signature-line { border-top: 1px solid #000; margin-top: 40px; }
        .signature-label { font-size: 12px; margin-top: 5px; }
        .footer { text-align: center; margin-top: 50px; font-size: 11px; color: #666; }
        .date { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($etablissement && $etablissement->logo)
            <div style="margin-bottom: 20px;">
                <img src="{{ storage_path('app/public/logo/' . basename($etablissement->logo)) }}" alt="Logo" style="width: 130px; height: auto; max-height: 130px;">
            </div>
            @endif
            <div class="logo">{{ $etablissement->sigle ?? '' }}</div>
            <h1>{{ $etablissement->nom ?? 'ÉTABLISSEMENT SCOLAIRE' }}</h1>
            <p>{{ $etablissement->adresse ?? '' }}</p>
            <p>
                @if($etablissement->telephone){{ $etablissement->telephone }}@endif
                @if($etablissement->telephone && $etablissement->email) - @endif
                @if($etablissement->email){{ $etablissement->email }}@endif
            </p>
        </div>

        <div class="title">CERTIFICAT DE SCOLARITÉ</div>

        <div class="content">
            <p>
                Nous soussigné, Directeur de <span class="student-name">{{ $etablissement->nom ?? 'l\'établissement' }}</span>,
                certifions par la présente que l'élève :
            </p>

            <p style="text-align: center; font-size: 16px;">
                <span class="student-name">{{ $etudiant->nom_complet }}</span><br>
                <span style="font-size: 13px;">Matricule : {{ $etudiant->matricule }}</span>
            </p>

            <p>
                est régulièrement inscrit(e) et a fréquenté notre établissement au cours de l'année scolaire 2024-2025.
            </p>

            <p>
                Cet élève est actuellement en classe de <span class="student-name">{{ $etudiant->classe?->nom ?? 'N/A' }}</span>
                et se distingue par son assiduité et son comportement disciplinaire satisfaisant.
            </p>

            <p>
                Le présent certificat est délivré pour servir et valoir ce que de droit.
            </p>
        </div>

        <div class="date">
            <p>Délivré à __________, le {{ now()->format('d/m/Y') }}</p>
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label"><span class="student-name">{{ $etablissement->directeur ?? 'Le Directeur' }}</span></div>
                <div class="signature-label">Directeur de l'établissement</div>
            </div>
        </div>

        <div class="footer">
            <p style="margin-top: 60px;">Visa de l'établissement : ________________</p>
            <p style="margin-top: 30px; font-size: 10px;">Certificat généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
