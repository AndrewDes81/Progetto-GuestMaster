document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formCliente');
    if (!form) return;

    // --- AGGIUNTA: Ricerca live automatica per Codice Fiscale ---
    const inputCf = document.getElementById('codice_fiscale');
    if (inputCf) {
        let timeoutCf = null;
        inputCf.addEventListener('input', function() {
            clearTimeout(timeoutCf);
            let cf = this.value.trim();

            if (cf.length >= 10) { // Inizia la ricerca quando il CF è quasi completo
                timeoutCf = setTimeout(() => {
                    fetch(`php/clienti/cerca_per_cf.php?cf=${encodeURIComponent(cf)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.trovato) {
                                // Autocompila i campi con i dati trovati nel DB
                                document.getElementById('nome').value = data.cliente.nome || '';
                                document.getElementById('cognome').value = data.cliente.cognome || '';
                                const emailField = document.getElementById('email');
                                if (emailField) emailField.value = data.cliente.email || '';
                                
                                const cellulareField = document.getElementById('cellulare') || document.getElementById('telefono');
                                if (cellulareField) cellulareField.value = data.cliente.cellulare || data.cliente.telefono || '';
                                
                                const indirizzoField = document.getElementById('indirizzo');
                                if (indirizzoField) indirizzoField.value = data.cliente.indirizzo || '';
                                
                                const cittaField = document.getElementById('citta');
                                if (cittaField) cittaField.value = data.cliente.citta || '';
                                
                                const noteField = document.getElementById('note');
                                if (noteField) noteField.value = data.cliente.note || '';

                                // Imposta l'ID cliente nascosto
                                const inputClienteId = document.getElementById('prenotazione_cliente_id');
                                if (inputClienteId) {
                                    inputClienteId.value = data.cliente.id;
                                }

                                // Feedback visivo immediato
                                const esitoDiv = document.getElementById('esitoCliente');
                                if (esitoDiv) {
                                    esitoDiv.innerHTML = `<div class="alert alert-info py-2 mb-2">Cliente trovato nel database! Dati caricati in automatico.</div>`;
                                }
                            }
                        })
                        .catch(err => console.error("Errore ricerca live cliente:", err));
                }, 400);
            }
        });
    }
    // -----------------------------------------------------------

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);

            const response = await fetch('php/clienti/salva_cliente.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // 1. Estrae i dati del cliente restituiti dal PHP
                const clienteId = result.cliente.id;
                const nomeCompleto = `${result.cliente.nome} ${result.cliente.cognome}`;
                const giaEsistente = result.cliente_esistente;

                // 2. Associa l'ID al campo nascosto della modale prenotazione
                const inputClienteId = document.getElementById('prenotazione_cliente_id');
                if (inputClienteId) {
                    inputClienteId.value = clienteId;
                }

                // 3. Aggiorna la label descrittiva nella modale prenotazione
                const infoCliente = document.getElementById('infoClienteSelezionato');
                if (infoCliente) {
                    const tipo = giaEsistente ? 'Cliente Esistente' : 'Nuovo Cliente';
                    infoCliente.value = `${tipo}: ${nomeCompleto} (ID: ${clienteId})`;
                }

                // 4. Mostra un feedback visivo nel div esito
                const esitoDiv = document.getElementById('esitoCliente');
                if (esitoDiv) {
                    const messaggioTesto = giaEsistente
                        ? 'Cliente già presente nei nostri sistemi. Selezionato automaticamente!'
                        : 'Nuovo cliente registrato correttamente!';
                    esitoDiv.innerHTML = `<div class="alert alert-success py-2 mb-2">${messaggioTesto}</div>`;
                }

                // 5. Abilita tutti i pulsanti prenotazione
                const btnPrenotazioni = document.querySelectorAll('.btn-prenotazione-trigger');
                btnPrenotazioni.forEach(btn => {
                    btn.disabled = false;
                    btn.removeAttribute('disabled');
                });

                // 6. Rilascia il focus dal pulsante
                if (document.activeElement) {
                    document.activeElement.blur();
                }

                // 7. Chiudi la modale cliente e APRI SUBITO DOPO LA MODALE PRENOTAZIONE (Step 2)
                setTimeout(() => {
                    const modalClienteEl = document.getElementById('clienteModal');
                    if (modalClienteEl) {
                        const modalCliente = bootstrap.Modal.getInstance(modalClienteEl) || bootstrap.Modal.getOrCreateInstance(modalClienteEl);
                        modalCliente.hide();
                    }

                    this.reset();
                    if (esitoDiv) esitoDiv.innerHTML = '';

                    // APRE AUTOMATICAMENTE IL FORM DI PRENOTAZIONE (Step 2)
                    const modalPrenotazioneEl = document.getElementById('prenotazioneModal');
                    if (modalPrenotazioneEl) {
                        const modalPrenotazione = bootstrap.Modal.getOrCreateInstance(modalPrenotazioneEl);
                        modalPrenotazione.show();
                    }
                }, 1000);

                // 8. Pulizia forzata di sicurezza per backdrop e scroll del body
                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    // Rimuoviamo solo i backdrop in eccesso lasciando quello della prenotazione se si è appena aperta
                    if (backdrops.length > 1) {
                        backdrops[0].remove();
                    }
                }, 1200);

            } else {
                alert(result.message || 'Errore durante il salvataggio.');
            }

        } catch (error) {
            console.error('Errore di rete o JSON non valido:', error);
            alert('Si è verificato un errore imprevisto.');
        }
    });
});