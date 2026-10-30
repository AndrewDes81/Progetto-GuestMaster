<?php

define('DATA_PATH', __DIR__ . '/../../data');

/**
 * Legge un file JSON e restituisce un array.
 */
function leggiJson($file)
{
    $path = DATA_PATH . '/' . $file;

    if (!file_exists($path)) {
        return [];
    }

    $contenuto = file_get_contents($path);

    if (empty(trim($contenuto))) {
        return [];
    }

    $dati = json_decode($contenuto, true);

    return is_array($dati) ? $dati : [];
}

/**
 * Salva un array in formato JSON.
 */
function salvaJson($file, $dati)
{
    $path = DATA_PATH . '/' . $file;

    file_put_contents(
        $path,
        json_encode(
            $dati,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        )
    );
}

/**
 * Legge il file configurazione.json.
 */
function leggiConfigurazione()
{
    return leggiJson('configurazione.json');
}

/**
 * Genera il prossimo ID.
 *
 * Esempi:
 * prossimoId('ultimo_cliente_id')
 * prossimoId('ultima_prenotazione_id')
 */
function prossimoId($campo)
{
    $config = leggiConfigurazione();

    if (!isset($config[$campo])) {
        $config[$campo] = 0;
    }

    $config[$campo]++;

    salvaJson('configurazione.json', $config);

    return $config[$campo];
}

/**
 * Cerca un cliente per ID.
 */
function cercaClienteById($id)
{
    $clienti = leggiJson('clienti.json');

    foreach ($clienti as $cliente) {
        if ((int)$cliente['id'] === (int)$id) {
            return $cliente;
        }
    }

    return null;
}

/**
 * Cerca una prenotazione per ID.
 */
function cercaPrenotazioneById($id)
{
    $prenotazioni = leggiJson('prenotazioni.json');

    foreach ($prenotazioni as $prenotazione) {
        if ((int)$prenotazione['id'] === (int)$id) {
            return $prenotazione;
        }
    }

    return null;
}

/**
 * Verifica se un periodo è occupato.
 *
 * Restituisce true se esiste una sovrapposizione.
 */
function periodoOccupato($dataInizio, $dataFine, $idDaEscludere = null)
{
    $prenotazioni = leggiJson('prenotazioni.json');

    foreach ($prenotazioni as $prenotazione) {

        if (
            $idDaEscludere !== null &&
            (int)$prenotazione['id'] === (int)$idDaEscludere
        ) {
            continue;
        }

        if (
            $dataInizio <= $prenotazione['al'] &&
            $dataFine >= $prenotazione['dal']
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Calcola il saldo.
 */
function calcolaSaldo($costoTotale, $caparra)
{
    return max(0, $costoTotale - $caparra);
}

/**
 * Restituisce il colore in base allo stato.
 */
function coloreDaStato($stato)
{
    $colori = [
        'PRENOTATA' => '#3498db',
        'CAPARRA_VERSATA' => '#f39c12',
        'SALDO_PAGATO' => '#27ae60',
        'CONCLUSA' => '#2c3e50',
        'ANNULLATA' => '#7f8c8d'
    ];

    return $colori[$stato] ?? '#3498db';
}