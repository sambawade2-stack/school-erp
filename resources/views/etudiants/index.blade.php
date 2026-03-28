@extends('layouts.app')
@section('titre', 'Etudiants')
@section('titre-page', 'Gestion des Etudiants')
@section('breadcrumb', 'Liste de tous les etudiants')

@section('contenu')

<div class="flex items-center justify-between mb-5 gap-3 flex-wrap">
    <form id="form-recherche" action="{{ route('etudiants.index') }}" method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="recherche" id="input-recherche" value="{{ request('recherche') }}" placeholder="Rechercher un étudiant..."
               class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:220px;">

        <select name="classe_id" onchange="document.getElementById('form-recherche').submit()"
                class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:200px;">
            <option value="">Toutes les classes</option>
            <option value="sans_classe" {{ request('classe_id') === 'sans_classe' ? 'selected' : '' }}>Sans classe</option>
            @foreach($classes as $classe)
            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id && request('classe_id') !== 'sans_classe' ? 'selected' : '' }}>{{ $classe->nom }}</option>
            @endforeach
        </select>

        <select name="statut" onchange="document.getElementById('form-recherche').submit()"
                class="border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" style="min-width:180px;">
            <option value="">Actifs + Inactifs</option>
            <option value="actif"   {{ request('statut') === 'actif'   ? 'selected' : '' }}>Actif</option>
            <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
            <option value="archive" {{ request('statut') === 'archive' ? 'selected' : '' }}>Archives</option>
        </select>

        @if(request()->hasAny(['recherche', 'classe_id', 'statut']))
        <a href="{{ route('etudiants.index') }}" class="px-3 py-2 text-gray-500 rounded-lg text-sm hover:bg-gray-100 transition-colors whitespace-nowrap">
            Effacer
        </a>
        @endif
    </form>

    <div class="flex items-center gap-2">
        <a href="{{ route('etudiants.export.pdf', request()->only(['classe_id', 'statut'])) }}"
           class="flex items-center gap-1.5 px-3 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background:#dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF
        </a>
        <a href="{{ route('etudiants.export.csv', request()->only(['classe_id', 'statut'])) }}"
           class="flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            CSV
        </a>
        <a href="{{ route('etudiants.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un étudiant
        </a>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('input-recherche');
        var timer;

        // Restaurer le focus et positionner le curseur en fin de texte après rechargement
        @if(request('recherche'))
        input.focus();
        var val = input.value;
        input.value = '';
        input.value = val;
        @endif

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                document.getElementById('form-recherche').submit();
            }, 500);
        });
    })();
</script>
@endpush

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Photo</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Etudiant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Matricule</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Classe</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Telephone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($etudiants as $etudiant)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    @if($etudiant->photo)
                    <img src="{{ $etudiant->photo_url }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-gray-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-200" style="display:none;">
                        {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                    </div>
                    @else
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-200">
                        {{ strtoupper(substr($etudiant->prenom, 0, 1) . substr($etudiant->nom, 0, 1)) }}
                    </div>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-800">{{ $etudiant->nom_complet }}</div>
                    <div class="text-xs text-gray-400">{{ $etudiant->sexe === 'masculin' ? 'Garcon' : 'Fille' }}</div>
                </td>
                <td class="px-5 py-3 font-mono text-gray-600 text-xs">{{ $etudiant->matricule }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $etudiant->classe?->nom ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $etudiant->telephone ?? '—' }}</td>
                <td class="px-5 py-3">
                    @php
                        $badgeClasses = match($etudiant->statut) {
                            'actif'   => 'bg-green-100 text-green-700',
                            'archive' => 'bg-orange-100 text-orange-600',
                            default   => 'bg-red-100 text-red-600',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                        {{ $etudiant->statut === 'archive' ? 'Archivé' : ucfirst($etudiant->statut) }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('etudiants.show', $etudiant) }}"
                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir profil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('etudiants.edit', $etudiant) }}"
                           class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        @if($etudiant->statut === 'archive')
                        {{-- Bouton restaurer pour les archives --}}
                        <form action="{{ route('etudiants.restaurer', $etudiant) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Restaurer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </form>
                        @else
                        {{-- Bouton archiver --}}
                        <div x-data="{ ouvert: false }" class="relative">
                            <button type="button" @click="ouvert = true"
                                    class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-lg transition-colors" title="Archiver">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </button>
                            <div x-show="ouvert" x-cloak @keydown.escape.window="ouvert = false"
                                 class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6" @click.stop @click.outside="ouvert = false">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Archiver cet eleve</h3>
                                            <p class="text-sm text-gray-500 mt-0.5">Archiver <strong>{{ $etudiant->nom_complet }}</strong> ? Ses donnees seront conservees et vous pourrez le restaurer a tout moment.</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="ouvert = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</button>
                                        <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg" style="background:#ea580c;">Archiver</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-8 text-center text-gray-400">
                    Aucun etudiant trouve.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    @if($etudiants->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $etudiants->links() }}
    </div>
    @endif
</div>

<p class="text-xs text-gray-400 mt-3">Total : {{ $etudiants->total() }} etudiant(s)</p>

@endsection
