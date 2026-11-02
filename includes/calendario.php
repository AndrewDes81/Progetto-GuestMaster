<?php
// Caricamento della configurazione e delle funzioni JSON se non già incluse
require_once __DIR__ . '/../php/config/json.php';

$anno = isset($_GET['anno']) ? (int)$_GET['anno'] : date('Y');
$mese = isset($_GET['mese']) ? (int)$_GET['mese'] : date('n');

$mesi = [
    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
];

$giorniSettimana = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];

// Sostituito cal_days_in_month con la funzione nativa date('t') per evitare errori di estensione mancante
$numeroGiorni = (int)date('t', strtotime("$anno-$mese-01"));
$primoGiorno = (int)date('N', strtotime("$anno-$mese-01"));

// Leggiamo tutte le prenotazioni per colorare il calendario
$prenotazioni = function_exists('leggiJson') ? leggiJson('prenotazioni.json') : [];
?>

<div class="calendar-wrapper">

    <div class="calendar-title mb-3 fw-bold text-secondary">
        <?php echo $mesi[$mese] . ' ' . $anno; ?>
    </div>

    <table class="table calendar-table table-bordered">
        <thead>
            <tr>
                <?php foreach ($giorniSettimana as $giorno) : ?>
                    <th class="text-center">
                        <?php echo $giorno; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                $colonna = 1;

                // Giorni vuoti iniziali del mese
                for ($i = 1; $i < $primoGiorno; $i++) {
                    echo '<td class="empty-day bg-light"></td>';
                    $colonna++;
                }

                // Ciclo sui giorni del mese
                for ($giorno = 1; $giorno <= $numeroGiorni; $giorno++) {
                    // Formattiamo la data corrente nel formato YYYY-MM-DD per il confronto
                    $dataCorrente = sprintf('%04d-%02d-%02d', $anno, $mese, $giorno);

                    $coloreSfondo = '';
                    $prenotazioneTrovata = null;

                    // Verifichiamo se questa data cade in una prenotazione esistente
                    if (is_array($prenotazioni)) {
                        foreach ($prenotazioni as $p) {
                            if (isset($p['dal'], $p['al']) && $dataCorrente >= $p['dal'] && $dataCorrente <= $p['al']) {
                                $prenotazioneTrovata = $p;
                                if (function_exists('coloreDaStato') && isset($p['stato'])) {
                                    $coloreSfondo = coloreDaStato($p['stato']);
                                }
                                break;
                            }
                        }
                    }

                    // Prepariamo stili e attributi se il giorno è occupato
                    $stile = $coloreSfondo ? "style='background-color: {$coloreSfondo}; color: white;'" : "";
                    $classeOccupato = $prenotazioneTrovata ? "occupato" : "";
                    $idPrenotazioneAttr = $prenotazioneTrovata && isset($prenotazioneTrovata['id']) ? "data-prenotazione-id='{$prenotazioneTrovata['id']}'" : "";

                    echo "
                        <td class='calendar-day text-center position-relative $classeOccupato'
                            data-day='$giorno'
                            data-date='$dataCorrente'
                            $idPrenotazioneAttr
                            $stile>
                            <div class='day-content'>
                                <span class='day-number'>
                                    $giorno
                                </span>
                            </div>
                        </td>
                    ";

                    if ($colonna % 7 == 0 && $giorno < $numeroGiorni) {
                        echo '</tr><tr>';
                    }

                    $colonna++;
                }

                // Giorni vuoti finali per chiudere la tabella
                while (($colonna - 1) % 7 != 0) {
                    echo '<td class="empty-day bg-light"></td>';
                    $colonna++;
                }
                ?>
            </tr>
        </tbody>
    </table>
</div>