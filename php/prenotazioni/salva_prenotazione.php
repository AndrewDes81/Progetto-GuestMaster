<?php
// php/salva_prenotazione.php
header('Content-Type: application/json');
require_once __DIR__ . '/config/db.php';

try {
    // Riceviamo i dati in formato JSON dal frontend JS
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception("Dati non validi o mancanti.");
    }

    $pdo->beginTransaction();

    // --------------------------------------------------------
    // 1. GESTIONE CLIENTE (Inserimento o recupero ID esistente)
    // --------------------------------------------------------
    $clienteId = null;

    if (!empty($input['codice_fiscale'])) {
        $stmtCheck = $pdo->prepare("SELECT id FROM clienti WHERE UPPER(codice_fiscale) = UPPER(:cf)");
        $stmtCheck->execute([':cf' => $input['codice_fiscale']]);
        $clienteEsistente = $stmtCheck->fetch();

        if ($clienteEsistente) {
            $clienteId = $clienteEsistente['id'];
        }
    }

    if (!$clienteId) {
        $sqlCliente = "INSERT INTO clienti (
            nome, cognome, codice_fiscale, sesso, data_nascita, 
            luogo_nascita, provincia_nascita, tipo_documento, numero_documento,
            rilasciato_da, data_scadenza, email, cellulare
        ) VALUES (
            :nome, :cognome, :cf, :sesso, :data_nascita,
            :luogo_nascita, :provincia_nascita, :tipo_doc, :num_doc,
            :rilasciato_da, :data_scadenza, :email, :cellulare
        ) RETURNING id";

        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->execute([
            ':nome'              => $input['nome'] ?? '',
            ':cognome'           => $input['cognome'] ?? '',
            ':cf'                => !empty($input['codice_fiscale']) ? strtoupper($input['codice_fiscale']) : null,
            ':sesso'             => $input['sesso'] ?? null,
            ':data_nascita'      => $input['data_nascita'] ?: null,
            ':luogo_nascita'     => $input['luogo_nascita'] ?? null,
            ':provincia_nascita' => $input['provincia_nascita'] ?? null,
            ':tipo_doc'          => $input['tipo_documento'] ?? null,
            ':num_doc'           => $input['numero_documento'] ?? null,
            ':rilasciato_da'     => $input['rilasciato_da'] ?? null,
            ':data_scadenza'     => $input['data_scadenza'] ?: null,
            ':email'             => $input['email'] ?? null,
            ':cellulare'         => $input['cellulare'] ?? null,
        ]);

        $clienteId = $stmtCliente->fetchColumn();
    }

    // --------------------------------------------------------
    // 2. GESTIONE PRENOTAZIONE
    // --------------------------------------------------------
    $sqlPrenotazione = "INSERT INTO prenotazioni (
        cliente_id, stanza_id, data_arrivo, data_partenza, numero_ospiti, prezzo_totale, note
    ) VALUES (
        :cliente_id, :stanza_id, :data_arrivo, :data_partenza, :numero_ospiti, :prezzo_totale, :note
    ) RETURNING id";

    $stmtPren = $pdo->prepare($sqlPrenotazione);
    $stmtPren->execute([
        ':cliente_id'    => $clienteId,
        ':stanza_id'     => $input['stanza_id'],
        ':data_arrivo'   => $input['data_arrivo'],
        ':data_partenza' => $input['data_partenza'],
        ':numero_ospiti' => $input['numero_ospiti'] ?? 1,
        ':prezzo_totale' => $input['prezzo_totale'] ?? 0.00,
        ':note'          => $input['note'] ?? ''
    ]);

    $prenotazioneId = $stmtPren->fetchColumn();

    // Confermiamo la transazione SQL
    $pdo->commit();

    echo json_encode([
        'success'         => true,
        'message'         => 'Prenotazione creata con successo!',
        'cliente_id'      => $clienteId,
        'prenotazione_id' => $prenotazioneId
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}