<?php
// config/database.php

// Funzione per caricare .env in locale
function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Carica .env se esiste (solo in locale)
loadEnv(__DIR__ . '/../.env');

// Configurazione database
// In produzione (Railway) usa le variabili automatiche
// In locale usa le variabili da .env
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
$dbname = getenv('MYSQLDATABASE') ?: getenv('DB_DATABASE') ?: 'test';
$username = getenv('MYSQLUSER') ?: getenv('DB_USERNAME') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';

// Connessione PDO
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    // echo "✅ Connessione database riuscita!";
    
} catch(PDOException $e) {
    die("❌ Errore connessione database: " . $e->getMessage());
}

return $pdo;
?>
