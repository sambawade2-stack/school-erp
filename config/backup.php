<?php

return [
    /**
     * Configuration des sauvegardes
     */
    'backup' => [
        /**
         * Répertoire de stockage des backups
         * Chemin relatif à storage/app/
         */
        'directory' => 'backups',

        /**
         * Nombre maximum de sauvegardes à conserver
         */
        'max_backups' => 10,

        /**
         * Taille maximale d'une sauvegarde en MB
         */
        'max_size_mb' => 500,

        /**
         * Sauvegardes automatiques
         */
        'auto_backup' => [
            /**
             * Activer les sauvegardes automatiques
             */
            'enabled' => true,

            /**
             * Heure du backup quotidien (format 24h)
             */
            'daily_time' => '02:00',

            /**
             * Créer un backup avant chaque restauration
             */
            'backup_before_restore' => true,
        ],

        /**
         * Logging
         */
        'logging' => [
            /**
             * Logger les actions de backup
             */
            'enabled' => true,

            /**
             * Niveau de log
             */
            'level' => 'info',

            /**
             * Garder les logs de backup dans un canal séparé
             */
            'channel' => 'daily',
        ],

        /**
         * Sécurité
         */
        'security' => [
            /**
             * Vérifier l'intégrité des sauvegardes
             */
            'verify_integrity' => true,

            /**
             * Exiger l'authentification pour restaurer
             */
            'require_auth' => true,

            /**
             * Exiger une confirmation avant restauration
             */
            'require_confirmation' => true,
        ],
    ],
];
