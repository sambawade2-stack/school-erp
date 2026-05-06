<?php

namespace App\Providers;

use App\Models\Classe;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Route::model('class', Classe::class);

        // Admin passe toujours — les autres sont vérifiés via hasPermission()
        // Cela active @can('notes.create'), $this->authorize('notes.create'), etc.
        Gate::before(function ($user, string $ability): ?bool {
            $user->loadMissing('role.permissions');

            if ($user->isAdmin()) {
                return true;
            }

            return $user->hasPermission($ability) ? true : null;
        });
    }
}
