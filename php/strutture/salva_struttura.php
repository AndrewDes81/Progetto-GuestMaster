<?php
// php/strutture/salva_struttura.php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/php/config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['nome'])) {
        throw new Exception("Il nome della struttura è obbligatorio.");
    }

    $nome = trim($input['nome']);
    $citta = trim($input['citta'] ?? '');
    $cin = trim($input['codice_cin'] ?? '');
    $id = $input['id'] ?? null;

    // Iniziamo la transazione esplicita
    $pdo->beginTransaction();

    // 1. CONTROLLO UNICITÀ CIN (Se non è vuoto)
    if (!empty($cin)) {
        $sqlCin = "SELECT id FROM strutture WHERE codice_cin = :cin";
        $paramsCin = [':cin' => $cin];

        if (!empty($id)) {
            $sqlCin .= " AND id != :id";
            $paramsCin[':id'] = $id;
        }

        $stmtCin = $pdo->prepare($sqlCin);
        $stmtCin->execute($paramsCin);

        if ($stmtCin->fetch()) {
            throw new Exception("Esiste già un'altra struttura registrata con il Codice CIN: " . $cin);
        }
    }

    // 2. CONTROLLO DUPLICATO NOME + CITTA (Evita omonimie nella stessa località)
    if (!empty($citta)) {
        $sqlDup = "SELECT id FROM strutture WHERE LOWER(nome) = LOWER(:nome) AND LOWER(citta) = LOWER(:citta)";
        $paramsDup = [':nome' => $nome, ':citta' => $citta];

        if (!empty($id)) {
            $sqlDup .= " AND id != :id";
            $paramsDup[':id'] = $id;
        }

        $stmtDup = $pdo->prepare($sqlDup);
        $stmtDup->execute($paramsDup);

        if ($stmtDup->fetch()) {
            throw new Exception("Esiste già una struttura con questo nome a " . $citta . ".");
        }
    }

    if (!empty($id)) {
        // UPDATE Struttura esistente
        $sql = "UPDATE strutture SET 
                    nome = :nome, 
                    indirizzo = :indirizzo, 
                    citta = :citta, 
                    provincia = :provincia, 
                    cap = :cap, 
                    codice_cin = :codice_cin, 
                    codice_cir = :codice_cir, 
                    modalita_ocr = :modalita_ocr, 
                    api_key_ocr = :api_key_ocr 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id'           => $id,
            ':nome'         => $nome,
            ':indirizzo'    => $input['indirizzo'] ?? null,
            ':citta'        => $input['citta'] ?? null,
            ':provincia'    => $input['provincia'] ?? null,
            ':cap'          => $input['cap'] ?? null,
            ':codice_cin'   => !empty($cin) ? $cin : null,
            ':codice_cir'   => $input['codice_cir'] ?? null,
            ':modalita_ocr' => $input['modalita_ocr'] ?? 'mock',
            ':api_key_ocr'  => $input['api_key_ocr'] ?? null
        ]);
        
        $strutturaId = $id;
        $message = "Struttura aggiornata con successo!";
    } else {
        // INSERT Nuova Struttura
        $sql = "INSERT INTO strutture (
                    nome, indirizzo, citta, provincia, cap, 
                    codice_cin, codice_cir, modalita_ocr, api_key_ocr
                ) VALUES (
                    :nome, :indirizzo, :citta, :provincia, :cap, 
                    :codice_cin, :codice_cir, :modalita_ocr, :api_key_ocr
                ) RETURNING id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'         => $nome,
            ':indirizzo'    => $input['indirizzo'] ?? null,
            ':citta'        => $input['citta'] ?? null,
            ':provincia'    => $input['provincia'] ?? null,
            ':cap'          => $input['cap'] ?? null,
            ':codice_cin'   => !empty($cin) ? $cin : null,
            ':codice_cir'   => $input['codice_cir'] ?? null,
            ':modalita_ocr' => $input['modalita_ocr'] ?? 'mock',
            ':api_key_ocr'  => $input['api_key_ocr'] ?? null
        ]);
        
        $strutturaId = $stmt->fetchColumn();
        $message = "Struttura creata con successo!";
    }

    // Conferma della transazione
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id'      => $strutturaId
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