<?php
// Imposta l'intestazione per restituire JSON
header('Content-Type: application/json; charset=utf-8');

// Includi il file di connessione al database (assicurati che il percorso sia corretto, es. '../db.php')
require_once '../db.php'; 

// Recupera il parametro struttura_id passato via GET dalla fetch di JavaScript
$struttura_id = isset($_GET['struttura_id']) ? $_GET['struttura_id'] : 'all';

try {
    if ($struttura_id === 'all' || empty($struttura_id)) {
        // Se non c'è una struttura specifica selezionata, prendiamo tutti i clienti
        $sql = "SELECT id, nome, cognome, email, cellulare FROM clienti ORDER BY cognome, nome ASC";
        $stmt = $pdo->query($sql);
        $ospiti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Filtriamo i clienti in base alla struttura attiva (supponendo che la tabella clienti abbia la colonna struttura_id)
        $sql = "SELECT id, nome, cognome, email, cellulare FROM clienti WHERE struttura_id = :struttura_id ORDER BY cognome, nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['struttura_id' => $struttura_id]);
        $ospiti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Restituisce i dati in formato JSON pulito
    echo json_encode($ospiti);

} catch (PDOException $e) {
    // In caso di errore del database, restituisce un codice di errore HTTP 500 e il messaggio JSON
    http_response_code(500);
    echo json_encode(["errore" => "Errore di connessione o query: " . $e->getMessage()]);
}
?>