<?php
// php/ocr_scan.php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$imageBase64 = $input['image'] ?? null; // Immagine scattata da webcam o caricata

if (!$imageBase64) {
    echo json_encode(['success' => false, 'error' => 'Nessuna immagine fornita']);
    exit;
}

/*
 * ESEMPIO DI PROMPT PER IL MODELLO VISION:
 * "Analizza la seguente immagine di un documento d'identità italiano. 
 * Estrai i seguenti dati in formato JSON puro: 
 * nome, cognome, codice_fiscale, sesso (M/F), data_nascita (YYYY-MM-DD), 
 * luogo_nascita, provincia_nascita, tipo_documento, numero_documento, 
 * rilasciato_da, data_scadenza (YYYY-MM-DD)."
 */

// Qui faremo la cURL verso il provider AI selezionato.
// Per ora restituiamo la struttura che il frontend JS riceverà per la compilazione automatica:
echo json_encode([
    'success' => true,
    'data' => [
        'nome'              => 'MARIO',
        'cognome'           => 'ROSSI',
        'codice_fiscale'    => 'RSSMRA80A01H501U',
        'sesso'             => 'M',
        'data_nascita'      => '1980-01-01',
        'luogo_nascita'     => 'ROMA',
        'provincia_nascita' => 'RM',
        'tipo_documento'    => 'CARTA DI IDENTITA',
        'numero_documento'  => 'CA12345AA',
        'rilasciato_da'     => 'COMUNE DI ROMA',
        'data_scadenza'     => '2030-01-01'
    ]
]);