<?php
header('Content-Type: application/json');
require_once '../config/json.php';

try {
    // Leggiamo l'ID inviato via POST o GET
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

    if ($id <= 0) {
        throw new Exception('ID prenotazione non valido.');
    }

    $prenotazioni = leggiJson('prenotazioni.json');
    $nuovePrenotazioni = [];
    $trovata = false;

    foreach ($prenotazioni as $p) {
        if ($p['id'] == $id) {
            $trovata = true;
            // Salta questa prenotazione (di fatto la elimina)
            continue;
        }
        $nuovePrenotazioni[] = $p;
    }

    if (!$trovata) {
        throw new Exception('Prenotazione non trovata.');
    }

    // Salviamo il file aggiornato
    salvaJson('prenotazioni.json', $nuovePrenotazioni);

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione eliminata con successo.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}