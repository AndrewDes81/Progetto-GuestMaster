<?php
// includes/statistiche.php
require_once __DIR__ . '/../php/config/json.php';

$prenotazioni = leggiJson('prenotazioni.json');

$totaleMeseIncassato = 0;
$totaleAnnoIncassato = 0;

$MeseSelezionato = isset($_GET['mese']) ? (int)$_GET['mese'] : (int)date('n');
$AnnoSelezionato = isset($_GET['anno']) ? (int)$_GET['anno'] : (int)date('Y');

if (is_array($prenotazioni)) {
    foreach ($prenotazioni as $p) {
        $dataDal = $p['dal'] ?? '';
        if (!empty($dataDal)) {
            $timestamp = strtotime($dataDal);
            $annoPrenotazione = (int)date('Y', $timestamp);
            $mesePrenotazione = (int)date('n', $timestamp);

            $incassatoPrenotazione = floatval($p['caparra'] ?? 0);
            
            if (isset($p['stato']) && ($p['stato'] === 'Saldo Versato' || $p['stato'] === 'Completata')) {
                $incassatoPrenotazione = floatval($p['costo_totale'] ?? $p['prezzo'] ?? 0);
            }

            if ($annoPrenotazione === $AnnoSelezionato) {
                $totaleAnnoIncassato += $incassatoPrenotazione;

                if ($mesePrenotazione === $MeseSelezionato) {
                    $totaleMeseIncassato += $incassatoPrenotazione;
                }
            }
        }
    }
}
?>