-- 1. Crea la tabella STRUTTURE
create table if not exists strutture (
    id SERIAL primary key,
    nome VARCHAR(150) not null,
    indirizzo VARCHAR(255),
    citta VARCHAR(100),
    provincia VARCHAR(2),
    cap VARCHAR(10),
    codice_cir_cin VARCHAR(50),
    data_creazione TIMESTAMP default CURRENT_TIMESTAMP
);
-- 2. Crea la tabella STANZE (dipende da strutture)
create table if not exists stanze (
    id SERIAL primary key,
    struttura_id INT references strutture(id) on
delete
	cascade,
	nome_stanza VARCHAR(100) not null,
	tipologia VARCHAR(50),
	posti_letto INT default 2,
	prezzo_notte_base numeric(10,
	2) default 0.00,
	note text,
	data_creazione TIMESTAMP default CURRENT_TIMESTAMP
);
-- 3. Crea la tabella CLIENTI
create table if not exists clienti (
    id SERIAL primary key,
    nome VARCHAR(100) not null,
    cognome VARCHAR(100) not null,
    codice_fiscale VARCHAR(16) unique,
    sesso VARCHAR(1),
    data_nascita DATE,
    luogo_nascita VARCHAR(100),
    provincia_nascita VARCHAR(2),
    stato_nascita VARCHAR(100) default 'ITALIA',
    tipo_documento VARCHAR(50),
    numero_documento VARCHAR(50),
    rilasciato_da VARCHAR(100),
    data_emissione DATE,
    data_scadenza DATE,
    email VARCHAR(150),
    cellulare VARCHAR(50),
    indirizzo VARCHAR(255),
    citta VARCHAR(100),
    provincia_residenza VARCHAR(2),
    stato_residenza VARCHAR(100) default 'ITALIA',
    note text,
    data_creazione TIMESTAMP default CURRENT_TIMESTAMP
);
-- 3b. Crea l'indice sui clienti
create unique index if not exists idx_clienti_codice_fiscale on
clienti (UPPER(codice_fiscale));
-- 4. Crea la tabella PRENOTAZIONI (dipende da clienti e stanze)
create table if not exists prenotazioni (
    id SERIAL primary key,
    cliente_id INT references clienti(id) on
delete
	restrict,
	stanza_id INT references stanze(id) on
	delete
		restrict,
		data_arrivo DATE not null,
		data_partenza DATE not null,
		numero_ospiti INT default 1,
		prezzo_totale numeric(10,
		2) default 0.00,
		stato VARCHAR(50) default 'confermata',
		note text,
		data_creazione TIMESTAMP default CURRENT_TIMESTAMP
);