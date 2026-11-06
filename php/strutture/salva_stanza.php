<?php
// php/strutture/salva_stanza.php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['struttura_id']) || empty($input['nome_stanza'])) {
        throw new Exception("Struttura e nome della stanza sono obbligatori.");
    }

    $sql = "INSERT INTO stanze (
                struttura_id, nome_stanza, tipologia, posti_letto, prezzo_notte_base, note
            ) VALUES (
                :struttura_id, :nome_stanza, :tipologia, :posti_letto, :prezzo_notte_base, :note
            ) RETURNING id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':struttura_id'      => $input['struttura_id'],
        ':nome_stanza'       => $input['nome_stanza'],
        ':tipologia'         => $input['tipologia'] ?? 'Standard',
        ':posti_letto'       => $input['posti_letto'] ?? 2,
        ':prezzo_notte_base' => $input['prezzo_notte_base'] ?? 0.00,
        ':note'              => $input['note'] ?? ''
    ]);

    $stanzaId = $stmt->fetchColumn();

    echo json_encode([
        'success'   => true,
        'message'   => 'Stanza aggiunta con successo!',
        'id'        => $stanzaId
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}