<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php';

use Core\Database;

$db = Database::getConnection();

$action = $GLOBALS['MIGRATE_ACTION'] ?? 'migrate';
$steps = (int) ($GLOBALS['MIGRATE_STEPS'] ?? 1);

// 1. Criar a tabela de controle de migrations
$db->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($action === 'rollback') {
    $steps = $steps > 0 ? $steps : 1;
    $stmt = $db->prepare("SELECT id, migration FROM migrations ORDER BY id DESC LIMIT :limit");
    $stmt->bindValue(':limit', $steps, PDO::PARAM_INT);
    $stmt->execute();
    $toRollback = $stmt->fetchAll();

    if (empty($toRollback)) {
        echo "Nenhuma migration para rollback.\n";
        exit;
    }

    foreach ($toRollback as $row) {
        $file = __DIR__ . '/database/migrations/' . $row['migration'];
        if (!file_exists($file)) {
            echo "ERRO: migration nao encontrada: " . $row['migration'] . "\n";
            exit;
        }

        echo "Rollback migration: " . $row['migration'] . "...\n";
        $result = require $file;

        if (is_array($result) && isset($result['down'])) {
            $sql = $result['down'];
        } else {
            echo "ERRO: migration nao possui 'down' para rollback: " . $row['migration'] . "\n";
            exit;
        }

        try {
            $db->exec($sql);
            $del = $db->prepare("DELETE FROM migrations WHERE id = :id");
            $del->execute(['id' => $row['id']]);
            echo "Sucesso!\n";
        } catch (PDOException $e) {
            echo "ERRO ao executar rollback " . $row['migration'] . ": " . $e->getMessage() . "\n";
            exit;
        }
    }

    echo "Rollback concluido.\n";
    exit;
}

// 2. Buscar migrations ja executadas
$executedMigrations = $db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

// 3. Ler arquivos de migration
$files = glob(__DIR__ . '/database/migrations/*.php');
$toExecute = array_filter($files, function($file) use ($executedMigrations) {
    return !in_array(basename($file), $executedMigrations);
});

if (empty($toExecute)) {
    echo "Nenhuma migration nova encontrada.\n";
    exit;
}

foreach ($toExecute as $file) {
    echo "Executando migration: " . basename($file) . "...\n";

    $result = require $file;
    $sql = is_array($result) && isset($result['up']) ? $result['up'] : $result;

    try {
        $db->exec($sql);

        // Registrar como executada
        $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $stmt->execute(['migration' => basename($file)]);

        echo "Sucesso!\n";
    } catch (PDOException $e) {
        echo "ERRO ao executar " . basename($file) . ": " . $e->getMessage() . "\n";
        exit;
    }
}

echo "Todas as migrations foram processadas!\n";
