<?php

header('Content-Type: application/json');

require_once '../config/json.php';

try {
    // Lettura dati form
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $codiceFiscale = trim($_POST['codice_fiscale'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cellulare = trim($_POST['cellulare'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $citta = trim($_POST['citta'] ?? '');
    $note = trim($_POST['note'] ?? '');

    // Validazioni
    if (empty($nome) || empty($cognome)) {
        throw new Exception('Nome e cognome sono obbligatori.');
    }

    if (empty($codiceFiscale)) {
        throw new Exception('Il codice fiscale è obbligatorio.');
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email non valida.');
    }

    // Lettura clienti
    $clienti = leggiJson('clienti.json');

    // Controllo duplicati basato SOLO sul Codice Fiscale
    foreach ($clienti as $cliente) {
        if (
            !empty($cliente['codice_fiscale']) &&
            strtoupper(trim($cliente['codice_fiscale'])) === strtoupper($codiceFiscale)
        ) {
            // Trovato! Restituisce i dati del cliente esistente senza duplicarlo
            echo json_encode([
                'success' => true,
                'cliente_esistente' => true,
                'cliente' => $cliente
            ]);

            exit;
        }
    }

    // Nuovo ID tramite funzione centralizzata se non esiste
    $nuovoId = prossimoId('ultimo_cliente_id');

    // Creazione cliente
    $nuovoCliente = [
        'id' => $nuovoId,
        'nome' => $nome,
        'cognome' => $cognome,
        'codice_fiscale' => strtoupper($codiceFiscale),
        'email' => $email,
        'cellulare' => $cellulare,
        'indirizzo' => $indirizzo,
        'citta' => $citta,
        'note' => $note,
        'data_creazione' => date('Y-m-d H:i:s')
    ];

    // Aggiunta all'array
    $clienti[] = $nuovoCliente;

    // Salvataggio
    salvaJson('clienti.json', $clienti);

    echo json_encode([
        'success' => true,
        'cliente_esistente' => false,
        'cliente' => $nuovoCliente
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}