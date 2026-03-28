<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ParametresController extends Controller
{
    public function index()
    {
        $dossier = storage_path('app/sauvegardes');
        if (!is_dir($dossier)) @mkdir($dossier, 0755, true);

        $sauvegardes = [];
        foreach (glob($dossier . '/*.sql') ?: [] as $f) {
            $sauvegardes[] = [
                'nom'  => basename($f),
                'date' => date('d/m/Y H:i', filemtime($f)),
                'size' => round(filesize($f) / 1024, 1) . ' Ko',
            ];
        }
        usort($sauvegardes, fn($a, $b) => strcmp($b['nom'], $a['nom']));

        // Taille approximative via MySQL
        $dbSize = 'MySQL';
        try {
            $rows = DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024, 1) AS size
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [config('database.connections.mysql.database')]);
            $dbSize = ($rows[0]->size ?? 0) . ' Ko';
        } catch (\Exception $e) {}

        return view('parametres.index', compact('sauvegardes', 'dbSize'));
    }

    public function sauvegarder()
    {
        $dossier = storage_path('app/sauvegardes');
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);

        $nomFichier  = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $destination = "{$dossier}/{$nomFichier}";

        try {
            $this->dumpMysqlPhp($destination);
        } catch (\Exception $e) {
            return redirect()->route('parametres.index')
                ->withErrors(['db' => 'Sauvegarde échouée : ' . $e->getMessage()]);
        }

        return redirect()->route('parametres.index')
            ->with('succes', "Sauvegarde créée : {$nomFichier}");
    }

    public function restaurer(Request $request)
    {
        $request->validate(['fichier' => 'required|string']);

        $nom = basename($request->fichier);
        if (!preg_match('/^backup_[\d_-]+\.sql$/', $nom)) {
            return back()->withErrors(['fichier' => 'Nom de fichier invalide.']);
        }

        $source = storage_path("app/sauvegardes/{$nom}");
        if (!File::exists($source)) {
            return back()->withErrors(['fichier' => 'Fichier introuvable.']);
        }

        try {
            $this->restoreMysqlPhp($source);
        } catch (\Exception $e) {
            return back()->withErrors(['fichier' => 'Restauration échouée : ' . $e->getMessage()]);
        }

        return redirect()->route('parametres.index')
            ->with('succes', 'Base de données MySQL restaurée avec succès.');
    }

    public function supprimerSauvegarde(Request $request)
    {
        $nom     = basename($request->fichier ?? '');
        $fichier = storage_path("app/sauvegardes/{$nom}");
        if ($nom && File::exists($fichier)) {
            File::delete($fichier);
        }

        return redirect()->route('parametres.index')->with('succes', 'Sauvegarde supprimée.');
    }

    public function telecharger(string $fichier)
    {
        $nom = basename($fichier);

        if (!preg_match('/^backup_[\d_-]+\.sql$/', $nom)) {
            abort(403, 'Accès refusé.');
        }

        $base = realpath(storage_path('app/sauvegardes'));
        $path = realpath($base . '/' . $nom);

        if (!$path || !str_starts_with($path, $base . DIRECTORY_SEPARATOR) || !File::exists($path)) {
            return back()->withErrors(['fichier' => 'Fichier introuvable.']);
        }

        return response()->download($path);
    }

    /* ── Sauvegarde pure PHP (sans mysqldump) ───────────────────────────── */

    private function dumpMysqlPhp(string $destination): void
    {
        $host     = config('database.connections.mysql.host', '127.0.0.1');
        $port     = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $pdo = new \PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $lines   = [];
        $lines[] = "-- School ERP Backup";
        $lines[] = "-- Date      : " . now()->format('Y-m-d H:i:s');
        $lines[] = "-- Base      : {$database}";
        $lines[] = "";
        $lines[] = "SET FOREIGN_KEY_CHECKS=0;";
        $lines[] = "SET NAMES utf8mb4;";
        $lines[] = "";

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $lines[] = "-- Table: `{$table}`";
            $lines[] = "DROP TABLE IF EXISTS `{$table}`;";

            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $lines[] = $create['Create Table'] . ";";
            $lines[] = "";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $cols    = '`' . implode('`, `', array_keys($rows[0])) . '`';
                foreach ($rows as $row) {
                    $vals = array_map(function ($v) use ($pdo) {
                        return $v === null ? 'NULL' : $pdo->quote((string) $v);
                    }, array_values($row));
                    $lines[] = "INSERT INTO `{$table}` ({$cols}) VALUES (" . implode(', ', $vals) . ");";
                }
                $lines[] = "";
            }
        }

        $lines[] = "SET FOREIGN_KEY_CHECKS=1;";

        file_put_contents($destination, implode("\n", $lines));
    }

    private function restoreMysqlPhp(string $source): void
    {
        $host     = config('database.connections.mysql.host', '127.0.0.1');
        $port     = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $pdo = new \PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $sql        = file_get_contents($source);
        $statements = array_filter(
            array_map('trim', explode(";\n", $sql)),
            fn($s) => $s !== '' && !str_starts_with($s, '--')
        );

        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        foreach ($statements as $statement) {
            if (trim($statement) !== '') {
                $pdo->exec($statement);
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }

    public function changerMotDePasse(Request $request)
    {
        $request->validate([
            'mot_de_passe_actuel' => 'required',
            'nouveau_mot_de_passe' => 'required|min:8|confirmed',
        ], [
            'nouveau_mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas.',
            'nouveau_mot_de_passe.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->mot_de_passe_actuel, $user->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Mot de passe actuel incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->nouveau_mot_de_passe)]);

        return redirect()->route('parametres.index')->with('succes', 'Mot de passe modifié avec succès.');
    }
}
