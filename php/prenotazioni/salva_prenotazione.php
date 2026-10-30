<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/json.php'; // Adatta il percorso se necessario in base a dove posizioni il file

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metodo non consentito.');
    }

    $cliente_id   = $_POST['cliente_id'] ?? null;
    $dal          = $_POST['dal'] ?? '';
    $al           = $_POST['al'] ?? '';
    $costo_totale = floatval($_POST['costo_totale'] ?? 0);
    $caparra      = floatval($_POST['caparra'] ?? 0);
    $stato        = $_POST['stato'] ?? 'PRENOTATA';
    $note         = trim($_POST['note'] ?? '');

    // Validazioni base
    if (empty($dal) || empty($al)) {
        throw new Exception('Le date di arrivo e partenza sono obbligatorie.');
    }

    if ($dal > $al) {
        throw new Exception('La data di arrivo non può essere successiva alla data di partenza.');
    }

    // Controllo sovrapposizione date (usando la funzione di json.php)
    if (periodoOccupato($dal, $al)) {
        throw new Exception('Il periodo selezionato è già occupato da un\'altra prenotazione.');
    }

    // Calcolo saldo
    $saldo = calcolaSaldo($costo_totale, $caparra);

    // Generazione ID univoco per la prenotazione
    $nuovo_id = prossimoId('ultima_prenotazione_id');

    // Lettura prenotazioni esistenti, aggiunta della nuova e salvataggio
    $prenotazioni = leggiJson('prenotazioni.json');

    $nuovaPrenotazione = [
        'id'           => $nuovo_id,
        'cliente_id'   => (int)$cliente_id,
        'dal'          => $dal,
        'al'           => $al,
        'costo_totale' => $costo_totale,
        'caparra'      => $caparra,
        'saldo'        => $saldo,
        'stato'        => $stato,
        'note'         => $note
    ];

    $prenotazioni[] = $nuovaPrenotazione;
    salvaJson('prenotazioni.json', $prenotazioni);

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione salvata con successo!',
        'id'      => $nuovo_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e.getMessage()
    ]);
}