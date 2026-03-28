<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School ERP - Connexion</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen flex items-center justify-center">

<div class="w-full max-w-sm">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">School ERP</h1>
        <p class="text-blue-200 text-sm mt-1">Systeme de Gestion Scolaire</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Connexion Administrateur</h2>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              @error('email') border-red-400 @enderror">
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 rounded">
                <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg
                           transition-colors duration-150 text-sm">
                Se connecter
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            School ERP &copy; {{ date('Y') }}
        </p>
    </div>
</div>

</body>
</html>
