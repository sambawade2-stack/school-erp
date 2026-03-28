<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    /**
     * Sert le logo de l'établissement directement depuis le disque.
     * Contourne les problèmes de symlink Windows/Laragon.
     */
    public function servirLogo(): BinaryFileResponse|Response
    {
        $etablissement = Etablissement::first();

        if (!$etablissement || !$etablissement->logo) {
            abort(404);
        }

        $base = realpath(storage_path('app/public/logo'));
        $path = realpath($base . '/' . basename($etablissement->logo));

        if (!$path || !str_starts_with($path, $base . DIRECTORY_SEPARATOR) || !file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function index()
    {
        $etablissement = Etablissement::first();
        return view('admin.index', compact('etablissement'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'sigle' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'directeur' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'pays' => 'required|string|max:50',
            'ville' => 'nullable|string|max:50',
            'code_postal' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'jour_limite_paiement' => 'nullable|integer|min:1|max:28',
        ]);

        $etablissement = Etablissement::first() ?? new Etablissement();

        if ($request->hasFile('logo')) {
            $ancienLogo = $etablissement->logo ? storage_path('app/public/logo/' . basename($etablissement->logo)) : null;
            if ($ancienLogo && file_exists($ancienLogo)) {
                unlink($ancienLogo);
            }
            $logo     = $request->file('logo');
            $ext      = strtolower($logo->guessExtension() ?? 'jpg');
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $ext = 'jpg';
            }
            $filename = 'logo-' . bin2hex(random_bytes(8)) . '.' . $ext;
            $logo->storeAs('logo', $filename, 'public');
            $data['logo'] = $filename;
        }

        if ($etablissement->exists) {
            $etablissement->update($data);
        } else {
            Etablissement::create($data);
        }

        return redirect()->route('admin.index')
            ->with('succes', 'Informations établissement mises à jour avec succès.');
    }
}
