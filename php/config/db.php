<?php
// php/config/db.php

$host = 'postgres-guestmaster'; // Nome del container database nel docker-compose
$db   = 'guestmaster';
$user = 'postgres';
$pass = 'Elettronica1';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}