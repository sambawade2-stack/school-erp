@extends('layouts.app')
@section('titre', 'Nouveau Paiement')
@section('titre-page', 'Enregistrer un Paiement')

@section('contenu')

<x-btn-retour :href="route('paiements.index')" label="Retour aux paiements" breadcrumb="Nouveau paiement" />

@php
    $typeColors = \App\Models\Tarif::TYPE_COLORS;
    $typeLabels = \App\Models\Tarif::TYPES;
    $prefill = $prefill ?? [];
@endphp

@if(!empty($prefill['montant_restant']))
<div class="mb-4 flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-sm">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>
        Paiement partiel — Solde restant à régler :
        <strong>{{ number_format($prefill['montant_restant'], 0, ',', ' ') }} XOF</strong>
        pour <strong>{{ $typeLabels[$prefill['type_paiement']] ?? $prefill['type_paiement'] }}</strong>
        {{ $prefill['trimestre'] ? '(' . $prefill['trimestre'] . ')' : '' }}
    </span>
</div>
@endif

<div class="max-w-4xl space-y-4">

    {{-- Sélection étudiant --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Élève <span class="text-red-500">*</span></label>
        <select id="etudiant_select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">-- Choisir un élève --</option>
            @foreach($etudiants as $etu)
            <option value="{{ $etu->id }}"
                    data-niveau="{{ $niveauxEtudiants[$etu->id] ?? '' }}"
                    {{ (old('etudiant_id') ?? $etudiant?->id) == $etu->id ? 'selected' : '' }}>
                {{ $etu->nom_complet }}{{ $etu->classe ? ' (' . $etu->classe->nom . ')' : '' }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Tarifs prédéfinis --}}
    @if($tarifs->isNotEmpty())
    @php
        $groupeInscription = \App\Models\Tarif::GROUPE_INSCRIPTION;
        $tousLesTarifs = $tarifs->flatten();
        $tarifsInscription = $tousLesTarifs->whereIn('type_frais', $groupeInscription);
        $tarifsAutres = $tousLesTarifs->whereNotIn('type_frais', $groupeInscription);
    @endphp
    <div id="tarifs-panel" class="bg-white rounded-xl shadow-sm border border-blue-100 p-6" style="display:none">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Sélectionnez les frais à payer</h3>

        {{-- ── BLOC INSCRIPTION ── --}}
        @if($tarifsInscription->isNotEmpty())
        <div class="border border-gray-200 rounded-xl mb-4 overflow-hidden" id="bloc-inscription">
            {{-- En-tête du bloc --}}
            <div class="flex items-center justify-between px-4 py-3 bg-blue-600 cursor-pointer select-none"
                 onclick="toggleBlocInscription()">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-white font-bold text-sm tracking-wide uppercase">Frais d'Inscription</span>
                    <span class="text-blue-200 text-xs">(1ère Mensualité · Avance Juillet · Tenues · Assurance Maladie)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="bloc-inscription-total" class="text-white font-bold text-sm"></span>
                    <span class="text-blue-200 text-xs">Tout sélectionner</span>
                    <div id="bloc-inscription-check"
                         class="w-5 h-5 rounded border-2 border-white flex items-center justify-center transition-colors"
                         style="background:transparent">
                    </div>
                </div>
            </div>
            {{-- Grille des 4 types --}}
            <div class="grid grid-cols-2 gap-px bg-gray-100" id="tarifs-inscription-grid">
                @foreach($tarifsInscription as $tarif)
                <button type="button"
                        data-niveau="{{ $tarif->niveau }}"
                        data-type="{{ $tarif->type_frais }}"
                        data-montant="{{ $tarif->montant }}"
                        data-libelle="{{ $tarif->libelle }}"
                        data-groupe="inscription"
                        onclick="selectionnerTarif(this)"
                        class="tarif-btn text-left px-4 py-3 bg-white hover:bg-blue-50 transition-colors"
                        style="display:none">
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">{{ $typeLabels[$tarif->type_frais] ?? $tarif->type_frais }}</p>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $tarif->libelle }}</p>
                    <p class="text-sm font-bold text-blue-700 mt-1">{{ number_format($tarif->montant, 0, ',', ' ') }} FCFA</p>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── AUTRES TYPES (Logement, etc.) ── --}}
        @if($tarifsAutres->isNotEmpty())
        <div class="grid grid-cols-2 gap-2" id="tarifs-autres">
            @foreach($tarifsAutres as $tarif)
            <button type="button"
                    data-niveau="{{ $tarif->niveau }}"
                    data-type="{{ $tarif->type_frais }}"
                    data-montant="{{ $tarif->montant }}"
                    data-libelle="{{ $tarif->libelle }}"
                    onclick="selectionnerTarif(this)"
                    class="tarif-btn text-left px-3 py-2.5 border border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-colors"
                    style="display:none">
                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $typeLabels[$tarif->type_frais] ?? $tarif->type_frais }}</p>
                <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $tarif->libelle }}</p>
                <p class="text-sm font-bold text-blue-700 mt-1">{{ number_format($tarif->montant, 0, ',', ' ') }} FCFA</p>
            </button>
            @endforeach
        </div>
        @endif

        <p class="text-xs text-gray-400 mt-3">Cochez plusieurs frais pour créer un paiement par type.</p>
    </div>
    @endif

    {{-- Résumé des paiements par type (généré dynamiquement) --}}
    <div id="recapitulatif" style="display:none">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Récapitulatif — un paiement sera créé par type</h3>
        <div id="recap-cards" class="grid grid-cols-1 gap-3"></div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('paiements.store') }}" method="POST" id="paiement-form">
        @csrf
        <input type="hidden" name="etudiant_id" id="etudiant_id_hidden" value="{{ old('etudiant_id', $etudiant?->id) }}">
        <input type="hidden" name="paiements_json" id="paiements_json" value="">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">

            {{-- Mode manuel (sans tarifs ou en complément) --}}
            <div id="mode-manuel">
                <p class="text-xs text-gray-400 mb-3">Ou saisissez manuellement :</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant total dû <span class="text-red-500">*</span></label>
                        <input type="number" name="montant_total" id="montant_total" value="{{ old('montant_total', $prefill['montant_restant'] ?? '') }}" step="any" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant versé <span class="text-red-500">*</span></label>
                        <input type="number" name="montant" id="montant" value="{{ old('montant', $prefill['montant_restant'] ?? '') }}" step="any" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type_paiement" id="type_paiement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @foreach($typeLabels as $val => $label)
                            <option value="{{ $val }}" {{ old('type_paiement', $prefill['type_paiement'] ?? 'premiere_mensualite') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date_paiement" id="date_paiement" value="{{ old('date_paiement', today()->format('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Trimestre</label>
                        <select name="trimestre" id="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Aucun --</option>
                            @foreach(['T1','T2','T3','S1','S2','S3'] as $t)
                            <option value="{{ $t }}" {{ old('trimestre', $prefill['trimestre'] ?? $trimestreDefaut) === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Champs communs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire <span class="text-red-500">*</span></label>
                    <input type="text" name="annee_scolaire" id="annee_scolaire" value="{{ old('annee_scolaire', $anneeActive?->libelle ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div id="date-trimestre-multi" style="display:none">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                            <input type="date" id="date_paiement_multi" value="{{ today()->format('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Trimestre</label>
                            <select id="trimestre_multi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">-- Aucun --</option>
                                @foreach(['T1','T2','T3','S1','S2','S3'] as $t)
                                <option value="{{ $t }}" {{ old('trimestre', $trimestreDefaut) === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Remarque</label>
                <textarea name="remarque" id="remarque" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('remarque') }}</textarea>
            </div>
        </div>

        {{-- Total général --}}
        <div id="total-general" class="bg-white rounded-xl shadow-sm border border-green-200 p-4 mt-4" style="display:none">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">Total général</span>
                <span id="total-general-montant" class="text-xl font-bold text-green-600">0 FCFA</span>
            </div>
            <p id="total-general-detail" class="text-xs text-gray-400 mt-1"></p>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Enregistrer <span id="btn-count"></span>
            </button>
            <a href="{{ route('paiements.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>

<style>
.tarif-btn { cursor: pointer; position: relative; }
.tarif-btn:hover { background: #eff6ff; }
.tarif-btn.selected { background: #dbeafe !important; box-shadow: inset 0 0 0 2px #2563eb; }
.tarif-btn.selected::after {
    content: '\2713';
    position: absolute;
    top: 6px; right: 8px;
    font-size: 14px;
    font-weight: bold;
    color: #2563eb;
}
/* Boutons hors bloc inscription */
#tarifs-autres .tarif-btn { border: 1px solid #e5e7eb; border-radius: 0.5rem; }
#tarifs-autres .tarif-btn.selected { border-color: #2563eb !important; background: #eff6ff !important; }
.recap-card { border-radius: 0.75rem; padding: 1rem; border: 1px solid; }
</style>

<script>
const niveaux = @json($niveauxEtudiants ?? []);
const typeLabels = @json($typeLabels);
const typeColors = @json($typeColors);
let multiMode = false;

document.getElementById('etudiant_select').addEventListener('change', function () {
    const id = this.value;
    document.getElementById('etudiant_id_hidden').value = id;
    document.querySelectorAll('.tarif-btn.selected').forEach(b => b.classList.remove('selected'));
    recalculer();
    afficherTarifs(niveaux[id] ?? null);
});

window.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('etudiant_select');
    if (sel.value) {
        document.getElementById('etudiant_id_hidden').value = sel.value;
        afficherTarifs(niveaux[sel.value] ?? null);
    }
});

function afficherTarifs(niveau) {
    const panel = document.getElementById('tarifs-panel');
    if (!panel) return;
    const btns = document.querySelectorAll('.tarif-btn');
    let visible = 0;
    btns.forEach(btn => {
        if (!niveau || btn.dataset.niveau === niveau) {
            btn.style.display = '';
            visible++;
        } else {
            btn.style.display = 'none';
            btn.classList.remove('selected');
        }
    });
    panel.style.display = visible > 0 ? '' : 'none';
}

function selectionnerTarif(btn) {
    btn.classList.toggle('selected');
    updateBlocInscriptionUI();
    recalculer();
}

function recalculer() {
    const selectionnes = document.querySelectorAll('.tarif-btn.selected');

    // Grouper par type
    const parType = {};
    selectionnes.forEach(b => {
        const type = b.dataset.type;
        if (!parType[type]) {
            parType[type] = { lignes: [], total: 0 };
        }
        const montant = parseFloat(b.dataset.montant);
        parType[type].lignes.push({ libelle: b.dataset.libelle, montant: montant });
        parType[type].total += montant;
    });

    const types = Object.keys(parType);
    multiMode = types.length > 0;

    // Afficher/masquer mode
    const modeManuel = document.getElementById('mode-manuel');
    const recap = document.getElementById('recapitulatif');
    const totalPanel = document.getElementById('total-general');
    const dateTriMulti = document.getElementById('date-trimestre-multi');

    if (multiMode) {
        // Masquer les champs manuels montant/type, garder date/trimestre en mode multi
        modeManuel.style.display = 'none';
        recap.style.display = '';
        totalPanel.style.display = '';
        dateTriMulti.style.display = '';

        // Générer les cartes récapitulatives
        const container = document.getElementById('recap-cards');
        container.innerHTML = '';
        let totalGeneral = 0;

        types.forEach(type => {
            const colors = typeColors[type] || { color: '#6b7280', bg: '#f9fafb', border: '#e5e7eb' };
            const label = typeLabels[type] || type;
            const data = parType[type];
            totalGeneral += data.total;

            const card = document.createElement('div');
            card.className = 'recap-card';
            card.style.background = colors.bg;
            card.style.borderColor = colors.border;

            let lignesHtml = data.lignes.map(l =>
                `<div class="flex justify-between text-sm"><span class="text-gray-600">${l.libelle}</span><span class="font-medium">${formatMontant(l.montant)} FCFA</span></div>`
            ).join('');

            card.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold" style="color: ${colors.color}">${label}</span>
                    <span class="text-lg font-bold" style="color: ${colors.color}">${formatMontant(data.total)} FCFA</span>
                </div>
                <div class="space-y-1">${lignesHtml}</div>
                <div class="mt-3 pt-3 border-t" style="border-color: ${colors.border}">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-gray-500">Montant versé :</label>
                        <input type="number" data-type="${type}" class="montant-verse flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               value="${data.total}" min="0" step="any" onchange="updateJson()">
                        <span class="text-xs text-gray-400">/ ${formatMontant(data.total)}</span>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        document.getElementById('total-general-montant').textContent = formatMontant(totalGeneral) + ' FCFA';
        document.getElementById('total-general-detail').textContent = types.length + ' paiement(s) seront créés';
        document.getElementById('btn-count').textContent = '(' + types.length + ' paiements)';

        updateJson();
    } else {
        modeManuel.style.display = '';
        recap.style.display = 'none';
        totalPanel.style.display = 'none';
        dateTriMulti.style.display = 'none';
        document.getElementById('btn-count').textContent = '';
        document.getElementById('paiements_json').value = '';

        // Reset champs manuels
        document.getElementById('montant_total').value = '';
        document.getElementById('montant').value = '';
    }
}

function updateJson() {
    const selectionnes = document.querySelectorAll('.tarif-btn.selected');
    const parType = {};

    selectionnes.forEach(b => {
        const type = b.dataset.type;
        if (!parType[type]) {
            parType[type] = { lignes: [], total: 0 };
        }
        const montant = parseFloat(b.dataset.montant);
        parType[type].lignes.push({ libelle: b.dataset.libelle, montant: montant });
        parType[type].total += montant;
    });

    const paiements = [];
    document.querySelectorAll('.montant-verse').forEach(input => {
        const type = input.dataset.type;
        if (parType[type]) {
            paiements.push({
                type_paiement: type,
                montant_total: parType[type].total,
                montant: parseFloat(input.value) || 0,
                lignes: parType[type].lignes
            });
        }
    });

    document.getElementById('paiements_json').value = JSON.stringify(paiements);
}

function formatMontant(n) {
    return new Intl.NumberFormat('fr-FR').format(n);
}

// ── Bloc Inscription : tout sélectionner / tout désélectionner ──
const groupeInscription = @json(\App\Models\Tarif::GROUPE_INSCRIPTION);

function toggleBlocInscription() {
    const btns = document.querySelectorAll('#tarifs-inscription-grid .tarif-btn[style*="display"]:not([style*="none"]), #tarifs-inscription-grid .tarif-btn:not([style])');
    const visibles = document.querySelectorAll('#tarifs-inscription-grid .tarif-btn:not([style*="none"])');
    const tousSelectionnes = Array.from(visibles).every(b => b.classList.contains('selected'));

    visibles.forEach(b => {
        if (tousSelectionnes) b.classList.remove('selected');
        else b.classList.add('selected');
    });

    updateBlocInscriptionUI();
    recalculer();
}

function updateBlocInscriptionUI() {
    const visibles = document.querySelectorAll('#tarifs-inscription-grid .tarif-btn:not([style*="none"])');
    const tousSelectionnes = visibles.length > 0 && Array.from(visibles).every(b => b.classList.contains('selected'));
    const check = document.getElementById('bloc-inscription-check');
    if (!check) return;

    if (tousSelectionnes) {
        check.style.background = 'white';
        check.innerHTML = '<svg style="width:14px;height:14px;color:#2563eb" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
    } else {
        check.style.background = 'transparent';
        check.innerHTML = '';
    }

    // Total du bloc
    let total = 0;
    visibles.forEach(b => { if (b.classList.contains('selected')) total += parseFloat(b.dataset.montant); });
    const el = document.getElementById('bloc-inscription-total');
    if (el) el.textContent = total > 0 ? formatMontant(total) + ' FCFA' : '';
}

// Intercepter le submit pour injecter date/trimestre en mode multi
document.getElementById('paiement-form').addEventListener('submit', function(e) {
    if (multiMode) {
        const date = document.getElementById('date_paiement_multi').value;
        const trimestre = document.getElementById('trimestre_multi').value;
        const json = JSON.parse(document.getElementById('paiements_json').value || '[]');

        json.forEach(p => {
            p.date_paiement = date;
            p.trimestre = trimestre;
        });

        document.getElementById('paiements_json').value = JSON.stringify(json);

        // Désactiver les champs manuels required pour ne pas bloquer le submit
        document.getElementById('montant_total').removeAttribute('required');
        document.getElementById('montant').removeAttribute('required');
        const tpSel = document.getElementById('type_paiement');
        if (tpSel) tpSel.removeAttribute('required');
        document.getElementById('date_paiement').removeAttribute('required');
    }
});
</script>

@endsection
