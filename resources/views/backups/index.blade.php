@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🔄 Sauvegardes & Restauration</h1>
            <p class="text-gray-600 mt-2">Gérez les sauvegardes de votre base de données</p>
        </div>

        <!-- Alertes -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-red-600 text-xl mr-3">⚠️</span>
                    <div>
                        <h3 class="font-semibold text-red-800">Erreur</h3>
                        <ul class="text-red-700 text-sm mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 animate-pulse">
                <div class="flex items-center">
                    <span class="text-green-600 text-xl mr-3">✅</span>
                    <div>
                        <h3 class="font-semibold text-green-800">Succès</h3>
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        @if (session('security_backup'))
                            <p class="text-green-600 text-xs mt-2">
                                🛡️ Sauvegarde de sécurité: <code class="bg-green-100 px-2 py-1 rounded">{{ session('security_backup') }}</code>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="text-red-600 text-xl mr-3">❌</span>
                    <div>
                        <h3 class="font-semibold text-red-800">Erreur</h3>
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm">Total de sauvegardes</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total_count'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Max: {{ $stats['max_backups'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-gray-600 text-sm">Espace utilisé</p>
                <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_size_mb'] }} MB</p>
                <p class="text-xs text-gray-500 mt-2">Max: {{ $stats['max_size_mb'] }} MB</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm">Dernière sauvegarde</p>
                <p class="text-lg font-bold text-green-600 mt-2">
                    {{ $stats['latest_backup']['date_formatted'] ?? 'Aucune' }}
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $stats['latest_backup']['date_relative'] ?? '-' }}
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <p class="text-gray-600 text-sm">Taille dernière</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">
                    {{ $stats['latest_backup']['size_mb'] ?? '-' }} MB
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Actions</h2>

                <form method="POST" action="{{ route('backups.create') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold px-6 py-3 rounded-lg transition shadow-md hover:shadow-lg">
                        🔄 Nouvelle Sauvegarde
                    </button>
                </form>

                <p class="text-sm text-gray-600 mt-4">
                    ✓ Sauvegarde complète de la base de données<br>
                    ✓ Stockée dans: <code class="bg-gray-100 px-2 py-1 rounded">storage/backups/</code><br>
                    ✓ Limite: {{ $stats['max_backups'] }} fichiers maximum
                </p>
            </div>
        </div>

        <!-- Liste des sauvegardes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">📁 Sauvegardes disponibles</h2>
            </div>

            @if ($backups->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-500 text-lg">Aucune sauvegarde trouvée</p>
                    <p class="text-gray-400 text-sm mt-2">Créez votre première sauvegarde en cliquant sur le bouton ci-dessus</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fichier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Taille</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Âge</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($backups as $backup)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="text-lg mr-2">💾</span>
                                            <code class="text-sm text-gray-900 font-mono bg-gray-100 px-3 py-1 rounded">
                                                {{ $backup['name'] }}
                                            </code>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $backup['date_formatted'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $backup['size_mb'] }} MB
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $backup['date_relative'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 flex justify-end">
                                        <!-- Restaurer -->
                                        <button type="button"
                                                onclick="ouvrirModalRestauration('{{ $backup['name'] }}')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-2 rounded transition text-sm">
                                            ↩️ Restaurer
                                        </button>

                                        <!-- Télécharger -->
                                        <form method="POST" action="{{ route('backups.download') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="backup" value="{{ $backup['name'] }}">
                                            <button type="submit" title="Télécharger cette sauvegarde"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-3 py-2 rounded transition text-sm">
                                                ⬇️ Télécharger
                                            </button>
                                        </form>

                                        <!-- Supprimer -->
                                        <form method="POST" action="{{ route('backups.delete') }}" class="inline"
                                              onsubmit="return confirm('🗑️ Supprimer cette sauvegarde?\n\nCette action est irréversible.');">
                                            @csrf
                                            <input type="hidden" name="backup" value="{{ $backup['name'] }}">
                                            <button type="submit" title="Supprimer cette sauvegarde"
                                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-2 rounded transition text-sm">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Documentation -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">ℹ️ À propos des sauvegardes</h3>
            <ul class="space-y-2 text-blue-800 text-sm">
                <li>✅ <strong>Automatique:</strong> Une sauvegarde de sécurité est créée avant chaque restauration</li>
                <li>✅ <strong>Sécurisée:</strong> Vérification de l'intégrité des fichiers</li>
                <li>✅ <strong>Complète:</strong> Toutes les données sont sauvegardées</li>
                <li>✅ <strong>Historique:</strong> Conserve les {{ $stats['max_backups'] }} dernières sauvegardes</li>
                <li>⚠️ <strong>Restauration:</strong> Crée un backup avant de restaurer pour plus de sécurité</li>
            </ul>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .animate-pulse {
        animation: pulse 2s infinite;
    }
</style>

{{-- Modal confirmation restauration --}}
<div id="modal-restauration" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-1">⚠️ Confirmer la restauration</h3>
        <p class="text-sm text-gray-600 mb-4">
            Cette action remplace toute la base de données. Une sauvegarde de sécurité sera créée automatiquement avant.<br>
            <strong>Confirmez votre mot de passe pour continuer.</strong>
        </p>

        <form method="POST" action="{{ route('backups.restore') }}" id="form-restauration">
            @csrf
            <input type="hidden" name="backup" id="modal-backup-name">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Votre mot de passe</label>
                <input type="password" name="password" required autofocus
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Entrez votre mot de passe">
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="fermerModalRestauration()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    Confirmer la restauration
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirModalRestauration(filename) {
    document.getElementById('modal-backup-name').value = filename;
    document.getElementById('modal-restauration').classList.remove('hidden');
}
function fermerModalRestauration() {
    document.getElementById('modal-restauration').classList.add('hidden');
    document.getElementById('form-restauration').querySelector('input[type=password]').value = '';
}
document.getElementById('modal-restauration').addEventListener('click', function(e) {
    if (e.target === this) fermerModalRestauration();
});
</script>
@endsection
