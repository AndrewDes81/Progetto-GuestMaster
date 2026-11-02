<?php
// php/clienti/salva_cliente.php

header('Content-Type: application/json');

// Includiamo la connessione al database PostgreSQL via PDO
require_once __DIR__ . '/../config/db.php';

try {
    // Lettura dati dal form
    $nome          = trim($_POST['nome'] ?? '');
    $cognome       = trim($_POST['cognome'] ?? '');
    $codiceFiscale = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $email         = trim($_POST['email'] ?? '');
    $cellulare     = trim($_POST['cellulare'] ?? '');
    $indirizzo     = trim($_POST['indirizzo'] ?? '');
    $citta         = trim($_POST['citta'] ?? '');
    $note          = trim($_POST['note'] ?? '');

    // Validazioni di base
    if (empty($nome) || empty($cognome)) {
        throw new Exception('Nome e cognome sono obbligatori.');
    }

    if (empty($codiceFiscale)) {
        throw new Exception('Il codice fiscale è obbligatorio.');
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email non valida.');
    }

    // 1. Controllo duplicati basato sul Codice Fiscale in PostgreSQL
    $stmtCheck = $pdo->prepare("SELECT * FROM clienti WHERE UPPER(codice_fiscale) = :cf LIMIT 1");
    $stmtCheck->execute(['cf' => $codiceFiscale]);
    $clienteEsistente = $stmtCheck->fetch();

    if ($clienteEsistente) {
        // Trovato! Restituisce i dati del cliente esistente dal DB senza duplicarlo
        echo json_encode([
            'success' => true,
            'cliente_esistente' => true,
            'cliente' => $clienteEsistente
        ]);
        exit;
    }

    // 2. Inserimento del nuovo cliente su PostgreSQL
    $sqlInsert = "INSERT INTO clienti (nome, cognome, codice_fiscale, email, cellulare, indirizzo, citta, note, data_creazione) 
                  VALUES (:nome, :cognome, :codice_fiscale, :email, :cellulare, :indirizzo, :citta, :note, NOW())
                  RETURNING id, data_creazione";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        'nome'           => $nome,
        'cognome'        => $cognome,
        'codice_fiscale' => $codiceFiscale,
        'email'          => $email,
        'cellulare'      => $cellulare,
        'indirizzo'      => $indirizzo,
        'citta'          => $citta,
        'note'           => $note
    ]);

    // Recuperiamo l'ID appena generato da PostgreSQL (SERIAL/BIGSERIAL)
    $nuoviDati = $stmtInsert->fetch();

    // Prepariamo l'oggetto cliente completo da restituire al JS
    $nuovoCliente = [
        'id'             => $nuoviDati['id'],
        'nome'           => $nome,
        'cognome'        => $cognome,
        'codice_fiscale' => $codiceFiscale,
        'email'          => $email,
        'cellulare'      => $cellulare,
        'indirizzo'      => $indirizzo,
        'citta'          => $citta,
        'note'           => $note,
        'data_creazione' => $nuoviDati['data_creazione']
    ];

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