<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'raosmqxe_magazzino');
define('DB_USER', 'raosmqxe_magazzino');
define('DB_PASS', 'g4!go,qc~5KI');

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}
?>