-- 0. Pulizia (ordine inverso rispetto alle dipendenze)
DROP TABLE IF EXISTS prenotazioni CASCADE;
DROP TABLE IF EXISTS stanze       CASCADE;
DROP TABLE IF EXISTS clienti      CASCADE;
DROP TABLE IF EXISTS strutture    CASCADE;

-- 1. STRUTTURE (Aggiornato con modalita_ocr per la gestione delle impostazioni)
CREATE TABLE strutture (
    id              SERIAL PRIMARY KEY,
    nome            VARCHAR(150) NOT NULL,
    indirizzo       VARCHAR(255),
    citta           VARCHAR(100),
    provincia       VARCHAR(2),
    cap             VARCHAR(10),
    codice_cin      VARCHAR(50),
    codice_cir      VARCHAR(50),
    modalita_ocr    VARCHAR(20) DEFAULT 'mock', -- 'mock', 'openai', 'gemini'
    api_key_ocr     TEXT,
    data_creazione  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. STANZE (dipende da strutture)
CREATE TABLE stanze (
    id                 SERIAL PRIMARY KEY,
    struttura_id       INT REFERENCES strutture(id) ON DELETE CASCADE,
    nome_stanza        VARCHAR(100) NOT NULL,
    tipologia          VARCHAR(50),
    posti_letto        INT DEFAULT 2,
    prezzo_notte_base  NUMERIC(10,2) DEFAULT 0.00,
    note               TEXT,
    data_creazione     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. CLIENTI
CREATE TABLE clienti (
    id                   SERIAL PRIMARY KEY,
    nome                 VARCHAR(100) NOT NULL,
    cognome              VARCHAR(100) NOT NULL,
    codice_fiscale       VARCHAR(16) UNIQUE,
    sesso                VARCHAR(1),
    data_nascita         DATE,
    luogo_nascita        VARCHAR(100),
    provincia_nascita    VARCHAR(2),
    stato_nascita        VARCHAR(100) DEFAULT 'ITALIA',
    tipo_documento       VARCHAR(50),
    numero_documento     VARCHAR(50),
    rilasciato_da        VARCHAR(100),
    data_emissione       DATE,
    data_scadenza        DATE,
    email                VARCHAR(150),
    cellulare            VARCHAR(50),
    indirizzo            VARCHAR(255),
    citta                VARCHAR(100),
    provincia_residenza  VARCHAR(2),
    stato_residenza      VARCHAR(100) DEFAULT 'ITALIA',
    note                 TEXT,
    data_creazione       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3b. Indice sui clienti
CREATE UNIQUE INDEX idx_clienti_codice_fiscale
    ON clienti (UPPER(codice_fiscale));

-- 4. PRENOTAZIONI (dipende da clienti e stanze)
CREATE TABLE prenotazioni (
    id              SERIAL PRIMARY KEY,
    cliente_id      INT REFERENCES clienti(id) ON DELETE RESTRICT,
    stanza_id       INT REFERENCES stanze(id)  ON DELETE RESTRICT,
    data_arrivo     DATE NOT NULL,
    data_partenza   DATE NOT NULL,
    numero_ospiti   INT DEFAULT 1,
    prezzo_totale   NUMERIC(10,2) DEFAULT 0.00,
    stato           VARCHAR(50) DEFAULT 'confermata',
    note            TEXT,
    data_creazione  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);