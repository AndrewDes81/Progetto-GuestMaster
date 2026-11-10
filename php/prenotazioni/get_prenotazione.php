<?php
header('Content-Type: application/json; charset=utf-8');

// Includi il file di connessione al database PostgreSQL (correggi il percorso se necessario, es. '../../db.php')
require_once '../../db.php'; 

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception('ID prenotazione non valido.');
    }

    // 1. Cerchiamo la prenotazione nel database PostgreSQL
    $sqlP = "SELECT * FROM prenotazioni WHERE id = :id";
    $stmtP = $pdo->prepare($sqlP);
    $stmtP->execute(['id' => $id]);
    $prenotazioneTrovata = $stmtP->fetch(PDO::FETCH_ASSOC);

    if (!$prenotazioneTrovata) {
        throw new Exception('Prenotazione non trovata.');
    }

    // 2. Cerchiamo i dati del cliente associato (usando clienti_id dalla prenotazione)
    $clienteTrovato = null;
    if (!empty($prenotazioneTrovata['clienti_id'])) {
        $sqlC = "SELECT id, nome, cognome, codice_fiscale, cellulare, email FROM clienti WHERE id = :cliente_id";
        $stmtC = $pdo->prepare($sqlC);
        $stmtC->execute(['cliente_id' => $prenotazioneTrovata['clienti_id']]);
        $clienteTrovato = $stmtC->fetch(PDO::FETCH_ASSOC);
    }

    // Restituiamo il JSON pulito al frontend (identico a prima, ma preso da DB)
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
?>