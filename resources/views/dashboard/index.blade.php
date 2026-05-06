@extends('layouts.app')
@section('titre', 'Dashboard')
@section('titre-page', 'Système de Gestion Scolaire')

@section('contenu')

<h2 class="text-xl font-bold text-gray-700 mb-6">Bienvenue, {{ auth()->user()->name ?? 'Administrateur' }} !</h2>

@can('finances.view')
@if($alarme_paiement)
<div class="mb-5 flex items-start gap-4 bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm">
    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-red-800">Alerte paiement — {{ ucfirst($alarme_paiement['mois']) }}</p>
        <p class="text-sm text-red-700 mt-0.5">
            <span class="font-bold text-red-900 text-base">{{ $alarme_paiement['nb'] }}</span>
            élève{{ $alarme_paiement['nb'] > 1 ? 's' : '' }} n'{{ $alarme_paiement['nb'] > 1 ? 'ont' : 'a' }} pas encore payé la mensualité de ce mois
            (date limite : le <strong>{{ $alarme_paiement['jour_limite'] }}</strong>).
        </p>
    </div>
    <a href="{{ route('impayes') }}"
       class="flex-shrink-0 px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap">
        Voir la liste
    </a>
</div>
@endif
@endcan

{{-- CARTES STATISTIQUES --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">

    {{-- Élèves — visible par tous --}}
    <div class="bg-blue-500 text-white rounded-xl p-5 shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Élèves</p>
                <p class="text-4xl font-bold mt-1">{{ number_format($statistiques['etudiants']) }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        @can('etudiants.view')
        <a href="{{ route('etudiants.index') }}" class="text-xs text-blue-100 hover:text-white">Voir tous &rarr;</a>
        @endcan
    </div>

    {{-- Enseignants — masqué pour observateur --}}
    @can('enseignants.view')
    <div class="bg-green-500 text-white rounded-xl p-5 shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Enseignants</p>
                <p class="text-4xl font-bold mt-1">{{ $statistiques['enseignants'] }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('enseignants.index') }}" class="text-xs text-green-100 hover:text-white">Voir tous &rarr;</a>
    </div>
    @endcan

    {{-- Classes — masqué pour observateur --}}
    @can('classes.view')
    <div class="bg-orange-500 text-white rounded-xl p-5 shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Classes</p>
                <p class="text-4xl font-bold mt-1">{{ $statistiques['classes'] }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('classes.index') }}" class="text-xs text-orange-100 hover:text-white">Voir toutes &rarr;</a>
    </div>
    @endcan

    {{-- Revenus du mois — finances uniquement (admin, observateur) --}}
    @can('finances.view')
    <div class="bg-purple-500 text-white rounded-xl p-5 shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Revenus</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($statistiques['paiements_mois'], 0, ',', ' ') }}</p>
                <p class="text-xs opacity-75 mt-0.5">XOF ce mois</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        @can('paiements.view')
        <a href="{{ route('paiements.index') }}" class="text-xs text-purple-100 hover:text-white">Voir tous &rarr;</a>
        @endcan
    </div>
    @endcan

    {{-- Impayés — finances uniquement (admin, observateur) --}}
    @can('finances.view')
    <div class="text-white rounded-xl p-5 shadow-md" style="background-color:#ef4444;">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Impayés</p>
                <p class="text-4xl font-bold mt-1">{{ $nb_impayes }}</p>
                <p class="text-xs opacity-75 mt-0.5">{{ $trimestreCourant }} · {{ $anneeScolaire }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('impayes') }}" class="text-xs text-red-100 hover:text-white">Voir la liste &rarr;</a>
    </div>
    @endcan

    {{-- Dépenses du mois — finances uniquement (admin, observateur) --}}
    @can('finances.view')
    <div class="bg-rose-600 text-white rounded-xl p-5 shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium opacity-90">Dépenses</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($statistiques['depenses_mois'], 0, ',', ' ') }}</p>
                <p class="text-xs opacity-75 mt-0.5">XOF ce mois</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        @can('depenses.view')
        <a href="{{ route('depenses.index') }}" class="text-xs text-rose-100 hover:text-white">Voir toutes &rarr;</a>
        @endcan
    </div>
    @endcan

</div>

{{-- TABLEAUX PRINCIPAUX — masqués pour observateur --}}
@canany(['etudiants.view', 'paiements.view'])
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

    @can('etudiants.view')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Derniers Élèves</h3>
            <a href="{{ route('etudiants.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 text-xs border-b border-gray-100">
                    <th class="pb-2 font-medium">Nom</th>
                    <th class="pb-2 font-medium">Classe</th>
                    <th class="pb-2 font-medium">Téléphone</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($derniers_etudiants as $etudiant)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-2.5">
                        <a href="{{ route('etudiants.show', $etudiant) }}" class="font-medium text-gray-800 hover:text-blue-600">
                            {{ $etudiant->nom_complet }}
                        </a>
                    </td>
                    <td class="py-2.5 text-gray-500">{{ $etudiant->classe?->nom ?? '—' }}</td>
                    <td class="py-2.5 text-gray-400">{{ $etudiant->telephone ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="py-4 text-center text-gray-400 text-sm">Aucun élève enregistré</td></tr>
                @endforelse
            </tbody>
        </table>
        @can('etudiants.create')
        <div class="mt-4 text-center">
            <a href="{{ route('etudiants.create') }}"
               class="inline-block px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                + Ajouter un élève
            </a>
        </div>
        @endcan
    </div>
    @endcan

    @can('paiements.view')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-800">Paiements Récents</h3>
            <a href="{{ route('paiements.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 text-xs border-b border-gray-100">
                    <th class="pb-2 font-medium">Élève</th>
                    <th class="pb-2 font-medium">Montant</th>
                    <th class="pb-2 font-medium">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($derniers_paiements as $paiement)
                <tr>
                    <td class="py-2 font-medium text-gray-700">{{ $paiement->etudiant->nom_complet }}</td>
                    <td class="py-2 font-semibold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} XOF</td>
                    <td class="py-2 text-gray-400 text-xs">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="py-3 text-center text-gray-400 text-sm">Aucun paiement</td></tr>
                @endforelse
            </tbody>
        </table>
        @can('paiements.create')
        <div class="mt-3 text-center">
            <a href="{{ route('paiements.create') }}"
               class="inline-block px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                + Nouveau paiement
            </a>
        </div>
        @endcan
    </div>
    @endcan

</div>
@endcanany

{{-- GRAPHIQUE — masqué pour observateur --}}
@canany(['etudiants.view', 'paiements.view'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="font-semibold text-gray-800 mb-4">Statistiques de l'année {{ now()->year }}</h3>
    <canvas id="graphiqueStats" height="60"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
const donneesEtudiants = @json($mois->pluck('etudiants'));
const donneesPaiements = @json($mois->pluck('paiements'));

new Chart(document.getElementById('graphiqueStats'), {
    type: 'bar',
    data: {
        labels: moisLabels,
        datasets: [
            {
                label: 'Nouveaux élèves',
                data: donneesEtudiants,
                backgroundColor: '#3B82F6',
                borderRadius: 4,
            },
            {
                label: 'Paiements (XOF)',
                data: donneesPaiements,
                backgroundColor: '#F97316',
                borderRadius: 4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y:  { position: 'left',  beginAtZero: true },
            y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } }
        }
    }
});
</script>
@endpush
@endcanany

@endsection
