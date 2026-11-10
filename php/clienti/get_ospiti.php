<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if (!empty($query) && strlen($query) >= 2) {
        // Se l'utente sta scrivendo e digita almeno 2 caratteri, filtriamo
        $sql = "SELECT id, nome, cognome, codice_fiscale, cellulare, email, indirizzo, citta 
                FROM clienti 
                WHERE nome ILIKE :q OR cognome ILIKE :q OR cellulare ILIKE :q OR codice_fiscale ILIKE :q 
                ORDER BY cognome, nome ASC LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['q' => "%$query%"]);
    } else {
        // Se il campo è vuoto (es. all'apertura della modale), restituisce TUTTI i clienti del DB
        $sql = "SELECT id, nome, cognome, codice_fiscale, cellulare, email, indirizzo, citta 
                FROM clienti 
                ORDER BY cognome, nome ASC";
        $stmt = $pdo->query($sql);
    }

    $clienti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clienti);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["errore" => $e->getMessage()]);
}
?>