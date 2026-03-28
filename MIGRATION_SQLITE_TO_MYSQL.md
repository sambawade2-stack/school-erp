# 🔄 Migration SQLite → MySQL

## 📋 Guide Complet

Je vais vous guider pour migrer de SQLite à MySQL de manière sécurisée.

---

## 🚨 Avant de Commencer

**IMPORTANT:** Vous m'avez fourni le mot de passe root MySQL. Pour la **sécurité**, nous allons :

1. ✅ Créer une **base de données dédiée** pour School ERP
2. ✅ Créer un **utilisateur spécifique** (pas root)
3. ✅ Utiliser cet utilisateur dans `.env`
4. ✅ Ne JAMAIS garder le mot de passe root

---

## 🎯 Étapes de Migration

### Étape 1️⃣ : Créer la Base de Données MySQL

Sur votre serveur MySQL (Windows Laragon ou Linux), exécutez :

```sql
-- Créer la base de données
CREATE DATABASE schoolerp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Créer un utilisateur dédié (SÉCURISÉ)
CREATE USER 'schoolerp_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';

-- Donner les permissions
GRANT ALL PRIVILEGES ON schoolerp_db.* TO 'schoolerp_user'@'localhost';
FLUSH PRIVILEGES;

-- Vérifier
SHOW DATABASES;
```

**Remplacez `SecurePassword123!` par un mot de passe fort.**

---

### Étape 2️⃣ : Modifier le Fichier `.env`

Changez ces lignes dans `.env` :

```env
# AVANT (SQLite)
DB_CONNECTION=sqlite

# APRÈS (MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=schoolerp_db
DB_USERNAME=schoolerp_user
DB_PASSWORD=SecurePassword123!
```

**Valeurs à adapter:**
- `DB_HOST`: `localhost` (Laragon local) ou `192.168.x.x` (serveur réseau)
- `DB_PORT`: `3306` (défaut MySQL) ou `3307` (Laragon sur Windows)
- `DB_DATABASE`: `schoolerp_db`
- `DB_USERNAME`: `schoolerp_user`
- `DB_PASSWORD`: Le mot de passe que vous avez défini

---

### Étape 3️⃣ : Mettre à Jour les Dépendances

```bash
# Installer le driver MySQL pour PHP
composer require --no-dev --optimize-autoloader

# Ou si déjà dans composer.json, juste mettre à jour
composer update --no-dev
```

**PHP doit avoir l'extension `pdo_mysql` activée.**

Vérifier dans Laragon :
```
Laragon → PHP → php.ini
```

Chercher et décommenter :
```ini
extension=pdo_mysql
```

---

### Étape 4️⃣ : Migrer les Données

```bash
# Nettoyer le cache
php artisan config:clear
php artisan cache:clear

# Exécuter les migrations
php artisan migrate:fresh --force

# (Optionnel) Charger les données de test
php artisan db:seed
```

**⚠️ `migrate:fresh` supprime les données - c'est normal, nous avons déjà nettoyé SQLite.**

---

### Étape 5️⃣ : Vérifier la Connexion

```bash
# Test simple
php artisan tinker
>>> DB::connection('mysql')->getPdo()
>>> exit

# Ou vérifier les tables
php artisan migrate:status
```

Vous devez voir les 39 migrations "Ran".

---

## 🔐 Sécurité MySQL

### Bonnes Pratiques

✅ **Ne JAMAIS utiliser root en production**
✅ **Utiliser un utilisateur dédié avec permissions minimales**
✅ **Mot de passe fort (15+ caractères)**
✅ **Restreindre à localhost si possible**
✅ **Sauvegardes régulières**

### Exemple de Création Sécurisée

```sql
-- Créer un utilisateur avec accès limité
CREATE USER 'schoolerp'@'localhost' IDENTIFIED BY 'P@ssw0rd!Secure2024';
GRANT SELECT, INSERT, UPDATE, DELETE ON schoolerp_db.* TO 'schoolerp'@'localhost';
FLUSH PRIVILEGES;

-- Jamais ceci en production :
-- GRANT ALL PRIVILEGES ON schoolerp_db.* TO 'schoolerp'@'%';
```

---

## 📊 Configuration Laragon (Windows)

Si vous utilisez **Laragon**, MySQL tourne sur le **port 3306** par défaut :

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolerp_db
DB_USERNAME=schoolerp_user
DB_PASSWORD=VotreMotDePasse
```

**Vérifier que MySQL est activé:**
```
Laragon → MySQL → Actif (bouton vert)
```

---

## 🐧 Configuration Linux

Si vous utilisez **Linux**, déterminez où tourne MySQL :

```bash
# Vérifier le service
sudo systemctl status mysql

# Démarrer MySQL si arrêté
sudo systemctl start mysql

# Se connecter
mysql -u root -p
# Entrez le mot de passe root
```

**Puis exécutez les commandes SQL de l'Étape 1.**

---

## ✅ Vérifications Post-Migration

```bash
# 1. Vérifier la connexion
php artisan tinker
>>> DB::table('users')->count()
>>> exit

# 2. Vérifier les tables
php artisan migrate:status

# 3. Vérifier les performances
# (MySQL est plus rapide que SQLite pour les gros volumes)

# 4. Tester l'application
# Aller à http://schoolerp.test
# Créer un compte admin via wizard
# Tester les fonctionnalités
```

---

## 🚀 Commandes Rapides Résumé

```bash
# 1. Modifier .env (MySQL)
nano .env

# 2. Nettoyer le cache
php artisan config:clear

# 3. Migrer vers MySQL
php artisan migrate:fresh --force

# 4. Vérifier
php artisan migrate:status

# 5. Démarrer
php artisan serve
# ou Laragon → Start All
```

---

## ⚠️ En Cas de Problème

### Erreur : "SQLSTATE[HY000]: General error: 1030"

```
Cause: MySQL n'est pas accessible
Solution: Vérifier que MySQL tourne et les credentials sont corrects
```

### Erreur : "No such file or directory" SQLite

```
Cause: Toujours pointer vers SQLite dans .env
Solution: Vérifier DB_CONNECTION=mysql dans .env
```

### Erreur : "Access denied for user"

```
Cause: Mauvais mot de passe ou permissions
Solution: Vérifier les credentials dans .env et les permissions MySQL
```

---

## 📋 Checklist Migration

- [ ] Créer la base de données MySQL (`schoolerp_db`)
- [ ] Créer l'utilisateur MySQL (`schoolerp_user`)
- [ ] Modifier `.env` avec les credentials MySQL
- [ ] Modifier `DB_CONNECTION` de `sqlite` à `mysql`
- [ ] Vérifier `extension=pdo_mysql` dans php.ini
- [ ] Exécuter `php artisan migrate:fresh --force`
- [ ] Vérifier avec `php artisan migrate:status`
- [ ] Tester la connexion avec `php artisan tinker`
- [ ] Accéder à `http://schoolerp.test`
- [ ] Créer un compte admin via le wizard
- [ ] Tester un module (étudiants, paiements, etc.)

---

## 🎯 Résultat Final

Après migration :

```
✅ MySQL configuré
✅ 39 tables créées
✅ Zéro données de test
✅ Utilisateur dédié (pas root)
✅ Application fonctionnelle
✅ Prêt pour la production
```

---

## 📞 Problèmes?

Dites-moi :
1. Où tourne MySQL? (Laragon/Windows, Linux local, serveur réseau)
2. Quel port MySQL? (3306, 3307, autre)
3. Quelle erreur exacte recevez-vous?

Je pourrai alors adapter les instructions! 🚀

---

**Date:** Mars 2026
**Status:** Migration SQLite → MySQL
