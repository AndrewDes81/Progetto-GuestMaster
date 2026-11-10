<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if (strlen($query) < 2) {
        // Se digita meno di 2 caratteri, non restituiamo nulla per alleggerire
        echo json_encode([]);
        exit;
    }

    $sql = "SELECT id, nome, cognome, codice_fiscale, cellulare, email, indirizzo, citta 
            FROM clienti 
            WHERE nome ILIKE :q 
               OR cognome ILIKE :q 
               OR cellulare ILIKE :q 
               OR codice_fiscale ILIKE :q 
            ORDER BY cognome, nome ASC 
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    // Usiamo i percentuali per la ricerca parziale (LIKE)
    $stmt->execute(['q' => "%$query%"]);
    $clienti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clienti);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["errore" => $e->getMessage()]);
}
?>