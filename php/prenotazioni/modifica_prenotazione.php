<?php
header('Content-Type: application/json');
require_once '../config/json.php';

try {
    // Supporta sia i dati FormData (POST) che un eventuale payload JSON
    $input = $_POST;
    if (empty($input)) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    $id = isset($input['id']) ? (int)$input['id'] : 0;
    if ($id <= 0) {
        throw new Exception('ID prenotazione non valido.');
    }

    $prenotazioni = leggiJson('prenotazioni.json');
    $trovata = false;

    foreach ($prenotazioni as &$p) {
        if ((int)$p['id'] === $id) {
            $trovata = true;
            
            // Aggiorniamo i campi modificabili
            $p['dal'] = $input['dal'] ?? $p['dal'];
            $p['al'] = $input['al'] ?? $p['al'];
            $p['costo_totale'] = $input['costo_totale'] ?? ($input['costoTotale'] ?? $p['costo_totale']);
            $p['caparra'] = $input['caparra'] ?? $p['caparra'];
            $p['saldo'] = calcolaSaldo((float)$p['costo_totale'], (float)$p['caparra']);
            $p['stato'] = $input['stato'] ?? $p['stato'];
            $p['note'] = $input['note'] ?? $p['note'];
            
            break;
        }
    }
    unset($p);

    if (!$trovata) {
        throw new Exception('Prenotazione non trovata.');
    }

    salvaJson('prenotazioni.json', $prenotazioni);

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione aggiornata con successo.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}