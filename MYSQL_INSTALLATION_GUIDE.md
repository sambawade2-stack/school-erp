# 🚀 Guide Installation MySQL - School ERP

## ✅ État Actuel

Voici ce que j'ai préparé pour vous:

### ✓ Fichiers prêts:
- `MYSQL_SETUP.sql` - Script de configuration SQL
- `.env` - Modifié pour MySQL (DB_CONNECTION=mysql)
- Migrations Laravel - Prêtes à exécuter

### ⏳ À faire:
- Exécuter le script SQL
- Vérifier la connexion
- Migrer les données

---

## 📋 Installation Étape par Étape

### Étape 1️⃣ : Exécuter le Script de Configuration

**Sur votre terminal (Linux/Mac/Windows):**

```bash
# Aller dans le dossier du projet
cd /home/killer-ghost/Bureau/ecole/school-erp

# OU sur Windows :
cd C:\laragon\www\schoolerp

# Exécuter le script SQL
mysql -u root -p < MYSQL_SETUP.sql
# Entrez le mot de passe root MySQL : Gm@2026
```

**Vous devriez voir:**
```
✓ Base de données créée: schoolerp_db
✓ Utilisateur créé: schoolerp_user
✓ Permissions attribuées: GRANT...
```

---

### Étape 2️⃣ : Vérifier la Connexion Laravel

```bash
# Nettoyer le cache Laravel
php artisan config:clear

# Vérifier la connexion MySQL
php artisan tinker
>>> DB::connection('mysql')->getPdo()
>>> DB::table('information_schema.tables')->count()
>>> exit
```

**Résultat attendu:** Pas d'erreur, connexion OK ✓

---

### Étape 3️⃣ : Migrer vers MySQL

```bash
# Exécuter TOUTES les migrations
php artisan migrate:fresh --force

# Vérifier
php artisan migrate:status
```

**Vous devriez voir:** 39 migrations "Ran" ✓

---

### Étape 4️⃣ : Tester l'Application

```bash
# Démarrer Laravel
php artisan serve
# OU Laragon → Start All

# Aller à http://schoolerp.test
# Créer un compte admin via le wizard
```

---

## 🔧 Dépannage

### ❌ Erreur: "Access denied for user 'root'"

**Cause:** Mot de passe root MySQL incorrect

**Solution:**
```bash
mysql -u root -p
# Entrez votre mot de passe MySQL réel (pas le mot de passe système)
```

### ❌ Erreur: "Can't connect to local MySQL server"

**Cause:** MySQL n'est pas en cours d'exécution

**Solution Linux:**
```bash
sudo systemctl start mysql
sudo systemctl status mysql
```

**Solution Windows (Laragon):**
```
Laragon → MySQL → Activer (bouton vert)
```

### ❌ Erreur: "Unknown database 'schoolerp_db'"

**Cause:** Le script SQL n'a pas été exécuté

**Solution:** Réexécutez:
```bash
mysql -u root -p < MYSQL_SETUP.sql
```

### ❌ Erreur: "Access denied for user 'schoolerp_user'"

**Cause:** Utilisateur MySQL non créé

**Solution:** Vérifiez avec :
```bash
mysql -u root -p
mysql> SELECT user FROM mysql.user WHERE user='schoolerp_user';
```

Si vide, réexécutez le script.

---

## 🔐 Détails de Configuration

### Fichier `.env` (Actuel)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolerp_db
DB_USERNAME=schoolerp_user
DB_PASSWORD=SchoolERP@2026!Secure
```

### Credentials MySQL

| Paramètre | Valeur |
|-----------|--------|
| **Host** | 127.0.0.1 (ou localhost) |
| **Port** | 3306 |
| **Database** | schoolerp_db |
| **User** | schoolerp_user |
| **Password** | SchoolERP@2026!Secure |

---

## 📊 Vérification Post-Installation

### Vérifier MySQL directement

```bash
# Accéder à MySQL
mysql -u schoolerp_user -p
# Mot de passe: SchoolERP@2026!Secure

# Dans MySQL:
mysql> USE schoolerp_db;
mysql> SHOW TABLES;
mysql> SELECT COUNT(*) FROM users;
mysql> EXIT;
```

### Vérifier Laravel

```bash
# Via tinker
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('etudiants')->count()
>>> exit

# Via artisan
php artisan migrate:status
```

### Vérifier l'Application

1. Aller à `http://schoolerp.test`
2. Voir le wizard
3. Créer un compte admin
4. Se connecter
5. Voir le dashboard

---

## 🚀 Commandes Récap

```bash
# 1. Configurer MySQL
mysql -u root -p < MYSQL_SETUP.sql

# 2. Nettoyer cache Laravel
php artisan config:clear

# 3. Migrer les données
php artisan migrate:fresh --force

# 4. Vérifier
php artisan migrate:status

# 5. Démarrer
php artisan serve
```

---

## 📁 Fichiers Modifiés

```
.env                         ← DB_CONNECTION=mysql (MODIFIÉ)
MYSQL_SETUP.sql             ← Script de configuration (NOUVEAU)
MYSQL_INSTALLATION_GUIDE.md ← Ce fichier (NOUVEAU)
```

---

## ✅ Checklist Final

- [ ] `mysql -u root -p < MYSQL_SETUP.sql` exécuté
- [ ] Pas d'erreurs SQL
- [ ] `php artisan config:clear` exécuté
- [ ] `php artisan migrate:fresh --force` exécuté
- [ ] `php artisan migrate:status` affiche 39 migrations "Ran"
- [ ] `php artisan tinker` fonctionne
- [ ] `http://schoolerp.test` affiche le wizard
- [ ] Compte admin créé
- [ ] Dashboard affiché

---

## 💡 Tips

### Si vous voulez changer le mot de passe utilisateur:

```sql
ALTER USER 'schoolerp_user'@'localhost' IDENTIFIED BY 'NewPassword123!';
FLUSH PRIVILEGES;
```

### Si vous voulez utiliser PhpMyAdmin:

```bash
# Accédez à Laragon → PhpMyAdmin
# User: root
# Password: Gm@2026
# Vous verrez la BD schoolerp_db
```

### Sauvegarde MySQL:

```bash
# Exporter la base
mysqldump -u schoolerp_user -p schoolerp_db > backup.sql

# Importer une sauvegarde
mysql -u schoolerp_user -p schoolerp_db < backup.sql
```

---

## 📞 Besoin d'aide?

Si vous rencontrez des problèmes:
1. Partagez le message d'erreur exact
2. Dites-moi votre OS (Windows/Linux/Mac)
3. Dites-moi où MySQL tourne (Laragon/Local/Serveur)

Je pourrai alors adapter les solutions! 🚀

---

**Date:** Mars 2026
**Status:** Prêt pour MySQL
