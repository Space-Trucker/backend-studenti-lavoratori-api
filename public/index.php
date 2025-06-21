<?php
// public/index.php

// Includi la connessione database
$pdo = require_once __DIR__ . '/../config/database.php';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Database Connection</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🚀 Test Connessione Database</h1>
    
    <?php
    try {
        // Test connessione
        $stmt = $pdo->query("SELECT VERSION() as version, NOW() as current_time");
        $result = $stmt->fetch();
        
        echo "<div class='success'>✅ Connessione database riuscita!</div>";
        echo "<div class='info'>";
        echo "<strong>MySQL Version:</strong> " . $result['version'] . "<br>";
        echo "<strong>Server Time:</strong> " . $result['current_time'] . "<br>";
        echo "<strong>Host:</strong> " . (getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost') . "<br>";
        echo "<strong>Database:</strong> " . (getenv('MYSQLDATABASE') ?: getenv('DB_DATABASE') ?: 'N/A');
        echo "</div>";
        
        // Test creazione tabella semplice
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Inserisci dato di test
        $stmt = $pdo->prepare("INSERT INTO test_table (name) VALUES (?)");
        $stmt->execute(['Test Connection ' . date('Y-m-d H:i:s')]);
        
        // Mostra ultimi 5 record
        $stmt = $pdo->query("SELECT * FROM test_table ORDER BY id DESC LIMIT 5");
        $records = $stmt->fetchAll();
        
        echo "<h3>📋 Ultimi 5 record di test:</h3>";
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Created At</th></tr>";
        foreach($records as $record) {
            echo "<tr>";
            echo "<td>" . $record['id'] . "</td>";
            echo "<td>" . $record['name'] . "</td>";
            echo "<td>" . $record['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch(Exception $e) {
        echo "<div class='error'>❌ Errore: " . $e->getMessage() . "</div>";
    }
    ?>
    
    <hr>
    <small>🌐 Ambiente: <?php echo getenv('MYSQLHOST') ? 'Railway (Produzione)' : 'Locale (Sviluppo)'; ?></small>
</body>
</html>