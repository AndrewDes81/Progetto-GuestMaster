<?php
// php/salva_prenotazione.php
header('Content-Type: application/json');

// Includiamo la connessione al database in modo assoluto
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    // Supporto sia per FormData ($_POST) che per richieste JSON pure
    $input = $_POST;
    if (empty($input)) {
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $input = json_decode($json, true) ?? [];
        }
    }

    if (empty($input)) {
        throw new Exception("Dati non validi o mancanti.");
    }

    $pdo->beginTransaction();

   // --------------------------------------------------------
    // 1. GESTIONE CLIENTE (Uso l'ID esistente o lo creo se nuovo)
    // --------------------------------------------------------
    $clienteId = !empty($input['cliente_id']) ? intval($input['cliente_id']) : null;

    if (!$clienteId && !empty($input['codice_fiscale'])) {
        $stmtCheck = $pdo->prepare("SELECT id FROM clienti WHERE UPPER(codice_fiscale) = UPPER(:cf)");
        $stmtCheck->execute([':cf' => trim($input['codice_fiscale'])]);
        $clienteEsistente = $stmtCheck->fetch();

        if ($clienteEsistente) {
            $clienteId = $clienteEsistente['id'];
        }
    }

    // Se non abbiamo ancora un ID cliente valido, procediamo con l'inserimento del nuovo cliente
    if (!$clienteId) {
        $sqlCliente = "INSERT INTO clienti (
            nome, cognome, codice_fiscale, sesso, data_nascita, 
            luogo_nascita, provincia_nascita, stato_nascita, tipo_documento, numero_documento,
            rilasciato_da, data_emissione, data_scadenza, email, cellulare,
            indirizzo, citta, provincia_residenza, stato_residenza
        ) VALUES (
            :nome, :cognome, :cf, :sesso, :data_nascita,
            :luogo_nascita, :provincia_nascita, :stato_nascita, :tipo_doc, :num_doc,
            :rilasciato_da, :data_emissione, :data_scadenza, :email, :cellulare,
            :indirizzo, :citta, :provincia_residenza, :stato_residenza
        ) RETURNING id";

        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->execute([
            ':nome'                => $input['nome'] ?? '',
            ':cognome'             => $input['cognome'] ?? '',
            ':cf'                  => !empty($input['codice_fiscale']) ? strtoupper(trim($input['codice_fiscale'])) : null,
            ':sesso'               => $input['sesso'] ?? null,
            ':data_nascita'        => !empty($input['data_nascita']) ? $input['data_nascita'] : null,
            ':luogo_nascita'       => $input['luogo_nascita'] ?? null,
            ':provincia_nascita'   => $input['provincia_nascita'] ?? null,
            ':stato_nascita'       => $input['stato_nascita'] ?? 'ITALIA',
            ':tipo_doc'            => $input['tipo_documento'] ?? null,
            ':num_doc'             => $input['numero_documento'] ?? null,
            ':rilasciato_da'       => $input['rilasciato_da'] ?? null,
            ':data_emissione'      => !empty($input['data_emissione']) ? $input['data_emissione'] : null,
            ':data_scadenza'       => !empty($input['data_scadenza']) ? $input['data_scadenza'] : null,
            ':email'               => $input['email'] ?? null,
            ':cellulare'           => $input['cellulare'] ?? null,
            ':indirizzo'           => $input['indirizzo'] ?? null,
            ':citta'               => $input['citta'] ?? null,
            ':provincia_residenza' => $input['provincia_residenza'] ?? null,
            ':stato_residenza'     => $input['stato_residenza'] ?? 'ITALIA',
        ]);

        $clienteId = $stmtCliente->fetchColumn();
    }

    // --------------------------------------------------------
    // 2. GESTIONE PRENOTAZIONE
    // --------------------------------------------------------
    $sqlPrenotazione = "INSERT INTO prenotazioni (
        cliente_id, stanza_id, data_arrivo, data_partenza, numero_ospiti, prezzo_totale, caparra, saldo, note
    ) VALUES (
        :cliente_id, :stanza_id, :data_arrivo, :data_partenza, :numero_ospiti, :prezzo_totale, :caparra, :saldo, :note
    ) RETURNING id";

    $stmtPren = $pdo->prepare($sqlPrenotazione);
    $stmtPren->execute([
        ':cliente_id'    => $clienteId,
        ':stanza_id'     => $input['stanza_id'] ?? null,
        ':data_arrivo'   => $input['data_arrivo'],
        ':data_partenza' => $input['data_partenza'],
        ':numero_ospiti' => $input['numero_ospiti'] ?? 1,
        ':prezzo_totale' => $input['prezzo_totale'] ?? 0.00,
        ':caparra'       => $input['caparra'] ?? 0.00,
        ':saldo'          => $input['saldo'] ?? 0.00,
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