<?php
// php/ocr_scan.php
header('Content-Type: application/json');
session_start();
require_once 'config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $imageBase64 = $input['image'] ?? null;

    if (!$imageBase64) {
        throw new Exception("Nessuna immagine ricevuta.");
    }

    $struttura_id = $_SESSION['struttura_id'] ?? 1;

    // Recupera la modalità e la chiave API dal database PostgreSQL
    $stmt = $pdo->prepare("SELECT modalita_ocr, api_key_ocr FROM strutture WHERE id = :id");
    $stmt->execute([':id' => $struttura_id]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    $modalita = $config['modalita_ocr'] ?? 'mock';
    $apiKey = trim($config['api_key_ocr'] ?? '');

    // Se è stata scelta l'IA reale ma manca la chiave API, torna al mock per sicurezza
    if ($modalita !== 'mock' && empty($apiKey)) {
        $modalita = 'mock';
    }

    if ($modalita === 'openai') {
        // -----------------------------------------------------------------
        // 1. INTEGRAZIONE REALE OPENAI (GPT-4o-mini Vision)
        // -----------------------------------------------------------------
        $promptText = "Estrai i dati da questo documento di identità e restituisci ESCLUSIVAMENTE un oggetto JSON valido con queste chiavi esatte: nome, cognome, codice_fiscale, sesso (M o F), data_nascita (formato YYYY-MM-DD), luogo_nascita, provincia_nascita (2 lettere), tipo_documento, numero_documento, rilasciato_da, data_scadenza (YYYY-MM-DD).";

        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        ["type" => "text", "text" => $promptText],
                        ["type" => "image_url", "image_url" => ["url" => $imageBase64]]
                    ]
                ]
            ],
            "response_format" => ["type" => "json_object"],
            "max_tokens" => 500
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Errore risposta API OpenAI (HTTP $httpCode)");
        }

        $resData = json_decode($response, true);
        $jsonEstratto = $resData['choices'][0]['message']['content'] ?? null;
        $datiEstratti = json_decode($jsonEstratto, true);

    } else {
        // -----------------------------------------------------------------
        // 2. PIANO BASE GRATUITO (Mock simulato)
        // -----------------------------------------------------------------
        $datiEstratti = [
            'nome'              => 'MARIO (Mock Demo)',
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
        ];
    }

    echo json_encode([
        'success' => true,
        'modalita' => $modalita,
        'message' => 'Documento analizzato con successo!',
        'data'    => $datiEstratti
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}