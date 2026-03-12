<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php';

use Core\Database;

$db = Database::getConnection();

// 1. Criar a tabela de controle de migrations
$db->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 2. Buscar migrations já executadas
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
    
    $sql = require_once $file;
    
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
