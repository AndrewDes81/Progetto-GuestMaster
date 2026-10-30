<?php
header('Content-Type: application/json');
require_once '../config/json.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception('ID prenotazione non valido.');
    }

    $prenotazioni = leggiJson('prenotazioni.json');
    $prenotazioneTrovata = null;
    foreach ($prenotazioni as $p) {
        if ($p['id'] == $id) {
            $prenotazioneTrovata = $p;
            break;
        }
    }

    if (!$prenotazioneTrovata) {
        throw new Exception('Prenotazione non trovata.');
    }

    // Cerchiamo i dati del cliente associato
    $clienti = leggiJson('clienti.json');
    $clienteTrovato = null;
    foreach ($clienti as $c) {
        if ($c['id'] == $prenotazioneTrovata['cliente_id']) {
            $clienteTrovato = $c;
            break;
        }
    }

    echo json_encode([
        'success' => true,
        'prenotazione' => $prenotazioneTrovata,
        'cliente' => $clienteTrovato
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}