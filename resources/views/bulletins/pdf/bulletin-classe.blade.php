<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletins - {{ $classe->nom }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            color: #000;
            margin: 20px 30px;
        }

        .page-break { page-break-after: always; }

        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .notes-table th, .notes-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .notes-table thead th {
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            background-color: #f0f0f0;
        }

        .sig-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .sig-table td {
            text-align: center;
            width: 33%;
            padding: 0 15px;
            vertical-align: bottom;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 18px;
            border-top: 1px solid #000;
            padding-top: 4px;
            color: #333;
        }
    </style>
</head>
<body>

@foreach($bulletins as $index => $bulletin)
@php
    $etudiant              = $bulletin['etudiant'];
    $lignesMatiere         = $bulletin['lignesMatiere'];
    $moyennePonderee       = $bulletin['moyennePonderee'];
    $totalCoefficient      = $bulletin['totalCoefficient'];
    $rang                  = $bulletin['rang'];
    $notesExamen           = $bulletin['notesExamen'];
    $notesDevoir           = $bulletin['notesDevoir'];
    $notesComposition      = $bulletin['notesComposition'];
    $moyenneGeneraleClasse = $bulletin['moyenneGeneraleClasse'] ?? 0.0;

    /* Détection semestre / trimestre */
    $estSemestre  = str_starts_with($trimestre, 'S');
    $labelPeriode = $estSemestre ? 'SEMESTRE' : 'TRIMESTRE';
    $numPeriode   = (int) substr($trimestre, 1);
    $numTrimestre = match($numPeriode) {
        1       => '1<sup>er</sup>',
        default => $numPeriode . '<sup>e</sup>',
    };

    /* Mention générale */
    $mentionGen = function(float $note): string {
        if ($note >= 18) return 'Excellent';
        if ($note >= 16) return 'Très Bien';
        if ($note >= 14) return 'Bien';
        if ($note >= 12) return 'Assez Bien';
        if ($note >= 10) return 'Passable';
        return 'Insuffisant';
    };

    /* Titulaire de classe */
    $titulaire = $etudiant->classe?->responsable;
    $tenuePar  = $titulaire ? $titulaire->nom_complet : '..............';

    /* Grouper lignesMatiere par section */
    $ordresSections = \App\Models\Section::pluck('ordre', 'nom')->toArray();
    $groupesSection = collect($lignesMatiere)->groupBy(fn($l) => $l['matiere']->section ?? 'Générale')
        ->sortBy(fn($g, $key) => $ordresSections[$key] ?? 999);

    /* Élémentaire */
    $categorie = $etudiant->classe?->categorie ?? 'college';
    $estElem   = $categorie === 'elementaire';

    /* Résumé */
    $totalPoints   = collect($lignesMatiere)->sum('points');
    $totalCoeff    = $totalCoefficient ?: collect($lignesMatiere)->sum('coefficient');
    $moyenneFinale = $totalCoeff > 0 ? round($totalPoints / $totalCoeff, 2) : 0;
    $rangFinal     = $rang ?? '—';
    $appreciationGen = $mentionGen($moyenneFinale);
@endphp

{{-- ════════════════ EN-TÊTE ════════════════ --}}
<table style="width:100%; margin-bottom:10px;">
    <tr>
        <td style="width:130px; vertical-align:middle; text-align:center;">
            @if($etablissement && $etablissement->logo)
                <img src="{{ storage_path('app/public/logo/' . basename($etablissement->logo)) }}"
                     alt="Logo" style="width:120px; height:auto; max-height:120px;">
            @else
                <div style="width:100px; height:100px; border:2px solid #000; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:24px; margin:0 auto;">
                    {{ strtoupper(substr($etablissement->sigle ?? $etablissement->nom ?? 'E', 0, 1)) }}
                </div>
            @endif
        </td>
        <td style="text-align:center; vertical-align:middle; padding:0 15px;">
            <div style="font-size:16px; font-weight:bold; margin-bottom:4px; letter-spacing:1px;">
                {{ strtoupper($etablissement->nom ?? 'ÉTABLISSEMENT SCOLAIRE') }}
            </div>
            @if($etablissement->sigle ?? false)
            <div style="font-size:12px; font-weight:bold; margin-bottom:4px; font-style:italic;">{{ $etablissement->sigle }}</div>
            @endif
            @if($etablissement->adresse ?? false)
            <div style="font-size:10px; margin-bottom:2px;">Adresse : {{ $etablissement->adresse }}</div>
            @endif
            @if($etablissement->telephone ?? false)
            <div style="font-size:10px;">Tél : {{ $etablissement->telephone }}</div>
            @endif
            @if($etablissement->email ?? false)
            <div style="font-size:10px;">Email : {{ $etablissement->email }}</div>
            @endif
        </td>
        <td style="width:130px;"></td>
    </tr>
</table>

<div style="border-top:2px solid #000; margin-bottom:8px;"></div>

{{-- ════════════════ TITRE ════════════════ --}}
<div style="font-size:12px; font-weight:bold; margin-bottom:6px; letter-spacing:0.3px;">
    BULLETIN DE NOTES DU &nbsp;
    <span style="border-bottom:1px solid #000; padding:0 6px 1px;">{!! $numTrimestre !!}</span>
    &nbsp; {{ $labelPeriode }} &nbsp;&nbsp;&nbsp;&nbsp;
    ANNEE SCOLAIRE &nbsp;
    <span style="border-bottom:1px solid #000; padding:0 6px 1px;">{{ $anneeScolaire }}</span>
</div>

{{-- ════════════════ CLASSE / ENSEIGNANT / ÉLÈVE ════════════════ --}}
<table style="width:100%; margin-bottom:10px; border-collapse:collapse;">
    <tr>
        <td style="font-size:11px; padding:3px 0; width:80px; font-weight:bold; vertical-align:bottom;">CLASSE :</td>
        <td style="font-size:11px; padding:3px 0; width:auto; vertical-align:bottom;">
            {{ $classe->nom }}
        </td>
        <td style="font-size:11px; padding:3px 0; width:100px; font-weight:bold; vertical-align:bottom; text-align:right; padding-right:8px;">TENUE PAR :</td>
        <td style="font-size:11px; padding:3px 0; width:auto; vertical-align:bottom;">
            {{ $tenuePar }}
        </td>
    </tr>
    <tr>
        <td style="font-size:12px; padding:3px 0; font-weight:bold; vertical-align:bottom;">ÉLÈVE :</td>
        <td colspan="3" style="font-size:12px; padding:3px 0; font-weight:bold; vertical-align:bottom;">
            {{ strtoupper($etudiant->nom_complet) }}
        </td>
    </tr>
</table>

{{-- ════════════════ TABLEAU DES NOTES ════════════════ --}}
<table class="notes-table">
    <thead>
        @if($estElem)
        <tr>
            <th style="width:46%; text-align:center; background:#f0f0f0;">MATIÈRES</th>
            <th style="width:8%;  text-align:center; background:#f0f0f0;">COEFF.</th>
            <th style="width:13%; text-align:center; background:#f0f0f0;">COMPOSITION</th>
            <th style="width:13%; text-align:center; font-size:9.5px; background:#f0f0f0;">MOY.<br>CLASSE</th>
            <th style="width:20%; text-align:center; background:#f0f0f0;">APPRÉCIATION</th>
        </tr>
        @else
        <tr>
            <th style="width:33%;" rowspan="2">MATIÈRES</th>
            <th style="width:7%;"  rowspan="2">COEFF.</th>
            <th colspan="3" style="text-align:center;">NOTES PAR TYPE</th>
            <th style="width:8%;"  rowspan="2">MOYENNE<br>/20</th>
            <th style="width:10%; font-size:9.5px;" rowspan="2">MOY.<br>CLASSE</th>
            <th style="width:15%;" rowspan="2">APPRÉCIATION</th>
        </tr>
        <tr>
            <th style="width:9%; font-size:9.5px;">Devoir</th>
            <th style="width:9%; font-size:9.5px;">Examen</th>
            <th style="width:9%; font-size:9.5px;">Compos.</th>
        </tr>
        @endif
    </thead>
    <tbody>
        @forelse($groupesSection as $section => $lignes)

        @if($groupesSection->count() > 1 || $section !== 'Générale')
        <tr>
            <td colspan="{{ $estElem ? 5 : 8 }}" style="background:#e0e0e0; font-weight:bold; text-align:center; font-size:10.5px; letter-spacing:0.5px; padding:5px 6px;">
                {{ strtoupper($section) }}
            </td>
        </tr>
        @endif

        @foreach($lignes as $ligne)
        @php
            $moy    = $ligne['moyenne_matiere'];
            $clrBas = $moy < 10 ? 'color:#cc0000;' : '';
        @endphp
        <tr>
            @if($estElem)
            <td style="font-weight:bold; font-size:10px; padding:4px 6px;">{{ strtoupper($ligne['matiere']->nom) }}</td>
            <td style="text-align:center; font-size:10px;">{{ number_format($ligne['coefficient'], 0) }}</td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $clrBas }}">
                {{ $ligne['moyenne_compo'] !== null ? number_format($ligne['moyenne_compo'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:10px; color:#15803d;">
                {{ isset($ligne['moyenne_classe']) && $ligne['moyenne_classe'] !== null ? number_format($ligne['moyenne_classe'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:9.5px;">{{ $ligne['mention'] }}</td>
            @else
            <td style="font-weight:bold; font-size:10px; padding:4px 6px;">{{ strtoupper($ligne['matiere']->nom) }}</td>
            <td style="text-align:center; font-size:10px;">{{ number_format($ligne['coefficient'], 0) }}</td>
            <td style="text-align:center; font-size:10px;">{{ $ligne['moyenne_devoir'] !== null ? number_format($ligne['moyenne_devoir'], 2) : '—' }}</td>
            <td style="text-align:center; font-size:10px;">{{ $ligne['moyenne_examen'] !== null ? number_format($ligne['moyenne_examen'], 2) : '—' }}</td>
            <td style="text-align:center; font-size:10px;">{{ $ligne['moyenne_compo'] !== null ? number_format($ligne['moyenne_compo'], 2) : '—' }}</td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $clrBas }}">{{ number_format($moy, 2) }}</td>
            <td style="text-align:center; font-size:10px; color:#15803d;">
                {{ isset($ligne['moyenne_classe']) && $ligne['moyenne_classe'] !== null ? number_format($ligne['moyenne_classe'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:9.5px;">{{ $ligne['mention'] }}</td>
            @endif
        </tr>
        @endforeach

        @empty
        <tr>
            <td colspan="{{ $estElem ? 5 : 8 }}" style="text-align:center; padding:20px; font-style:italic; color:#666;">
                Aucune note disponible pour cette période.
            </td>
        </tr>
        @endforelse

        @php $nbCols = $estElem ? 5 : 8; $nbColsLabel = $estElem ? 2 : 5; $moyGenClasse = $moyenneGeneraleClasse ?? 0.0; @endphp
        <tr><td colspan="{{ $nbCols }}" style="padding:0; border-left:none; border-right:none; height:1px; background:#000;"></td></tr>

        {{-- TOTAL --}}
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">TOTAL DES POINTS</td>
            <td style="text-align:center; font-weight:bold; font-size:11px;">{{ number_format($totalPoints, 2) }}</td>
            <td></td><td></td>
        </tr>
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">TOTAL COEFFICIENTS</td>
            <td style="text-align:center; font-weight:bold; font-size:11px;">{{ number_format($totalCoeff, 0) }}</td>
            <td></td><td></td>
        </tr>
        <tr style="background:#e8f0fe;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:12px; letter-spacing:0.3px;">MOYENNE GÉNÉRALE</td>
            <td style="text-align:center; font-weight:bold; font-size:13px; {{ $moyenneFinale < 10 ? 'color:#cc0000;' : 'color:#1a56db;' }}">{{ number_format($moyenneFinale, 2) }}</td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $moyGenClasse > 0 && $moyGenClasse < 10 ? 'color:#cc0000;' : 'color:#15803d;' }}">
                {{ $moyGenClasse > 0 ? number_format($moyGenClasse, 2) : '—' }}
            </td>
            <td style="text-align:center; font-weight:bold; font-size:10px;">{{ $appreciationGen }}</td>
        </tr>
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">RANG</td>
            <td style="text-align:center; font-weight:bold; font-size:12px;">{{ $rangFinal }}</td>
            <td></td><td></td>
        </tr>
    </tbody>
</table>

{{-- ════════════════ SIGNATURES ════════════════ --}}
<table class="sig-table">
    <tr>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">LE (A) MAÎTRE (ESSE)</div>
            <div style="border-top:1px solid #000;"></div>
        </td>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">LE DIRECTEUR</div>
            <div style="border-top:1px solid #000;"></div>
        </td>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">LES PARENTS</div>
            <div style="border-top:1px solid #000;"></div>
        </td>
    </tr>
</table>

<div class="footer">
    {{ $etablissement->nom ?? '' }}
    @if($etablissement->adresse ?? false) &nbsp;:&nbsp; Adresse&nbsp;: {{ $etablissement->adresse }} @endif
    @if($etablissement->telephone ?? false) &nbsp;&nbsp; Tél&nbsp;: {{ $etablissement->telephone }} @endif
</div>

{{-- Saut de page entre les élèves (sauf le dernier) --}}
@if(!$loop->last)
<div class="page-break"></div>
@endif

@endforeach

</body>
</html>
