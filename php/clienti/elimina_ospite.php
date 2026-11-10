<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    $input = $_POST;
    if (empty($input)) {
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $input = json_decode($json, true) ?? [];
        }
    }

    $id = isset($input['id']) ? intval($input['id']) : 0;

    if ($id <= 0) {
        throw new Exception("ID ospite non valido.");
    }

    // Nota: se un cliente ha prenotazioni collegate con ON DELETE RESTRICT, 
    // PostgreSQL genererà un'eccezione se provi a eliminarlo direttamente.
    $stmt = $pdo->prepare("DELETE FROM clienti WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Ospite eliminato con successo!'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    // Gestione specifica se ci sono prenotazioni collegate (Foreign Key constraint)
    if ($e->getCode() == '23503') {
        echo json_encode([
            'success' => false, 
            'error' => 'Impossibile eliminare l\'ospite: è associato a una o più prenotazioni.'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>