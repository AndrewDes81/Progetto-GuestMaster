<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    // Usiamo i nomi delle colonne reali dello schema PostgreSQL
    $sql = "SELECT id, cliente_id, stanza_id, data_arrivo as dal, data_partenza as al, numero_ospiti, prezzo_totale as costo_totale, caparra, stato, note 
            FROM prenotazioni 
            ORDER BY data_arrivo DESC";
            
    $stmt = $pdo->query($sql);
    $prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($prenotazioni);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["errore" => $e->getMessage()]);
}
?>