<?php

return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    'connections' => [
        // mysql (MySQL)
        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'database' => $_ENV['DB_DATABASE'] ?? 'thaluz',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        ],

        // mariadb (MariaDB)
        'mariadb' => [
            'driver' => 'mariadb',
            'host' => $_ENV['DB_MARIADB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_MARIADB_PORT'] ?? 3306,
            'database' => $_ENV['DB_MARIADB_DATABASE'] ?? 'thaluz',
            'username' => $_ENV['DB_MARIADB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_MARIADB_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_MARIADB_CHARSET'] ?? 'utf8mb4',
            'collation' => $_ENV['DB_MARIADB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        ],

        // pgsql (PostgreSQL)
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => $_ENV['DB_PGSQL_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PGSQL_PORT'] ?? 5432,
            'database' => $_ENV['DB_PGSQL_DATABASE'] ?? 'thaluz',
            'username' => $_ENV['DB_PGSQL_USERNAME'] ?? 'postgres',
            'password' => $_ENV['DB_PGSQL_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_PGSQL_CHARSET'] ?? 'utf8',
        ],

        // sqlite (SQLite)
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => $_ENV['DB_SQLITE_DATABASE'] ?? (__DIR__ . '/../database/database.sqlite'),
        ],

        // sqlsrv (SQL Server)
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => $_ENV['DB_SQLSRV_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_SQLSRV_PORT'] ?? 1433,
            'database' => $_ENV['DB_SQLSRV_DATABASE'] ?? 'thaluz',
            'username' => $_ENV['DB_SQLSRV_USERNAME'] ?? 'sa',
            'password' => $_ENV['DB_SQLSRV_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_SQLSRV_CHARSET'] ?? 'utf8',
        ],

        // oracle (Oracle)
        'oracle' => [
            'driver' => 'oracle',
            'host' => $_ENV['DB_ORACLE_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_ORACLE_PORT'] ?? 1521,
            'database' => $_ENV['DB_ORACLE_DATABASE'] ?? 'XE',
            'username' => $_ENV['DB_ORACLE_USERNAME'] ?? 'system',
            'password' => $_ENV['DB_ORACLE_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_ORACLE_CHARSET'] ?? 'WE8MSWIN1252',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'path' => 'database/migrations',
    ],

    'seeders' => [
        'path' => 'database/seeders',
    ],
];
