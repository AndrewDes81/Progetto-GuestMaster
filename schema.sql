-- 1. Tabella Appartamenti
CREATE TABLE IF NOT EXISTS appartamenti (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

INSERT INTO appartamenti (id, nome) VALUES 
(1, 'Appartamento 1'), 
(2, 'Appartamento 2')
ON CONFLICT (id) DO NOTHING;

-- 2. Tabella Locazioni (Inquilini e Tipologie di Affitto)
CREATE TABLE IF NOT EXISTS locazioni (
    id SERIAL PRIMARY KEY,
    appartamento_id INT REFERENCES appartamenti(id),
    nome_inquilino VARCHAR(100) NOT NULL,
    codice_fiscale VARCHAR(16),
    tipo_affitto VARCHAR(20) CHECK (tipo_affitto IN ('giornaliero', 'settimanale', 'mensile')),
    data_inizio DATE NOT NULL,
    data_fine DATE NOT NULL,
    canone_pattuito NUMERIC(10, 2) NOT NULL,
    stato VARCHAR(20) DEFAULT 'Attivo'
);

-- 3. Tabella Tariffe Utenze (Configurazione Unica Bolletta)
CREATE TABLE IF NOT EXISTS tariffe_utenze (
    id SERIAL PRIMARY KEY,
    luce_quota_fissa NUMERIC(10,2) DEFAULT 0,
    luce_costo_kwh NUMERIC(10,4) DEFAULT 0,
    acqua_quota_fissa NUMERIC(10,2) DEFAULT 0,
    acqua_costo_mc NUMERIC(10,4) DEFAULT 0,
    data_aggiornamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tariffe_utenze (id, luce_quota_fissa, luce_costo_kwh, acqua_quota_fissa, acqua_costo_mc)
VALUES (1, 20.00, 0.25, 10.00, 2.50)
ON CONFLICT (id) DO NOTHING;

-- 4. Tabella Letture e Consumi Mensili
CREATE TABLE IF NOT EXISTS letture_utenze (
    id SERIAL PRIMARY KEY,
    locazione_id INT REFERENCES locazioni(id),
    mese_anno VARCHAR(7) NOT NULL,
    luce_lettura_prec NUMERIC(10,2) NOT NULL,
    luce_lettura_att NUMERIC(10,2) NOT NULL,
    luce_consumo NUMERIC(10,2) GENERATED ALWAYS AS (luce_lettura_att - luce_lettura_prec) STORED,
    luce_importo_variabile NUMERIC(10,2) NOT NULL,
    luce_quota_fissa_assegnata NUMERIC(10,2) NOT NULL,
    acqua_lettura_prec NUMERIC(10,2) NOT NULL,
    acqua_lettura_att NUMERIC(10,2) NOT NULL,
    acqua_consumo NUMERIC(10,2) GENERATED ALWAYS AS (acqua_lettura_att - acqua_lettura_prec) STORED,
    acqua_importo_variabile NUMERIC(10,2) NOT NULL,
    acqua_quota_fissa_assegnata NUMERIC(10,2) NOT NULL,
    totale_da_pagare NUMERIC(10,2) NOT NULL,
    data_inserimento DATE DEFAULT CURRENT_DATE
);