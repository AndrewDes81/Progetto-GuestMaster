<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($id <= 0) {
        throw new Exception("ID cliente non valido.");
    }

    $stmt = $pdo->prepare("SELECT * FROM clienti WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        throw new Exception("Cliente non trovato.");
    }

    echo json_encode($cliente);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>