@extends('layouts.app')
@section('titre', 'Grille tarifaire')
@section('titre-page', 'Grille tarifaire')

@section('contenu')

@php $anneeActiveNav = \App\Models\AnneeScolaire::active(); @endphp
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-4">
    <a href="{{ route('admin.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Établissement
    </a>
    <a href="{{ route('admin.tarifs.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M9 14h.01M15 14h.01M12 14h.01M15 7h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Grille tarifaire
    </a>
    <a href="{{ route('admin.annees.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Années scolaires
        @if($anneeActiveNav)<span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-semibold">{{ $anneeActiveNav->libelle }}</span>@else<span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-xs rounded-full font-semibold">!</span>@endif
    </a>
</div>

{{-- Sélecteur d'année --}}
<div class="flex items-center gap-3 mb-5">
    @foreach($annees as $a)
    <a href="{{ route('admin.tarifs.index', ['annee' => $a]) }}"
       class="px-3 py-1.5 rounded-lg text-sm font-medium border transition-colors
              {{ $a === $anneeSelectionnee ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
        {{ $a }}
    </a>
    @endforeach
    @if($annees->isEmpty())
    <p class="text-sm text-amber-600">Aucune année scolaire configurée.
        <a href="{{ route('admin.annees.index') }}" class="underline">Créer une année →</a>
    </p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Formulaire ajout tarif --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Ajouter un tarif</h3>
        <form action="{{ route('admin.tarifs.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="annee_scolaire" value="{{ $anneeSelectionnee }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau <span class="text-red-500">*</span></label>
                <select name="niveau" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    <option value="elementaire" {{ old('niveau') === 'elementaire' ? 'selected' : '' }}>Élémentaire</option>
                    <option value="college"     {{ old('niveau') === 'college'     ? 'selected' : '' }}>Collège</option>
                    <option value="terminal"    {{ old('niveau') === 'terminal'    ? 'selected' : '' }}>Terminal</option>
                </select>
                @error('niveau')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type de frais <span class="text-red-500">*</span></label>
                <select name="type_frais" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choisir --</option>
                    @foreach(\App\Models\Tarif::TYPES as $val => $label)
                    <option value="{{ $val }}" {{ old('type_frais') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type_frais')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle') }}" placeholder="ex: Scolarité T1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('libelle')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                <input type="number" name="montant" value="{{ old('montant') }}" min="0" step="1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('montant')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                + Ajouter le tarif
            </button>
        </form>
    </div>

    {{-- Liste des tarifs groupés par niveau --}}
    <div class="lg:col-span-2 space-y-5">

        @php
            $niveauxOrdre = ['elementaire' => 'Élémentaire', 'college' => 'Collège', 'terminal' => 'Terminal'];
            $couleurs = [
                'elementaire' => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'badge_bg' => '#dbeafe', 'badge_text' => '#1e40af'],
                'college'     => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'badge_bg' => '#dcfce7', 'badge_text' => '#166534'],
                'terminal'    => ['bg' => '#faf5ff', 'border' => '#e9d5ff', 'badge_bg' => '#ede9fe', 'badge_text' => '#5b21b6'],
            ];
        @endphp

        @forelse($niveauxOrdre as $niveauKey => $niveauLabel)
        @php $lignes = $tarifs[$niveauKey] ?? collect(); @endphp
        <div class="bg-white rounded-xl shadow-sm border p-5"
             style="border-color: {{ $couleurs[$niveauKey]['border'] }}; background: {{ $couleurs[$niveauKey]['bg'] }}">

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold"
                          style="background:{{ $couleurs[$niveauKey]['badge_bg'] }};color:{{ $couleurs[$niveauKey]['badge_text'] }}">
                        {{ $niveauLabel }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $lignes->count() }} tarif(s)</span>
                </div>
                @if($lignes->isNotEmpty())
                <span class="text-sm font-semibold text-gray-700">
                    Total : {{ number_format($lignes->sum('montant'), 0, ',', ' ') }} FCFA
                </span>
                @endif
            </div>

            @if($lignes->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">Aucun tarif défini pour ce niveau.</p>
            @else
            <div class="space-y-2">
                @foreach($lignes as $tarif)
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                {{ \App\Models\Tarif::TYPES[$tarif->type_frais] ?? $tarif->type_frais }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $tarif->libelle }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        {{-- Inline edit form --}}
                        <form action="{{ route('admin.tarifs.update', $tarif) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="hidden" name="libelle" value="{{ $tarif->libelle }}">
                            <input type="number" name="montant" value="{{ $tarif->montant }}" min="0" step="1"
                                   class="w-28 border border-gray-300 rounded px-2 py-1 text-sm text-right focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">✓</button>
                        </form>
                    </div>
                    <form action="{{ route('admin.tarifs.destroy', $tarif) }}" method="POST"
                          onsubmit="return confirm('Supprimer ce tarif ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
            Aucun tarif pour cette année. Utilisez le formulaire pour en ajouter.
        </div>
        @endforelse

    </div>
</div>

@endsection
