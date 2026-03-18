<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static $instances = [];

    public static function getConnection(?string $connectionName = null)
    {
        $configPath = __DIR__ . '/../config/database.php';
        $config = require $configPath;

        $defaultConnection = $_ENV['DB_CONNECTION'] ?? ($config['default'] ?? 'mysql');
        $connectionName = $connectionName ?? $defaultConnection;
        $connectionName = strtolower($connectionName);

        if (!isset($config['connections'][$connectionName])) {
            Response::error('Database Connection Error', "Connection '$connectionName' not configured", 500);
        }

        if (isset(self::$instances[$connectionName])) {
            return self::$instances[$connectionName];
        }

        $connection = $config['connections'][$connectionName];
        $driver = strtolower($connection['driver'] ?? 'mysql');

        $options = $connection['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $dsn = self::buildDsn($driver, $connection);
            $username = $connection['username'] ?? null;
            $password = $connection['password'] ?? null;
            self::$instances[$connectionName] = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            Response::error('Database Connection Error', $e->getMessage(), 500);
        }

        return self::$instances[$connectionName];
    }

    private static function buildDsn(string $driver, array $connection): string
    {
        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                $host = $connection['host'] ?? 'localhost';
                $port = $connection['port'] ?? 3306;
                $db = $connection['database'] ?? '';
                $charset = $connection['charset'] ?? 'utf8mb4';
                return "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            case 'pgsql':
                $host = $connection['host'] ?? 'localhost';
                $port = $connection['port'] ?? 5432;
                $db = $connection['database'] ?? '';
                return "pgsql:host=$host;port=$port;dbname=$db";
            case 'sqlite':
                $db = $connection['database'] ?? '';
                if ($db === '') {
                    Response::error('Database Connection Error', 'SQLite database not configured', 500);
                }
                $db = self::resolveSqlitePath($db);
                return "sqlite:$db";
            case 'sqlsrv':
                $host = $connection['host'] ?? 'localhost';
                $port = $connection['port'] ?? 1433;
                $db = $connection['database'] ?? '';
                return "sqlsrv:Server=$host,$port;Database=$db";
            case 'oracle':
            case 'oci':
                $host = $connection['host'] ?? 'localhost';
                $port = $connection['port'] ?? 1521;
                $db = $connection['database'] ?? '';
                $charset = $connection['charset'] ?? 'AL32UTF8';
                return "oci:dbname=//$host:$port/$db;charset=$charset";
            default:
                Response::error('Database Connection Error', "Driver '$driver' not supported", 500);
        }

        return '';
    }

    private static function resolveSqlitePath(string $path): string
    {
        if ($path === ':memory:') {
            return $path;
        }

        $isAbsolute = preg_match('/^[A-Za-z]:\\\\/', $path) === 1 || str_starts_with($path, '/');
        if ($isAbsolute) {
            return $path;
        }

        return __DIR__ . '/../' . ltrim($path, '/\\');
    }
}
