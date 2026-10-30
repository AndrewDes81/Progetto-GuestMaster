// assets/js/cerca_cliente.js

document.addEventListener('DOMContentLoaded', function() {
    const btnDesktop = document.getElementById('btnCercaClienteDesktop');
    const btnMobile = document.getElementById('btnCercaClienteMobile');
    const modalCercaEl = document.getElementById('cercaClienteModal');
    
    const inputFiltro = document.getElementById('inputFiltroCliente');
    const containerClienti = document.getElementById('listaClientiTrovati');
    const sezioneStorico = document.getElementById('sezioneStoricoCliente');
    const nomeStoricoEl = document.getElementById('nomeClienteSelezionatoStorico');
    const containerStorico = document.getElementById('tabellaStoricoSoggiorni');

    let tuttiIClienti = [];
    let tutteLePrenotazioni = [];

    if (modalCercaEl) {
        const modalCerca = new bootstrap.Modal(modalCercaEl);

        // Funzione per aprire la modale e caricare i dati
        const apriModaleCerca = (e) => {
            if (e) e.preventDefault();
            caricaDatiPerRicerca();
            modalCerca.show();
        };

        if (btnDesktop) btnDesktop.addEventListener('click', apriModaleCerca);
        if (btnMobile) btnMobile.addEventListener('click', apriModaleCerca);
    }

    // Carichiamo i dati dei clienti e delle prenotazioni dai file JSON
    function caricaDatiPerRicerca() {
        // Carica clienti
        fetch('data/clienti.json')
            .then(res => res.json())
            .then(data => {
                tuttiIClienti = Array.isArray(data) ? data : (data.clienti || []);
                mostraClienti(tuttiIClienti);
            })
            .catch(err => console.error('Errore caricamento clienti:', err));

        // Carica prenotazioni per lo storico
        fetch('data/prenotazioni.json')
            .then(res => res.json())
            .then(data => {
                tutteLePrenotazioni = Array.isArray(data) ? data : (data.prenotazioni || []);
            })
            .catch(err => console.error('Errore caricamento prenotazioni:', err));
    }

    // Funzione per visualizzare la lista dei clienti come Card Responsive
    function mostraClienti(clienti) {
        if (!containerClienti) return;
        
        if (clienti.length === 0) {
            containerClienti.innerHTML = `<div class="col-12 text-center text-muted py-3">Nessun cliente trovato.</div>`;
            return;
        }

        containerClienti.innerHTML = '';
        clienti.forEach(c => {
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6'; // 1 colonna su Mobile, 2 colonne su Desktop
            col.innerHTML = `
                <div class="card h-100 border shadow-sm">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="mb-2">
                            <h6 class="fw-bold mb-1 text-dark">${c.nome || ''} ${c.cognome || ''}</h6>
                            <div class="text-muted small">
                                📞 ${c.cellulare || 'N/D'} <br>
                                ✉️ ${c.email || 'N/D'} <br>
                                🆔 ${c.codice_fiscale || 'N/D'}
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary flex-grow-1 btn-storico" data-id="${c.id}">
                                📁 Storico
                            </button>
                            <button class="btn btn-sm btn-success flex-grow-1 btn-seleziona" data-id="${c.id}">
                                ✔ Scegli
                            </button>
                        </div>
                    </div>
                </div>
            `;
            containerClienti.appendChild(col);
        });

        // Aggiungiamo gli eventi ai pulsanti generati dinamicamente
        attivaEventiPulsanti();
    }

    // FILTRO IN TEMPO REALE DURANTE LA DIGITAZIONE (Ripristinato!)
    if (inputFiltro) {
        inputFiltro.addEventListener('input', function() {
            const testo = this.value.toLowerCase().trim();
            const filtrati = tuttiIClienti.filter(c => {
                const nome = (c.nome || '').toLowerCase();
                const cognome = (c.cognome || '').toLowerCase();
                const cf = (c.codice_fiscale || '').toLowerCase();
                const tel = (c.cellulare || '').toLowerCase();
                const email = (c.email || '').toLowerCase();

                return nome.includes(testo) || cognome.includes(testo) || cf.includes(testo) || tel.includes(testo) || email.includes(testo);
            });
            mostraClienti(filtrati);
        });
    }

    // Gestione dei click sui pulsanti delle card clienti
    function attivaEventiPulsanti() {
        // Pulsante Storico Soggiorni
        document.querySelectorAll('.btn-storico').forEach(btn => {
            btn.addEventListener('click', function() {
                const clienteId = this.getAttribute('data-id');
                const cliente = tuttiIClienti.find(c => String(c.id) === String(clienteId));
                
                if (cliente) {
                    mostraStoricoCliente(cliente);
                }
            });
        });

        // Pulsante Seleziona per Prenotazione
        document.querySelectorAll('.btn-seleziona').forEach(btn => {
            btn.addEventListener('click', function() {
                const clienteId = this.getAttribute('data-id');
                const cliente = tuttiIClienti.find(c => String(c.id) === String(clienteId));
                
                if (cliente) {
                    // Chiudiamo la modale di ricerca
                    const modalCercaEl = document.getElementById('cercaClienteModal');
                    const modalCerca = bootstrap.Modal.getInstance(modalCercaEl);
                    if (modalCerca) modalCerca.hide();

                    // Precompilazione dati prenotazione
                    if (typeof selezionaClientePerPrenotazione === 'function') {
                        selezionaClientePerPrenotazione(cliente);
                    } else {
                        const inputClienteId = document.getElementById('prenotazione_cliente_id');
                        const inputInfoCliente = document.getElementById('infoClienteSelezionato');
                        if (inputClienteId) inputClienteId.value = cliente.id;
                        if (inputInfoCliente) inputInfoCliente.value = `${cliente.nome} ${cliente.cognome} - ${cliente.cellulare || ''}`;
                        
                        const modalPrenotazioneEl = document.getElementById('prenotazioneModal');
                        if (modalPrenotazioneEl) {
                            new bootstrap.Modal(modalPrenotazioneEl).show();
                        }
                    }
                }
            });
        });
    }

    // Mostra lo storico soggiorni come schede compatte
    function mostraStoricoCliente(cliente) {
        if (!sezioneStorico || !nomeStoricoEl || !containerStorico) return;

        nomeStoricoEl.textContent = `${cliente.nome} ${cliente.cognome}`;
        sezioneStorico.style.display = 'block';

        const prenotazioniCliente = tutteLePrenotazioni.filter(p => String(p.cliente_id) === String(cliente.id));

        if (prenotazioniCliente.length === 0) {
            containerStorico.innerHTML = `<div class="text-center text-muted py-3 small bg-light rounded">Nessun soggiorno registrato per questo cliente.</div>`;
            return;
        }

        containerStorico.innerHTML = '';
        prenotazioniCliente.forEach(p => {
            const card = document.createElement('div');
            card.className = 'card card-body p-2 border-start border-4 border-primary shadow-sm bg-light';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="fw-bold text-dark">🗓️ ${p.dal || '?'} ➔ ${p.al || '?'}</small>
                        <div class="small text-muted">Totale: <strong class="text-success">€ ${parseFloat(p.costo_totale || p.prezzo || 0).toFixed(2)}</strong> | Caparra: € ${parseFloat(p.caparra || 0).toFixed(2)}</div>
                    </div>
                    <span class="badge bg-primary text-white">${p.stato || 'Confermata'}</span>
                </div>
            `;
            containerStorico.appendChild(card);
        });
    }
});