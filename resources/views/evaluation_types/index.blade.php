@extends('layouts.app')
@section('titre', 'Types d\'évaluation')
@section('titre-page', 'Types d\'évaluation')
@section('breadcrumb', 'Administration / Types d\'évaluation')

@section('contenu')

@if(session('succes'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
    {{ session('succes') }}
</div>
@endif
@if(session('erreur'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
    {{ session('erreur') }}
</div>
@endif

<div class="grid grid-cols-3 gap-6">

    {{-- ── Liste des types ── --}}
    <div class="col-span-2 space-y-4">

        {{-- Indicateur somme des poids --}}
        @php $pct = round($sommePoids * 100); $ok = abs($pct - 100) < 1; @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">Répartition des poids</h3>
                <span class="text-sm font-medium {{ $ok ? 'text-green-600' : 'text-amber-600' }}">
                    Total : {{ $pct }}% {{ $ok ? '✓' : '⚠ doit être 100%' }}
                </span>
            </div>
            @if($types->count())
            <div class="flex rounded-full overflow-hidden h-5 gap-px">
                @foreach($types as $type)
                <div class="h-full flex items-center justify-center text-white text-xs font-medium transition-all"
                     style="width:{{ round($type->poids * 100) }}%; background-color:{{ $type->couleur }};"
                     title="{{ $type->nom }} – {{ $type->pourcentage }}">
                    {{ $type->pourcentage }}
                </div>
                @endforeach
                @if(!$ok)
                <div class="h-full bg-gray-200 flex-1"></div>
                @endif
            </div>
            <div class="flex flex-wrap gap-3 mt-3">
                @foreach($types as $type)
                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="w-3 h-3 rounded-full inline-block" style="background:{{ $type->couleur }}"></span>
                    {{ $type->nom }} ({{ $type->pourcentage }})
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-2">Aucun type défini</p>
            @endif
        </div>

        {{-- Table des types --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Types d'évaluation</h3>
                <span class="text-xs text-gray-400">{{ $types->count() }} type(s)</span>
            </div>

            @if($types->count())
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-xs border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left">Nom</th>
                        <th class="px-5 py-3 text-center">Poids</th>
                        <th class="px-5 py-3 text-center">Notes liées</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($types as $type)
                    <tr class="hover:bg-gray-50 transition-colors" id="row-{{ $type->id }}">
                        {{-- Affichage normal --}}
                        <td class="px-5 py-3 view-{{ $type->id }}">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $type->couleur }}"></span>
                                <span class="font-medium text-gray-800">{{ $type->nom }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center view-{{ $type->id }}">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:{{ $type->couleur }}20; color:{{ $type->couleur }}">
                                {{ $type->pourcentage }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center view-{{ $type->id }}">
                            <span class="text-gray-500">{{ $type->notes()->count() }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 view-{{ $type->id }}">
                            {{ $type->description ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center view-{{ $type->id }}">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="toggleEdit({{ $type->id }})"
                                        class="text-xs px-2 py-1 border border-blue-200 text-blue-600 rounded hover:bg-blue-50 transition-colors">
                                    Modifier
                                </button>
                                <form method="POST" action="{{ route('evaluation-types.destroy', $type) }}"
                                      onsubmit="return confirm('Supprimer ce type ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs px-2 py-1 border border-red-200 text-red-500 rounded hover:bg-red-50 transition-colors">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Formulaire d'édition inline (caché) --}}
                        <td colspan="5" class="px-5 py-3 hidden edit-{{ $type->id }}">
                            <form method="POST" action="{{ route('evaluation-types.update', $type) }}" class="flex flex-wrap gap-3 items-end">
                                @csrf @method('PUT')
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nom</label>
                                    <input type="text" name="nom" value="{{ $type->nom }}" required
                                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-36">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Poids (0–1)</label>
                                    <input type="number" name="poids" value="{{ $type->poids }}" step="0.01" min="0.01" max="1" required
                                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-24">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Couleur</label>
                                    <input type="color" name="couleur" value="{{ $type->couleur }}"
                                           class="border border-gray-300 rounded h-9 w-14 cursor-pointer">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 mb-1">Description</label>
                                    <input type="text" name="description" value="{{ $type->description }}"
                                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-full">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                        Enregistrer
                                    </button>
                                    <button type="button" onclick="toggleEdit({{ $type->id }})"
                                            class="px-3 py-1.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-10 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm">Aucun type d'évaluation. Créez-en un.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Formulaire de création ── --}}
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-5">
            <h3 class="font-semibold text-gray-800 mb-4">Nouveau type</h3>

            <form method="POST" action="{{ route('evaluation-types.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" placeholder="ex: Examen, Devoir, Contrôle" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           value="{{ old('nom') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Poids (0 à 1) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="poids" placeholder="0.40" step="0.01" min="0.01" max="1" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           value="{{ old('poids') }}">
                    <p class="text-xs text-gray-400 mt-1">Ex: 0.40 = 40% du total de la note</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="couleur" value="#3B82F6"
                               class="border border-gray-300 rounded h-9 w-14 cursor-pointer">
                        <span class="text-xs text-gray-400">Couleur du badge</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" placeholder="Note optionnelle..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           value="{{ old('description') }}">
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Créer le type
                </button>
            </form>

            {{-- Guide de configuration --}}
            <div class="mt-5 p-3 bg-blue-50 rounded-lg text-xs text-blue-700 space-y-1.5">
                <p class="font-semibold">Exemple de configuration :</p>
                <div class="flex justify-between"><span>Devoir</span><span class="font-medium">30% (0.30)</span></div>
                <div class="flex justify-between"><span>Contrôle</span><span class="font-medium">30% (0.30)</span></div>
                <div class="flex justify-between"><span>Examen</span><span class="font-medium">40% (0.40)</span></div>
                <div class="flex justify-between border-t border-blue-200 pt-1 font-semibold">
                    <span>Total</span><span>100% (1.00)</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function toggleEdit(id) {
    // Toggle view cells
    document.querySelectorAll('.view-' + id).forEach(el => el.classList.toggle('hidden'));
    // Toggle edit cell
    document.querySelectorAll('.edit-' + id).forEach(el => el.classList.toggle('hidden'));
}
</script>

@endsection
