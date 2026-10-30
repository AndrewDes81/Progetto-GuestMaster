<?php
$host     = "db";
$port     = "5432";
$dbname   = "gestionale_db";
$user     = "postgres";
$password = "miamiapassword";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Errore di connessione a PostgreSQL: " . $e->getMessage());
}
?>