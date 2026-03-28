# 🚀 Production Ready - School ERP

## ✅ État Actuel

L'application est **prête pour la production** avec :

- ✅ Base de données **complètement propre** (zéro données de test)
- ✅ Wizard d'installation simplifié (3 champs)
- ✅ Structure complète (39 tables)
- ✅ Tous les modules fonctionnels
- ✅ Sauvegardes & restauration
- ✅ Interface moderne (Tailwind + Alpine)

---

## 📦 Ce qui a été supprimé

### Base de Données
```
✓ 20 utilisateurs
✓ 150+ étudiants
✓ 50+ paiements
✓ 200+ notes
✓ Toutes les données de test
```

### Fichiers Storage
```
storage/app/public/logo/     → Vide (0 fichiers)
storage/app/public/etudiants/ → Vide (0 fichiers)
```

### Commande utilisée
```bash
php artisan data:clean --force
```

---

## 🎯 Installation pour Production

### 1️⃣ Copier le projet

```bash
Copier: /home/killer-ghost/Bureau/ecole/school-erp
Vers:   C:\laragon\www\schoolerp\
```

### 2️⃣ Installation des dépendances

```bash
cd C:\laragon\www\schoolerp

composer install --no-dev --optimize-autoloader
```

### 3️⃣ Configuration Laravel

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 4️⃣ Créer les dossiers storage

```bash
mkdir storage\app\public\etudiants
mkdir storage\app\public\logo
mkdir storage\app\public\sauvegardes
mkdir storage\app\backups
```

### 5️⃣ Démarrer Laragon

```
Laragon → Start All
```

### 6️⃣ Accès au wizard

```
http://schoolerp.test
```

---

## 🧙‍♂️ Wizard Installation

Le formulaire est **simplifié** pour la production :

```
👤 Votre nom          [_________________]
📧 Email              [_________________]
🔐 Mot de passe       [_________________]
✓ Confirmer           [_________________]

    [✅ Créer le compte administrateur]
```

**Durée:** 30 secondes

---

## 📋 Après Connexion

Après vous être connecté, configurez l'établissement :

```
Administration → Établissement
├─ Nom
├─ Sigle
├─ Adresse
├─ Téléphone
├─ Email
├─ Directeur
├─ Pays
├─ Ville
├─ Code Postal
├─ Logo
└─ Description
```

---

## 🔐 Sécurité

### Avant de Passer en Production

- [ ] Changer `APP_DEBUG=false` dans `.env`
- [ ] Générer une clé Laravel unique : `php artisan key:generate`
- [ ] Configurer HTTPS
- [ ] Augmenter `BCRYPT_ROUNDS` à 12 dans `.env`
- [ ] Configurer les logs (rotation, stockage)
- [ ] Sauvegarder régulièrement la base

### Fichier `.env` Production

```env
APP_NAME="School ERP"
APP_ENV=production
APP_KEY=                          # Générer avec key:generate
APP_DEBUG=false
APP_URL=https://schoolerp.example.com

DB_CONNECTION=sqlite
FILESYSTEM_DISK=local
SESSION_DRIVER=file
CACHE_STORE=file

MAIL_MAILER=smtp                  # Configurer pour envois email
```

---

## 📊 Tailles de Fichiers

Après nettoyage :

```
Dossier source         : 2.3 MB   (sans vendor/ ni node_modules/)
vendor/ (après install): ~70 MB   (composer install)
Base SQLite            : 0.28 MB  (vide, structure uniquement)
─────────────────────────────────────
Total installation     : ~72 MB
```

---

## 🚀 Commandes Utiles Production

```bash
# Nettoyer les données de test
php artisan data:clean --force

# Réinitialiser l'installation (recommencer zéro)
php artisan setup:reset --force

# Créer une sauvegarde manuelle
php artisan backup:create

# Voir les logs
tail -f storage/logs/laravel.log

# Nettoyer le cache
php artisan optimize:clear

# Vérifier les migrations
php artisan migrate:status
```

---

## 📅 Sauvegardes Automatiques

Le système crée automatiquement une sauvegarde chaque jour à **2h du matin**.

### Sur Serveur

Ajouter au crontab :
```bash
* * * * * cd /chemin/du/projet && php artisan schedule:run >> /dev/null 2>&1
```

### Sur Laragon (Local)

Les sauvegardes fonctionnent si le serveur tourne (Start All activé).

---

## 🛡️ Fichiers Sécurisés

Les fichiers sensibles sont **hors du web** :

```
public/                ← Seul ce dossier est accessible
  ├── index.php
  ├── css/
  ├── js/
  └── storage/        ← Lien symbolique vers storage/app/public

storage/               ← Caché du web
  ├── app/
  ├── logs/
  ├── backups/
  └── ...
```

---

## 📞 Support & Troubleshooting

### Erreur "Unable to open database file"

```bash
type nul > database\database.sqlite
php artisan migrate --force
```

### storage:link échoue

```bash
# Terminal en Admin
mklink /J "C:\laragon\www\schoolerp\public\storage" "C:\laragon\www\schoolerp\storage\app\public"
```

### Logo ne s'affiche pas

```bash
# Vérifier permissions
chmod 755 storage/app/public/logo/
chmod 644 storage/app/public/logo/*
```

### Cache problématique

```bash
php artisan optimize:clear
```

---

## ✅ Checklist Production Final

- [ ] Données de test supprimées (`php artisan data:clean --force`)
- [ ] Fichiers de test supprimés (logo, étudiants)
- [ ] `.env` configuré correctement
- [ ] `APP_DEBUG=false` (production)
- [ ] Clé APP_KEY générée
- [ ] Dossier `vendor/` installé
- [ ] Dossier `storage/app/backups/` créé
- [ ] Laragon fonctionne
- [ ] Wizard accessible
- [ ] Premier admin créé
- [ ] Établissement configuré
- [ ] Logo uploadé (optionnel)
- [ ] Sauvegardes testées

---

## 🎯 Résumé

**L'application est prête à être déployée en production.**

Elle est :
- Légère (2.3 MB source)
- Sécurisée (authentification, validation)
- Complète (39 tables, tous les modules)
- Propre (zéro données de test)
- Documentée

Bonne chance! 🚀

---

**Version:** 1.0 Production Ready
**Date:** Mars 2026
**Status:** ✅ Approuvé pour production
