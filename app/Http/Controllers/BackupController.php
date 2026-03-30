<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(private BackupService $backupService) {}

    /**
     * Afficher la page de gestion des backups
     */
    public function index(): View
    {
        $backups = $this->backupService->list();
        $stats = $this->backupService->getStats();

        return view('backups.index', [
            'backups' => $backups,
            'stats' => $stats,
            'latestBackupAge' => $stats['latest_backup']['date_relative'] ?? null,
        ]);
    }

    /**
     * Créer une nouvelle sauvegarde
     */
    public function create(Request $request): RedirectResponse
    {
        $result = $this->backupService->backup();

        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message'])
                ->with('backup_file', $result['file']);
        }

        return redirect()->route('backups.index')
            ->with('error', $result['message']);
    }

    /**
     * Restaurer une sauvegarde (nécessite confirmation du mot de passe)
     */
    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'backup'   => 'required|string|max:255',
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($validated['password'], auth()->user()->password)) {
            return redirect()->route('backups.index')
                ->with('error', '❌ Mot de passe incorrect. Restauration annulée.');
        }

        $result = $this->backupService->restore($validated['backup']);

        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message'])
                ->with('security_backup', $result['security_backup'] ?? null);
        }

        return redirect()->route('backups.index')
            ->with('error', $result['message']);
    }

    /**
     * Supprimer une sauvegarde
     */
    public function delete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'backup' => 'required|string|max:255',
        ]);

        $result = $this->backupService->delete($validated['backup']);

        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('backups.index')
            ->with('error', $result['message']);
    }

    /**
     * Télécharger une sauvegarde
     */
    public function download(Request $request): StreamedResponse|RedirectResponse
    {
        $validated = $request->validate([
            'backup' => 'required|string|max:255',
        ]);

        $response = $this->backupService->download($validated['backup']);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        return redirect()->route('backups.index')
            ->with('error', 'Impossible de télécharger le fichier');
    }
}
