@extends('layouts.app')
@section('titre', $enseignant->nom_complet)
@section('titre-page', 'Profil Personnel')

@section('contenu')

<x-btn-retour :href="route('enseignants.index')" label="Retour au personnel" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            @if($enseignant->photo)
            <img src="{{ $enseignant->photo_url }}"
                 class="w-24 h-24 rounded-full object-cover border-4 border-green-100 mx-auto mb-3"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-green-100 mx-auto mb-3" style="display:none;">
                {{ strtoupper(substr($enseignant->prenom, 0, 1) . substr($enseignant->nom, 0, 1)) }}
            </div>
            @else
            <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-green-100 mx-auto mb-3">
                {{ strtoupper(substr($enseignant->prenom, 0, 1) . substr($enseignant->nom, 0, 1)) }}
            </div>
            @endif
            <h2 class="font-bold text-gray-800 text-lg">{{ $enseignant->nom_complet }}</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1" style="background: #eff6ff; color: #2563eb;">
                {{ \App\Models\Enseignant::TYPES[$enseignant->type] ?? ucfirst($enseignant->type) }}
            </span>
            <p class="text-sm text-gray-500 mt-1">{{ $enseignant->specialite ?? '' }}</p>
            <span class="inline-flex mt-2 items-center px-3 py-1 rounded-full text-xs font-medium {{ $enseignant->statut === 'actif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                {{ ucfirst($enseignant->statut) }}
            </span>
            <div class="mt-5 text-left space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2"><span class="text-gray-400">Email</span><span class="font-medium text-gray-700">{{ $enseignant->email ?? '—' }}</span></div>
                <div class="flex justify-between border-b pb-2"><span class="text-gray-400">Téléphone</span><span class="font-medium text-gray-700">{{ $enseignant->telephone ?? '—' }}</span></div>
                <div class="flex justify-between pb-2"><span class="text-gray-400">Embauche</span><span class="font-medium text-gray-700">{{ $enseignant->date_embauche?->format('d/m/Y') ?? '—' }}</span></div>
            </div>
            <a href="{{ route('enseignants.edit', $enseignant) }}" class="mt-4 block px-3 py-2 bg-blue-600 text-white rounded-lg text-sm text-center hover:bg-blue-700 transition-colors">Modifier</a>
        </div>
    </div>
    <div class="lg:col-span-2">
        @if($enseignant->type === 'enseignant')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Matières enseignées</h3>
            @if($enseignant->matieres->count())
            <div class="space-y-2">
                @foreach($enseignant->matieres as $matiere)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="font-medium text-gray-700">{{ $matiere->nom }}</span>
                    <span class="text-sm text-gray-500">{{ $matiere->classe?->nom ?? '—' }}</span>
                    <span class="text-xs text-gray-400">Coef. {{ $matiere->coefficient }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Aucune matière assignée</p>
            @endif
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Informations</h3>
            <div class="text-sm text-gray-600 space-y-2">
                @if($enseignant->adresse)
                <p><span class="font-medium text-gray-700">Adresse :</span> {{ $enseignant->adresse }}</p>
                @endif
                <p class="text-gray-400 text-center py-4">Fiche du personnel</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
