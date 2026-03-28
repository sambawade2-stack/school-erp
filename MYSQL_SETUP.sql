-- ============================================================================
-- SCHOOL ERP - Configuration MySQL
-- ============================================================================
-- Exécutez ce fichier avec : mysql -u root -p < MYSQL_SETUP.sql
-- ============================================================================

-- 1. Créer la base de données
CREATE DATABASE IF NOT EXISTS schoolerp_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- 2. Créer l'utilisateur dédié (sécurisé - pas root)
CREATE USER IF NOT EXISTS 'schoolerp_user'@'localhost'
    IDENTIFIED BY 'SchoolERP@2026!Secure';

-- 3. Donner les permissions (seulement sur la BD schoolerp_db)
GRANT ALL PRIVILEGES ON schoolerp_db.*
    TO 'schoolerp_user'@'localhost';

-- 4. Appliquer les permissions
FLUSH PRIVILEGES;

-- 5. Vérifier la configuration
SELECT '=====================================' AS '';
SELECT 'Configuration MySQL complétée!' AS '';
SELECT '=====================================' AS '';
SELECT @@version AS 'MySQL Version';
SELECT CONCAT('User: ', USER()) AS '';
SELECT '' AS '';
SELECT '✓ Base de données créée:' AS '';
SHOW DATABASES LIKE 'schoolerp%';
SELECT '' AS '';
SELECT '✓ Utilisateur créé:' AS '';
SELECT user, host FROM mysql.user WHERE user='schoolerp_user';
SELECT '' AS '';
SELECT '✓ Permissions attribuées:' AS '';
SHOW GRANTS FOR 'schoolerp_user'@'localhost';
SELECT '' AS '';
SELECT 'Prochaines étapes:' AS '';
SELECT '1. Vérifier que schoolerp_db existe' AS '';
SELECT '2. Exécuter: php artisan migrate:fresh --force' AS '';
SELECT '3. Tester: php artisan tinker' AS '';
SELECT '=====================================' AS '';
