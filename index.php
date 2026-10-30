<?php
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("
    SELECT l.*, a.nome as nome_app 
    FROM locazioni l 
    JOIN appartamenti a ON l.appartamento_id = a.id 
    WHERE l.id = ?
");
$stmt->execute([$id]);
$locazione = $stmt->fetch();

if (!$locazione) {
    die("Ricevuta non trovata.");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricevuta Affitto - <?= $locazione['nome_inquilino'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white">

    <div class="container my-5" style="max-width: 800px;">
        <div class="text-end no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Stampa / Salva PDF</button>
            <a href="index.php" class="btn btn-secondary">⬅️ Torna Indietro</a>
        </div>

        <div class="card border-dark p-4 shadow-sm">
            <div class="text-center mb-4">
                <h3>RICEVUTA DI PAGAMENTO AFFITTO</h3>
                <p class="text-muted">Gestione Affitti Privata</p>
                <hr>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <strong>Immobile:</strong> <?= $locazione['nome_app'] ?>
                </div>
                <div class="col-6 text-end">
                    <strong>Data Emissione:</strong> <?= date('d/m/Y') ?>
                </div>
            </div>

            <div class="mb-4">
                <h5>Ricevuto da:</h5>
                <p class="mb-1">Sig./Sig.ra: <strong><?= $locazione['nome_inquilino'] ?></strong></p>
                <p class="mb-1">Codice Fiscale: <?= $locazione['codice_fiscale'] ?: 'Non inserito' ?></p>
            </div>

            <div class="mb-4">
                <h5>Dettagli Periodo & Canone:</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Tipologia Affitto</th>
                        <td class="text-uppercase"><?= $locazione['tipo_affitto'] ?></td>
                    </tr>
                    <tr>
                        <th>Periodo di Riferimento</th>
                        <td>Dal <?= date('d/m/Y', strtotime($locazione['data_inizio'])) ?> al <?= date('d/m/Y', strtotime($locazione['data_fine'])) ?></td>
                    </tr>
                    <tr>
                        <th>Importo Canone Pattuito</th>
                        <td><strong>€ <?= number_format($locazione['canone_pattuito'], 2, ',', '.') ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-6">
                    <p class="text-muted">Firma del Locatore</p>
                    <br>
                    <p>___________________________</p>
                </div>
                <div class="col-6 text-end">
                    <p class="text-muted">Firma dell'Inquilino</p>
                    <br>
                    <p>___________________________</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>