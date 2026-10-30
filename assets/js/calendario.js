document.addEventListener('DOMContentLoaded', () => {
    // Variabili globali per memorizzare i dati della prenotazione aperta nella modale
    let prenotazioneCorrenteId = null;
    let datiPrenotazioneCorrente = null;
    let clienteCorrenteId = null;

    // 1. Gestione dei click sui singoli giorni del calendario
    const giorni = document.querySelectorAll('.calendar-day');
    giorni.forEach(giorno => {
        giorno.addEventListener('click', () => {
            const prenotazioneId = giorno.dataset.prenotazioneId;

            // Se la cella contiene una prenotazione, carichiamo i dati via AJAX
            if (prenotazioneId) {
                fetch(`php/prenotazioni/get_prenotazione.php?id=${prenotazioneId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const p = data.prenotazione;
                            const c = data.cliente;

                            // Salviamo l'ID e l'intero oggetto nelle variabili globali
                            prenotazioneCorrenteId = p.id;
                            datiPrenotazioneCorrente = p;
                            clienteCorrenteId = c ? c.id : (p.cliente_id || null); // <-- Salviamo l'ID cliente

                            const totale = p.costo_totale !== undefined ? p.costo_totale : (p.prezzo || p.importo || '0');
                            const caparra = p.caparra !== undefined ? p.caparra : (p.acconto || '0');
                            const saldo = p.saldo !== undefined ? p.saldo : '0';

                            // Creiamo il contenuto HTML da inserire nella modale
                            const contenuto = `
                                <div class="mb-3">
                                    <h6 class="text-secondary fw-bold border-bottom pb-1">👤 Informazioni Cliente</h6>
                                    <p class="mb-1"><strong>Nome:</strong> ${c ? c.nome + ' ' + c.cognome : 'Cliente non trovato'}</p>
                                    <p class="mb-1"><strong>Codice Fiscale:</strong> ${c ? c.codice_fiscale : 'N/D'}</p>
                                    <p class="mb-1"><strong>Cellulare:</strong> ${c ? c.cellulare : 'N/D'}</p>
                                    <p class="mb-1"><strong>Email:</strong> ${c ? c.email : 'N/D'}</p>
                                </div>
                                <div>
                                    <h6 class="text-secondary fw-bold border-bottom pb-1">📅 Dettagli Prenotazione</h6>
                                    <p class="mb-1"><strong>Check-in (Dal):</strong> ${p.dal}</p>
                                    <p class="mb-1"><strong>Check-out (Al):</strong> ${p.al}</p>
                                    <p class="mb-1"><strong>Stato:</strong> <span class="badge bg-info text-dark">${p.stato}</span></p>
                                    <p class="mb-1"><strong>Totale:</strong> € ${totale}</p>
                                    <p class="mb-1"><strong>Caparra:</strong> € ${caparra}</p>
                                    <p class="mb-1"><strong>Saldo:</strong> € ${saldo}</p>
                                    ${p.note ? `<p class="mt-2 mb-1"><strong>Note:</strong> ${p.note}</p>` : ''}
                                </div>
                            `;

                            document.getElementById('dettaglioContenuto').innerHTML = contenuto;

                            // Mostriamo la modale di Bootstrap
                            const modalElement = document.getElementById('dettaglioPrenotazioneModal');
                            if (modalElement) {
                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            }
                        } else {
                            alert('Errore: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Errore di comunicazione:', err);
                        alert('Impossibile caricare i dettagli della prenotazione.');
                    });
            }
        });
    });

    // 2. Gestione eliminazione prenotazione
    const btnElimina = document.getElementById('btnEliminaPrenotazione');
    if (btnElimina) {
        btnElimina.addEventListener('click', () => {
            if (!prenotazioneCorrenteId) {
                alert('Nessuna prenotazione selezionata.');
                return;
            }

            if (confirm('Sei sicuro di voler eliminare questa prenotazione? L\'azione è irreversibile e libererà le date nel calendario.')) {
                fetch('php/prenotazioni/elimina_prenotazione.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: prenotazioneCorrenteId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore durante l\'eliminazione: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Errore:', err);
                    alert('Errore di connessione con il server.');
                });
            }
        });
    }

// 3. Gestione modifica prenotazione (Apertura modale di inserimento precompilata)
    const btnModifica = document.getElementById('btnModificaPrenotazione');
    if (btnModifica) {
        btnModifica.addEventListener('click', () => {
            if (!datiPrenotazioneCorrente) {
                alert('Nessuna prenotazione selezionata.');
                return;
            }

            // Chiudiamo la modale di dettaglio
            const modalDettaglioEl = document.getElementById('dettaglioPrenotazioneModal');
            const modalDettaglio = bootstrap.Modal.getInstance(modalDettaglioEl);
            if (modalDettaglio) modalDettaglio.hide();

            // Precompiliamo i campi del form con i dati attuali
            const inputDal = document.getElementById('dataDal');
            const inputAl = document.getElementById('dataAl');
            const inputCosto = document.getElementById('costoTotale');
            const inputCaparra = document.getElementById('caparra');
            const inputSaldo = document.getElementById('saldo');
            const inputStato = document.getElementById('stato');
            const inputNote = document.getElementById('note');

            if (inputDal) inputDal.value = datiPrenotazioneCorrente.dal || '';
            if (inputAl) inputAl.value = datiPrenotazioneCorrente.al || '';
            if (inputCosto) inputCosto.value = datiPrenotazioneCorrente.costo_totale || datiPrenotazioneCorrente.prezzo || 0;
            if (inputCaparra) inputCaparra.value = datiPrenotazioneCorrente.caparra || 0;
            if (inputSaldo) inputSaldo.value = datiPrenotazioneCorrente.saldo || 0;
            if (inputStato) inputStato.value = datiPrenotazioneCorrente.stato || '';
            if (inputNote) inputNote.value = datiPrenotazioneCorrente.note || '';

            // 1. Assegniamo l'ID della prenotazione al form e all'input nascosto
            const formPrenotazione = document.getElementById('formPrenotazione');
            if (formPrenotazione) {
                formPrenotazione.dataset.editId = datiPrenotazioneCorrente.id;
            }
            const inputIdPrenotazione = document.getElementById('prenotazione_id');
            if (inputIdPrenotazione) {
                inputIdPrenotazione.value = datiPrenotazioneCorrente.id;
            }

            // 2. Assegniamo l'ID del cliente al campo nascosto
            const inputClienteId = document.getElementById('prenotazione_cliente_id');
            if (inputClienteId) {
                inputClienteId.value = clienteCorrenteId || '';
            }

            // 3. Compiliamo anche il campo visibile infoClienteSelezionato con i dati del cliente corrente
            const inputInfoCliente = document.getElementById('infoClienteSelezionato');
            if (inputInfoCliente) {
                // Se è un input di testo o una select
                if (inputInfoCliente.tagName === 'INPUT' || inputInfoCliente.tagName === 'TEXTAREA') {
                    inputInfoCliente.value = (typeof c !== 'undefined' && c) ? `${c.nome} ${c.cognome} - ${c.cellulare || ''}` : 'Cliente associato';
                } else {
                    inputInfoCliente.textContent = (typeof c !== 'undefined' && c) ? `${c.nome} ${c.cognome}` : 'Cliente associato';
                }
            }

            // Apriamo la modale di inserimento/modifica
            const modalInserimentoEl = document.getElementById('prenotazioneModal');
            if (modalInserimentoEl) {
                const modalIns = new bootstrap.Modal(modalInserimentoEl);
                modalIns.show();
            }
        });
    }

    // 4. Gestione del cambio Mese o Anno dai selettori in alto
    const selectMese = document.getElementById('mese');
    const selectAnno = document.getElementById('anno');

    function aggiornaCalendario() {
        const mese = selectMese ? selectMese.value : '';
        const anno = selectAnno ? selectAnno.value : '';

        if (mese && anno) {
            window.location.href = `index.php?mese=${mese}&anno=${anno}`;
        }
    }

    if (selectMese) {
        selectMese.addEventListener('change', aggiornaCalendario);
    }

    if (selectAnno) {
        selectAnno.addEventListener('change', aggiornaCalendario);
    }
});