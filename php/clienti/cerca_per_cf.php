<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$cf = strtoupper(trim($_GET['cf'] ?? ''));

if (empty($cf)) {
    echo json_encode(['success' => false, 'message' => 'Codice fiscale non valido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM clienti WHERE UPPER(codice_fiscale) = :cf LIMIT 1");
    $stmt->execute(['cf' => $cf]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        echo json_encode([
            'success' => true,
            'trovato' => true,
            'cliente' => $cliente
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'trovato' => false
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>