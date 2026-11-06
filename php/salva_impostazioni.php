<?php
// php/salva_impostazioni.php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metodo non consentito.");
    }

    $struttura_id = $_SESSION['struttura_id'] ?? 1;
    $modalita_ocr = $_POST['modalita_ocr'] ?? 'mock';
    $api_key_ocr = trim($_POST['api_key_ocr'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE strutture 
        SET modalita_ocr = :modalita_ocr, 
            api_key_ocr = :api_key_ocr 
        WHERE id = :id
    ");
    $stmt->execute([
        ':modalita_ocr' => $modalita_ocr,
        ':api_key_ocr'  => $api_key_ocr,
        ':id'           => $struttura_id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Impostazioni salvate con successo!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}