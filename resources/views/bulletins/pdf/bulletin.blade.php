<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            color: #000;
            margin: 20px 30px;
        }

        /* ── En-tête ─────────────────────────────────────────────── */
        .header { display: flex; align-items: center; margin-bottom: 8px; }
        .header-logo { width: 80px; height: 80px; object-fit: contain; margin-right: 12px; }
        .header-logo-placeholder {
            width: 80px; height: 80px; border: 1px solid #000;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 18px; margin-right: 12px; flex-shrink: 0;
        }
        .school-info { text-align: center; flex: 1; }
        .school-info .nom-ecole { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
        .school-info .sigle { font-size: 11px; font-weight: bold; margin-bottom: 2px; }
        .school-info p { font-size: 9.5px; margin: 1px 0; }

        /* ── Séparateur + titre ──────────────────────────────────── */
        .divider { border-top: 2px solid #000; margin: 8px 0 6px; }

        .bulletin-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .underline-field {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 5px 1px;
            min-width: 35px;
            text-align: center;
        }

        /* ── Infos classe / élève ───────────────────────────────── */
        .info-row { margin-bottom: 4px; font-size: 11px; }
        .info-row .label { font-weight: bold; }
        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            padding: 0 5px 1px;
            min-width: 100px;
        }

        /* ── Tableau principal ──────────────────────────────────── */
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

        /* En-tête du tableau */
        .notes-table thead th {
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            background-color: #f0f0f0;
        }

        /* Colonnes */
        .col-matiere    { width: 33%; }
        .col-coeff      { width: 7%;  text-align: center; }
        .col-devoir     { width: 9%;  text-align: center; }
        .col-examen     { width: 9%;  text-align: center; }
        .col-compo      { width: 9%;  text-align: center; }
        .col-moy        { width: 8%;  text-align: center; font-weight: bold; }
        .col-moy-classe { width: 10%; text-align: center; }
        .col-apprec     { width: 15%; text-align: center; }

        /* Ligne de section (groupe) */
        .row-section td {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 5px 6px;
        }

        /* Ligne matière normale */
        .row-matiere td { font-size: 10px; }
        .row-matiere .matiere-nom { font-weight: bold; }

        /* Valeur numérique */
        .note-val { text-align: center; font-weight: bold; }
        .note-low { color: #cc0000; }

        /* Ligne Totaux / Moyenne / Rang */
        .row-total td {
            font-weight: bold;
            font-size: 11px;
            background-color: #f8f8f8;
        }
        .row-total .lbl { text-align: right; padding-right: 8px; }
        .row-total .val { text-align: center; }

        /* Appréciation générale */
        .apprec-bien      { font-weight: bold; }
        .apprec-tbien     { font-weight: bold; }
        .apprec-insuffis  { color: #cc0000; }

        /* ── Signatures ─────────────────────────────────────────── */
        .sig-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .sig-table td {
            text-align: center;
            width: 33%;
            padding: 0 15px;
            vertical-align: bottom;
        }
        .sig-label {
            font-weight: bold;
            font-size: 10px;
            text-decoration: underline;
            margin-bottom: 40px;
            display: block;
        }
        .sig-line { border-top: 1px solid #000; margin-top: 2px; }

        /* ── Pied de page ───────────────────────────────────────── */
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

@php
    /* Détection automatique semestre / trimestre selon la période */
    $estSemestre    = str_starts_with($trimestre, 'S');
    $labelPeriode   = $estSemestre ? 'SEMESTRE' : 'TRIMESTRE';
    $numPeriode     = (int) substr($trimestre, 1);
    $numTrimestre   = match($numPeriode) {
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

    /* Grouper lignesMatiere par section, triées selon l'ordre admin */
    $ordresSections = \App\Models\Section::pluck('ordre', 'nom')->toArray();
    $groupesSection = collect($lignesMatiere ?? [])
        ->groupBy(fn($l) => $l['matiere']->section ?? 'Générale')
        ->sortBy(fn($g, $key) => $ordresSections[$key] ?? 999);

    /* Élémentaire : seule la colonne Composition est affichée */
    $categorie = $etudiant->classe?->categorie ?? 'college';
    $estElem   = $categorie === 'elementaire';

    /* Résumé */
    $totalPoints     = collect($lignesMatiere ?? [])->sum('points');
    $totalCoeff      = $totalCoefficient ?? collect($lignesMatiere ?? [])->sum('coefficient');
    $moyenneFinale   = $moyennePonderee ?? ($totalCoeff > 0 ? round($totalPoints / $totalCoeff, 2) : 0);
    $rangFinal       = $rang ?? '—';

    /* Appréciation globale */
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
            <div style="font-size:12px; font-weight:bold; margin-bottom:4px; font-style:italic;">
                {{ $etablissement->sigle }}
            </div>
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
            {{ $etudiant->classe?->nom ?? '' }}
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
        {{-- Élémentaire : composition seule --}}
        <tr>
            <th style="width:46%; text-align:center; font-weight:bold; font-size:10.5px; background:#f0f0f0;">MATIÈRES</th>
            <th style="width:8%;  text-align:center; font-weight:bold; font-size:10.5px; background:#f0f0f0;">COEFF.</th>
            <th style="width:13%; text-align:center; font-weight:bold; font-size:10.5px; background:#f0f0f0;">COMPOSITION</th>
            <th style="width:13%; text-align:center; font-weight:bold; font-size:9.5px;  background:#f0f0f0;">MOY.<br>CLASSE</th>
            <th style="width:20%; text-align:center; font-weight:bold; font-size:10.5px; background:#f0f0f0;">APPRÉCIATION</th>
        </tr>
        @else
        {{-- Secondaire : devoir + examen + composition --}}
        <tr>
            <th class="col-matiere"    rowspan="2">MATIÈRES</th>
            <th class="col-coeff"      rowspan="2">COEFF.</th>
            <th colspan="3" style="text-align:center;">NOTES PAR TYPE</th>
            <th class="col-moy"        rowspan="2">MOYENNE<br>/20</th>
            <th class="col-moy-classe" rowspan="2">MOY.<br>CLASSE</th>
            <th class="col-apprec"     rowspan="2">APPRÉCIATION</th>
        </tr>
        <tr>
            <th class="col-devoir" style="font-size:9.5px;">Devoir</th>
            <th class="col-examen" style="font-size:9.5px;">Examen</th>
            <th class="col-compo"  style="font-size:9.5px;">Compos.</th>
        </tr>
        @endif
    </thead>
    <tbody>

        @forelse($groupesSection as $section => $lignes)

        {{-- ── Ligne de section (domain header) ── --}}
        @if($groupesSection->count() > 1 || $section !== 'Générale')
        <tr>
            <td colspan="{{ $estElem ? 5 : 8 }}" style="background:#e0e0e0; font-weight:bold; text-align:center; font-size:10.5px; letter-spacing:0.5px; padding:5px 6px;">
                {{ strtoupper($section) }}
            </td>
        </tr>
        @endif

        {{-- ── Lignes matières ── --}}
        @foreach($lignes as $ligne)
        @php
            $moy    = $ligne['moyenne_matiere'];
            $clrBas = $moy < 10 ? 'color:#cc0000;' : '';
        @endphp
        <tr>
            @if($estElem)
            {{-- Élémentaire : 5 colonnes --}}
            <td style="font-weight:bold; font-size:10px; padding:4px 6px;">
                {{ strtoupper($ligne['matiere']->nom) }}
            </td>
            <td style="text-align:center; font-size:10px;">
                {{ number_format($ligne['coefficient'], 0) }}
            </td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $clrBas }}">
                {{ $ligne['moyenne_compo'] !== null ? number_format($ligne['moyenne_compo'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:10px; color:#15803d;">
                {{ $ligne['moyenne_classe'] !== null ? number_format($ligne['moyenne_classe'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:9.5px;">
                {{ $ligne['mention'] }}
            </td>
            @else
            {{-- Secondaire : 8 colonnes --}}
            <td style="font-weight:bold; font-size:10px; padding:4px 6px;">
                {{ strtoupper($ligne['matiere']->nom) }}
            </td>
            <td style="text-align:center; font-size:10px;">
                {{ number_format($ligne['coefficient'], 0) }}
            </td>
            <td style="text-align:center; font-size:10px;">
                {{ $ligne['moyenne_devoir'] !== null ? number_format($ligne['moyenne_devoir'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:10px;">
                {{ $ligne['moyenne_examen'] !== null ? number_format($ligne['moyenne_examen'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:10px;">
                {{ $ligne['moyenne_compo'] !== null ? number_format($ligne['moyenne_compo'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $clrBas }}">
                {{ number_format($moy, 2) }}
            </td>
            <td style="text-align:center; font-size:10px; color:#15803d;">
                {{ $ligne['moyenne_classe'] !== null ? number_format($ligne['moyenne_classe'], 2) : '—' }}
            </td>
            <td style="text-align:center; font-size:9.5px;">
                {{ $ligne['mention'] }}
            </td>
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

        {{-- ── Ligne séparation ── --}}
        @php $nbCols = $estElem ? 5 : 8; $nbColsLabel = $estElem ? 2 : 5; @endphp
        <tr><td colspan="{{ $nbCols }}" style="padding:0; border-left:none; border-right:none; height:1px; background:#000;"></td></tr>

        {{-- ── TOTAL des points ── --}}
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">
                TOTAL DES POINTS
            </td>
            <td style="text-align:center; font-weight:bold; font-size:11px;">
                {{ number_format($totalPoints, 2) }}
            </td>
            <td></td><td></td>
        </tr>

        {{-- ── TOTAL des coefficients ── --}}
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">
                TOTAL COEFFICIENTS
            </td>
            <td style="text-align:center; font-weight:bold; font-size:11px;">
                {{ number_format($totalCoeff, 0) }}
            </td>
            <td></td><td></td>
        </tr>

        {{-- ── MOYENNE GÉNÉRALE + MOY. CLASSE sur la même ligne ── --}}
        @php $moyGenClasse = $moyenneGeneraleClasse ?? 0.0; @endphp
        <tr style="background:#e8f0fe;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:12px; letter-spacing:0.3px;">
                MOYENNE GÉNÉRALE
            </td>
            <td style="text-align:center; font-weight:bold; font-size:13px; {{ $moyenneFinale < 10 ? 'color:#cc0000;' : 'color:#1a56db;' }}">
                {{ number_format($moyenneFinale, 2) }}
            </td>
            <td style="text-align:center; font-weight:bold; font-size:11px; {{ $moyGenClasse > 0 && $moyGenClasse < 10 ? 'color:#cc0000;' : 'color:#15803d;' }}">
                {{ $moyGenClasse > 0 ? number_format($moyGenClasse, 2) : '—' }}
            </td>
            <td style="text-align:center; font-weight:bold; font-size:10px;">
                {{ $appreciationGen }}
            </td>
        </tr>

        {{-- ── RANG ── --}}
        <tr style="background:#f8f8f8;">
            <td colspan="{{ $nbColsLabel }}" style="text-align:right; font-weight:bold; padding-right:8px; font-size:11px;">
                RANG
            </td>
            <td style="text-align:center; font-weight:bold; font-size:12px;">
                {{ $rangFinal }}
            </td>
            <td></td><td></td>
        </tr>

    </tbody>
</table>

{{-- ════════════════ SIGNATURES ════════════════ --}}
<table class="sig-table">
    <tr>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">
                LE (A) MAÎTRE (ESSE)
            </div>
            <div style="border-top:1px solid #000;"></div>
        </td>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">
                LE DIRECTEUR
            </div>
            <div style="border-top:1px solid #000;"></div>
        </td>
        <td style="text-align:center; width:33%; padding:0 15px; vertical-align:bottom;">
            <div style="font-weight:bold; font-size:10px; text-decoration:underline; margin-bottom:45px;">
                LES PARENTS
            </div>
            <div style="border-top:1px solid #000;"></div>
        </td>
    </tr>
</table>

{{-- ════════════════ PIED DE PAGE ════════════════ --}}
<div class="footer">
    {{ $etablissement->nom ?? '' }}
    @if($etablissement->adresse ?? false) &nbsp;:&nbsp; Adresse&nbsp;: {{ $etablissement->adresse }} @endif
    @if($etablissement->telephone ?? false) &nbsp;&nbsp; Tél&nbsp;: {{ $etablissement->telephone }} @endif
</div>

</body>
</html>
