<?php
// php/strutture/get_strutture.php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    // Recupera tutte le strutture
    $stmt = $pdo->query("SELECT * FROM strutture ORDER BY nome ASC");
    $strutture = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Per ogni struttura, recuperiamo le stanze collegate
    foreach ($strutture as &$struttura) {
        $stmtStanze = $pdo->prepare("SELECT * FROM stanze WHERE struttura_id = :id ORDER BY nome_stanza ASC");
        $stmtStanze->execute([':id' => $struttura['id']]);
        $struttura['stanze'] = $stmtStanze->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data'    => $strutture
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
?>