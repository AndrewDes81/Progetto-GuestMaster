<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

try {
    $sql = "SELECT p.id, p.dal, p.al, p.stato, p.costo_totale, s.nome as nome_stanza 
            FROM prenotazioni p
            JOIN stanze s ON p.stanze_id = s.id
            WHERE p.clienti_id = :cliente_id
            ORDER BY p.dal DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cliente_id' => $cliente_id]);
    $storico = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($storico);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["errore" => $e->getMessage()]);
}
?>