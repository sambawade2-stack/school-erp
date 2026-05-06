@extends('layouts.app')
@section('titre', 'Utilisateurs')
@section('titre-page', 'Gestion des utilisateurs')

@section('contenu')

{{-- Navigation admin par onglets --}}
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-200 pb-4">
    <a href="{{ route('admin.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Établissement
    </a>
    <a href="{{ route('admin.tarifs.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M9 14h.01M15 14h.01M12 14h.01M15 7h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Grille tarifaire
    </a>
    <a href="{{ route('admin.annees.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Années scolaires
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Utilisateurs
    </a>
</div>

<div x-data="{
    modal: false,
    mode: 'create',
    userId: null,
    form: { name: '', email: '', role_id: '', password: '', password_confirmation: '' },
    openCreate() {
        this.mode = 'create';
        this.userId = null;
        this.form = { name: '', email: '', role_id: '{{ $roles->first()?->id }}', password: '', password_confirmation: '' };
        this.modal = true;
    },
    openEdit(user) {
        this.mode = 'edit';
        this.userId = user.id;
        this.form = { name: user.name, email: user.email, role_id: user.role_id, password: '', password_confirmation: '' };
        this.modal = true;
    }
}">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-sm text-gray-500">{{ $users->count() }} utilisateur{{ $users->count() > 1 ? 's' : '' }} au total</p>
        </div>
        <button @click="openCreate()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvel utilisateur
        </button>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateur</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Rôle</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0
                                {{ $user->isAdmin() ? 'bg-red-500' : ($user->hasRole('professeur') ? 'bg-blue-500' : ($user->hasRole('surveillant') ? 'bg-amber-500' : 'bg-gray-400')) }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                <span class="text-xs text-blue-500 font-medium">Vous</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $user->email }}</td>
                    <td class="px-5 py-3.5">
                        @php
                            $badgeClass = match($user->role?->slug) {
                                'admin'       => 'bg-red-100 text-red-700',
                                'professeur'  => 'bg-blue-100 text-blue-700',
                                'surveillant' => 'bg-amber-100 text-amber-700',
                                'observateur' => 'bg-gray-100 text-gray-600',
                                default       => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $user->role?->nom ?? 'Aucun rôle' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Modifier --}}
                            <button @click="openEdit({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role_id' => $user->role_id]) }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>

                            {{-- Supprimer --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Supprimer {{ addslashes($user->name) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">Aucun utilisateur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Légende des rôles --}}
    <div class="mt-4 flex flex-wrap gap-3">
        @foreach($roles as $role)
        @php
            $badgeClass = match($role->slug) {
                'admin'       => 'bg-red-100 text-red-700',
                'professeur'  => 'bg-blue-100 text-blue-700',
                'surveillant' => 'bg-amber-100 text-amber-700',
                'observateur' => 'bg-gray-100 text-gray-600',
                default       => 'bg-gray-100 text-gray-500',
            };
        @endphp
        <span class="flex items-center gap-1.5 text-xs {{ $badgeClass }} px-2.5 py-1 rounded-full font-medium">
            {{ $role->nom }}
            <span class="opacity-60">— {{ $role->users->count() ?? 0 }} user(s)</span>
        </span>
        @endforeach
    </div>

    {{-- ═══ MODAL CRÉER / MODIFIER ════════════════════════════════════════════ --}}
    <div x-show="modal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/50" @click="modal = false"></div>

        {{-- Panneau --}}
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800" x-text="mode === 'create' ? 'Nouvel utilisateur' : 'Modifier l\'utilisateur'"></h3>
                <button @click="modal = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Formulaire CRÉATION --}}
            <form x-show="mode === 'create'" action="{{ route('admin.users.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" x-model="form.email" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                    <select name="role_id" x-model="form.role_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" x-model="form.password" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">8 caractères minimum</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" x-model="form.password_confirmation" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Créer l'utilisateur
                    </button>
                    <button type="button" @click="modal = false"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                </div>
            </form>

            {{-- Formulaire MODIFICATION --}}
            <template x-if="mode === 'edit'">
                <form :action="'{{ url('admin/users') }}/' + userId + '?_method=PUT'" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" name="name" :value="form.name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" :value="form.email" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                        <select name="role_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" :selected="form.role_id == {{ $role->id }}">{{ $role->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="border border-gray-200 bg-gray-50 rounded-lg p-4 space-y-3">
                        <p class="text-xs text-gray-500 font-medium">Nouveau mot de passe — laisser vide pour ne pas changer</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nouveau mot de passe</label>
                            <input type="password" name="password" autocomplete="new-password"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            Enregistrer les modifications
                        </button>
                        <button type="button" @click="modal = false"
                                class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                    </div>
                </form>
            </template>

        </div>
    </div>

</div>

@endsection
