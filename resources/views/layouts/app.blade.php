<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>School ERP — @yield('titre', 'Tableau de Bord')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', 'Segoe UI Semibold', 'Segoe UI', system-ui, sans-serif; }
        .font-heading { font-family: 'Poppins', 'Segoe UI Semibold', 'Segoe UI', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900" style="font-feature-settings: 'kern' 1;">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden relative">

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

    {{-- ════════════ BARRE LATÉRALE ════════════ --}}
    <aside class="bg-blue-900 text-white flex flex-col flex-shrink-0 shadow-xl fixed inset-y-0 left-0 z-30 w-64 -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:z-auto" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        @php
            $sidebarEtab   = \App\Models\Etablissement::first();
            $sidebarAnnee  = \App\Models\AnneeScolaire::active();
        @endphp
        <div class="px-4 py-5 border-b border-blue-800">
            <div class="flex flex-col items-center text-center">
                @php $logoPath = storage_path('app/public/logo/' . $sidebarEtab?->logo); @endphp
                @if($sidebarEtab && $sidebarEtab->logo && file_exists($logoPath))
                <img src="{{ route('logo.etablissement') }}?v={{ @filemtime($logoPath) ?: 1 }}"
                     alt="Logo {{ $sidebarEtab->nom ?? 'School ERP' }}"
                     class="h-16 rounded-xl object-contain bg-white p-1.5 shadow-sm mb-3"
                     style="max-width: 90%;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center text-2xl font-bold shadow-sm mb-3"
                     style="display: none;">
                    {{ strtoupper(substr($sidebarEtab->sigle ?? $sidebarEtab->nom ?? 'S', 0, 1)) }}
                </div>
                @else
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center text-2xl font-bold shadow-sm mb-3">
                    {{ strtoupper(substr($sidebarEtab->sigle ?? $sidebarEtab->nom ?? 'S', 0, 1)) }}
                </div>
                @endif
                <p class="font-heading font-bold text-sm leading-tight text-white">{{ $sidebarEtab->nom ?? 'School ERP' }}</p>
                @if($sidebarEtab->sigle ?? null)
                <p class="text-xs text-blue-300 font-medium mt-1 leading-snug">{{ $sidebarEtab->sigle }}</p>
                @endif
            </div>
        </div>

        {{-- Année scolaire active --}}
        @if($sidebarAnnee)
        <div class="px-4 py-2 bg-blue-800/60 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0 animate-pulse"></span>
            <span class="text-xs text-blue-200 font-medium">{{ $sidebarAnnee->libelle }}</span>
            @if($sidebarAnnee->trimestre_actuel)
            <span class="text-xs text-blue-400 ml-auto">{{ $sidebarAnnee->trimestre_actuel }}</span>
            @endif
        </div>
        @else
        <div class="px-4 py-2 bg-amber-700/80 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-300 flex-shrink-0"></span>
            <a href="{{ route('admin.annees.index') }}" class="text-xs text-amber-100 hover:underline">Configurer une annee scolaire</a>
        </div>
        @endif

        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent;">
            @php
            $liens = [
                ['route' => 'dashboard',        'label' => 'Dashboard',       'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'etudiants.index',  'label' => 'Etudiants',       'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'enseignants.index','label' => 'Personnel',       'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['route' => 'classes.index',    'label' => 'Classes',         'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['route' => 'matieres.index',   'label' => 'Matieres',        'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['route' => 'sections.index',  'label' => 'Sections',        'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['route' => 'presences.index',  'label' => 'Presences',       'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['route' => 'examens.index',          'label' => 'Examens & Notes', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route' => 'evaluation-types.index', 'label' => 'Types d\'éval.',  'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                ['route' => 'paiements.index',  'label' => 'Paiements',       'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['route' => 'depenses.index',  'label' => 'Dépenses',        'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'internes.index',  'label' => 'Internat',        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a2 2 0 100-4 2 2 0 000 4z'],
                ['route' => 'chambres.index', 'label' => 'Chambres',        'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                ['route' => 'rapports.index',   'label' => 'Rapports',        'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route' => 'admin.index',      'label' => 'Administration',  'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                ['route' => 'parametres.index', 'label' => 'Parametres',      'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ];
            @endphp

            @foreach($liens as $lien)
            @php
                $segment = explode('.', $lien['route'])[0];
                $actif = request()->routeIs($lien['route']) || ($segment !== 'dashboard' && request()->routeIs($segment . '.*'));
            @endphp
            <a href="{{ route($lien['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150
                      {{ $actif ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0 {{ $actif ? 'text-white' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $lien['icon'] }}"/>
                </svg>
                <span>{{ $lien['label'] }}</span>
            </a>
            @endforeach
        </nav>

        <div class="px-3 py-3 border-t border-blue-800">
            <div class="flex items-center gap-2 px-3 py-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="text-xs text-blue-200 truncate">{{ auth()->user()->name ?? 'Administrateur' }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 px-3 py-2 text-blue-300 hover:text-white text-sm w-full rounded-lg hover:bg-red-600/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Deconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ════════════ ZONE PRINCIPALE ════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- BARRE DU HAUT --}}
        <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm flex-shrink-0 gap-3">
            {{-- Bouton hamburger (mobile) --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg font-heading font-bold text-gray-900 whitespace-nowrap">@yield('titre-page', 'Tableau de Bord')</h1>
                @hasSection('breadcrumb')
                <p class="text-sm text-gray-500 mt-1 font-medium">@yield('breadcrumb')</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                {{-- Recherche rapide --}}
                <form action="{{ route('etudiants.index') }}" method="GET" class="relative hidden sm:block">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <input type="text" name="recherche" placeholder="Rechercher un élève..."
                           value="{{ request('recherche') }}"
                           class="pl-9 pr-4 py-1.5 border border-gray-200 rounded-lg text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </form>

                {{-- Profil --}}
                <div class="flex items-center gap-2 pl-3 border-l border-gray-200">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Administrateur' }}</span>
                </div>
            </div>
        </header>

        {{-- CONTENU --}}
        <main class="flex-1 overflow-y-auto p-6">
            @if(session('succes'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm notification-auto-dismiss" style="transition: opacity 0.5s ease;">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('succes') }}
            </div>
            @endif

            @if(session('erreur'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm notification-auto-dismiss" style="transition: opacity 0.5s ease;">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('erreur') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm notification-auto-dismiss" style="transition: opacity 0.5s ease;">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('contenu')
        </main>
    </div>
</div>

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notification-auto-dismiss').forEach(function(el) {
        setTimeout(function() {
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }, 4000);
    });
});
</script>
</body>
</html>
