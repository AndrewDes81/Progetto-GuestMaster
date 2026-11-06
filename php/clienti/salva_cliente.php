<?php
// php/clienti/salva_cliente.php

header('Content-Type: application/json');

// Includiamo la connessione al database PostgreSQL via PDO

require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    // Lettura di tutti i dati inviati dal form
    $nome              = trim($_POST['nome'] ?? '');
    $cognome           = trim($_POST['cognome'] ?? '');
    $codiceFiscale     = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $sesso              = trim($_POST['sesso'] ?? '');
    $dataNascita       = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : null;
    $luogoNascita      = trim($_POST['luogo_nascita'] ?? '');
    $provinciaNascita  = strtoupper(trim($_POST['provincia_nascita'] ?? ''));
    
    // Dati Documento
    $tipoDocumento     = trim($_POST['tipo_documento'] ?? '');
    $numeroDocumento   = trim($_POST['numero_documento'] ?? '');
    $rilasciatoDa      = trim($_POST['rilasciato_da'] ?? '');
    $dataScadenza      = !empty($_POST['data_scadenza']) ? $_POST['data_scadenza'] : null;

    // Contatti e Residenza
    $email             = trim($_POST['email'] ?? '');
    $cellulare         = trim($_POST['cellulare'] ?? '');
    $indirizzo         = trim($_POST['indirizzo'] ?? '');
    $citta             = trim($_POST['citta'] ?? '');
    $note              = trim($_POST['note'] ?? '');

    // Validazioni di base
    if (empty($nome) || empty($cognome)) {
        throw new Exception('Nome e cognome sono obbligatori.');
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email non valida.');
    }

    // 1. Controllo duplicati basato sul Codice Fiscale in PostgreSQL
    $clienteEsistente = null;
    if (!empty($codiceFiscale)) {
        $stmtCheck = $pdo->prepare("SELECT * FROM clienti WHERE UPPER(codice_fiscale) = :cf LIMIT 1");
        $stmtCheck->execute(['cf' => $codiceFiscale]);
        $clienteEsistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    }

    if ($clienteEsistente) {
        // Se il cliente esiste già, aggiorniamo i suoi dati con gli ultimi inseriti/scansionati
        $sqlUpdate = "UPDATE clienti SET 
            nome = :nome, 
            cognome = :cognome, 
            sesso = :sesso, 
            data_nascita = :data_nascita, 
            luogo_nascita = :luogo_nascita, 
            provincia_nascita = :provincia_nascita, 
            tipo_documento = :tipo_documento, 
            numero_documento = :numero_documento, 
            rilasciato_da = :rilasciato_da, 
            data_scadenza = :data_scadenza, 
            email = :email, 
            cellulare = :cellulare, 
            indirizzo = :indirizzo, 
            citta = :citta, 
            note = :note 
          WHERE id = :id";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            'nome'              => $nome,
            'cognome'           => $cognome,
            'sesso'             => $sesso,
            'data_nascita'      => $dataNascita,
            'luogo_nascita'     => $luogoNascita,
            'provincia_nascita' => $provinciaNascita,
            'tipo_documento'    => $tipoDocumento,
            'numero_documento'  => $numeroDocumento,
            'rilasciato_da'     => $rilasciatoDa,
            'data_scadenza'     => $dataScadenza,
            'email'             => $email,
            'cellulare'         => $cellulare,
            'indirizzo'         => $indirizzo,
            'citta'             => $citta,
            'note'              => $note,
            'id'                => $clienteEsistente['id']
        ]);

        // Recuperiamo il record aggiornato
        $stmtGet = $pdo->prepare("SELECT * FROM clienti WHERE id = :id");
        $stmtGet->execute(['id' => $clienteEsistente['id']]);
        $clienteAggiornato = $stmtGet->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'cliente_esistente' => true,
            'message' => 'Cliente già esistente: dati aggiornati con successo.',
            'cliente' => $clienteAggiornato
        ]);
        exit;
    }

    // 2. Inserimento del nuovo cliente su PostgreSQL con tutti i campi
    $sqlInsert = "INSERT INTO clienti (
                    nome, cognome, codice_fiscale, sesso, data_nascita, luogo_nascita, 
                    provincia_nascita, tipo_documento, numero_documento, rilasciato_da, 
                    data_scadenza, email, cellulare, indirizzo, citta, note, data_creazione
                  ) VALUES (
                    :nome, :cognome, :codice_fiscale, :sesso, :data_nascita, :luogo_nascita, 
                    :provincia_nascita, :tipo_documento, :numero_documento, :rilasciato_da, 
                    :data_scadenza, :email, :cellulare, :indirizzo, :citta, :note, NOW()
                  ) RETURNING id, data_creazione";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        'nome'              => $nome,
        'cognome'           => $cognome,
        'codice_fiscale'    => !empty($codiceFiscale) ? $codiceFiscale : null,
        'sesso'             => $sesso,
        'data_nascita'      => $dataNascita,
        'luogo_nascita'     => $luogoNascita,
        'provincia_nascita' => $provinciaNascita,
        'tipo_documento'    => $tipoDocumento,
        'numero_documento'  => $numeroDocumento,
        'rilasciato_da'     => $rilasciatoDa,
        'data_scadenza'     => $dataScadenza,
        'email'             => $email,
        'cellulare'         => $cellulare,
        'indirizzo'         => $indirizzo,
        'citta'             => $citta,
        'note'              => $note
    ]);

    $nuoviDati = $stmtInsert->fetch(PDO::FETCH_ASSOC);

    // Ricostruiamo l'oggetto completo da restituire al frontend
    $nuovoCliente = [
        'id'                => $nuoviDati['id'],
        'nome'              => $nome,
        'cognome'           => $cognome,
        'codice_fiscale'    => $codiceFiscale,
        'sesso'             => $sesso,
        'data_nascita'      => $dataNascita,
        'luogo_nascita'     => $luogoNascita,
        'provincia_nascita' => $provinciaNascita,
        'tipo_documento'    => $tipoDocumento,
        'numero_documento'  => $numeroDocumento,
        'rilasciato_da'     => $rilasciatoDa,
        'data_scadenza'     => $dataScadenza,
        'email'             => $email,
        'cellulare'         => $cellulare,
        'indirizzo'         => $indirizzo,
        'citta'             => $citta,
        'note'              => $note,
        'data_creazione'    => $nuoviDati['data_creazione']
    ];

    echo json_encode([
        'success' => true,
        'cliente_esistente' => false,
        'message' => 'Nuovo cliente registrato con successo!',
        'cliente' => $nuovoCliente
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}