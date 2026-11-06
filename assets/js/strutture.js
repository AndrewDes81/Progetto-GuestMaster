document.addEventListener('DOMContentLoaded', function () {
    const strutturaModalEl = document.getElementById('strutturaModal');
    const modalGestioneStrutture = document.getElementById('gestioneStruttureModal');
    const inputCercaCard = document.getElementById('inputCercaStrutturaCard');
    const grigliaCard = document.getElementById('grigliaCardStrutture');
    const formStruttura = document.getElementById('formStruttura');
    const formStanza = document.getElementById('formStanza');
    const sezioneStanze = document.getElementById('sezioneStanze');
    const avvisoStruttura = document.getElementById('avvisoSelezionaStruttura');
    const strutturaIdEdit = document.getElementById('struttura_id_edit');
    const stanzaStrutturaId = document.getElementById('stanzaStrutturaId');
    const selectStrutturaGlobal = document.getElementById('selectStrutturaGlobal');

    // 1. DICHIARAZIONE DELLA FUNZIONE PER PRIMA
    window.caricaListaStrutture = function () {
        fetch('/php/strutture/get_strutture.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.struttureData = data.data;

                    const selectStruttura = document.getElementById('strutturaId');
                    if (selectStruttura) {
                        const valoreCorrente = selectStruttura.value;
                        selectStruttura.innerHTML = '<option value="">Seleziona struttura...</option>';

                        data.data.forEach(struct => {
                            selectStruttura.innerHTML += `<option value="${struct.id}">${struct.nome}</option>`;
                        });

                        if (valoreCorrente) {
                            selectStruttura.value = valoreCorrente;
                        }
                    }

                    // Se il modale a card è aperto, aggiorna la griglia in tempo reale
                    if (modalGestioneStrutture && modalGestioneStrutture.classList.contains('show')) {
                        applicaFiltroCard();
                    }
                }
            })
            .catch(err => console.error("Errore fetch get_strutture:", err));
    };

    // 2. ORA LA CHIAMIAMO IN SICUREZZA
    caricaListaStrutture();

    // 3. Gestione apertura modale configurazione/inserimento
    if (strutturaModalEl) {
        strutturaModalEl.addEventListener('show.bs.modal', function () {
            if (!strutturaIdEdit || !strutturaIdEdit.value) {
                if (formStruttura) formStruttura.reset();
                if (sezioneStanze) sezioneStanze.style.display = 'none';
                if (avvisoStruttura) avvisoStruttura.style.display = 'block';
            }
            caricaListaStrutture();
        });
    }

    // 3b. Gestione apertura modale Gestione Card Strutture
    if (modalGestioneStrutture) {
        modalGestioneStrutture.addEventListener('show.bs.modal', function () {
            if (inputCercaCard) inputCercaCard.value = '';
            caricaListaStrutture();
            renderizzaCardStrutture(window.struttureData || []);
        });
    }

    // Filtro di ricerca in tempo reale sulle card
    if (inputCercaCard) {
        inputCercaCard.addEventListener('input', function () {
            applicaFiltroCard();
        });
    }

    function applicaFiltroCard() {
        if (!window.struttureData) return;
        const query = inputCercaCard ? inputCercaCard.value.toLowerCase() : '';

        const filtrate = window.struttureData.filter(s =>
            (s.nome && s.nome.toLowerCase().includes(query)) ||
            (s.citta && s.citta.toLowerCase().includes(query)) ||
            (s.codice_cin && s.codice_cin.toLowerCase().includes(query))
        );
        renderizzaCardStrutture(filtrate);
    }

    // Funzione per disegnare le card a griglia
    function renderizzaCardStrutture(lista) {
        if (!grigliaCard) return;
        grigliaCard.innerHTML = '';

        if (lista.length === 0) {
            grigliaCard.innerHTML = '<div class="col-12 text-center text-muted py-4">Nessuna struttura trovata.</div>';
            return;
        }

        lista.forEach(struct => {
            const numStanze = struct.stanze ? struct.stanze.length : 0;

            grigliaCard.innerHTML += `
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold mb-2">${struct.nome}</h5>
                            <p class="card-text text-muted small mb-1">
                                <i class="bi bi-geo-alt"></i> ${struct.indirizzo || 'Indirizzo non specificato'}, ${struct.citta || ''} (${struct.provincia || ''})
                            </p>
                            <p class="card-text text-muted small mb-3">
                                <i class="bi bi-upc-scan"></i> CIN: <span class="fw-semibold">${struct.codice_cin || 'Non inserito'}</span>
                                <span class="badge bg-success float-end">${numStanze} Stanze</span>
                            </p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm flex-fill" onclick="modificaStruttura(${struct.id})">
                                    <i class="bi bi-pencil-square"></i> Modifica
                                </button>
                                <button class="btn btn-primary btn-sm flex-fill" onclick="gestisciStanzeStruttura(${struct.id})">
                                    <i class="bi bi-door-open"></i> Stanze
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // 4. Salvataggio Struttura via AJAX
    if (formStruttura) {
        formStruttura.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(formStruttura));

            fetch('/php/strutture/salva_struttura.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        if (strutturaIdEdit) strutturaIdEdit.value = data.id;
                        if (stanzaStrutturaId) stanzaStrutturaId.value = data.id;

                        if (avvisoStruttura) avvisoStruttura.style.display = 'none';
                        if (sezioneStanze) sezioneStanze.style.display = 'block';

                        caricaListaStrutture();
                    } else {
                        alert('Errore: ' + data.error);
                    }
                })
                .catch(err => console.error('Errore fetch salva_struttura:', err));
        });
    }

    // 5. Salvataggio Stanza via AJAX
    if (formStanza) {
        formStanza.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(formStanza));

            fetch('/php/strutture/salva_stanza.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        formStanza.reset();
                        if (stanzaStrutturaId && strutturaIdEdit) {
                            stanzaStrutturaId.value = strutturaIdEdit.value;
                        }
                        caricaListaStanzePerStruttura(strutturaIdEdit.value);
                        caricaListaStrutture();
                    } else {
                        alert('Errore: ' + data.error);
                    }
                })
                .catch(err => console.error('Errore fetch salva_stanza:', err));
        });
    }

    // 6. Gestione cambio struttura select
    const selectStruttura = document.getElementById('strutturaId');
    if (selectStruttura) {
        selectStruttura.addEventListener('change', function () {
            const strutturaId = this.value;
            const selectStanza = document.getElementById('stanzaId');

            if (selectStanza) {
                selectStanza.innerHTML = '<option value="">Seleziona stanza...</option>';

                if (strutturaId && window.struttureData) {
                    const strutturaTrovata = window.struttureData.find(s => s.id == strutturaId);
                    if (strutturaTrovata && strutturaTrovata.stanze) {
                        strutturaTrovata.stanze.forEach(st => {
                            // AGGIUNTO data-prezzo="${st.prezzo_notte_base}" QUI SOTTO:
                            selectStanza.innerHTML += `<option value="${st.id}" data-prezzo="${st.prezzo_notte_base}">${st.nome_stanza} (${st.posti_letto} posti - €${st.prezzo_notte_base})</option>`;
                        });
                    }
                }
            }
        });
    }

    // 7. Funzione tabella stanze
    function caricaListaStanzePerStruttura(strutturaId) {
        if (window.struttureData) {
            const struct = window.struttureData.find(s => s.id == strutturaId);
            const tbody = document.getElementById('listaStanzeTabel');
            if (tbody && struct && struct.stanze) {
                tbody.innerHTML = '';
                struct.stanze.forEach(st => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${st.nome_stanza}</td>
                            <td>${st.tipologia || '-'}</td>
                            <td>${st.posti_letto}</td>
                            <td>€${parseFloat(st.prezzo_notte_base).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }
        }
    }

    // 8. Gestione del cambio della struttura attiva globale (Header Index)
    // Ascoltatore sicuro per il selettore globale della struttura attiva
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'selectStrutturaGlobal') {
            const strutturaId = e.target.value;

            // Prende i parametri attuali dell'URL (mantenendo mese e anno)
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('struttura_id', strutturaId);

            // Ricarica la pagina applicando il filtro
            window.location.search = urlParams.toString();
        }
    });
});

// Funzione interna per popolare e aprire il modale di configurazione in sicurezza
// Funzione interna per popolare e aprire il modale di configurazione in sicurezza
function eseguiApriModaleConfigurazione(id) {
    if (!window.struttureData) return;
    const struct = window.struttureData.find(s => s.id == id);
    if (!struct) return;

    document.getElementById('struttura_id_edit').value = struct.id;
    document.getElementById('strutturaNome').value = struct.nome || '';
    document.getElementById('strutturaIndirizzo').value = struct.indirizzo || '';
    document.getElementById('strutturaCap').value = struct.cap || '';
    document.getElementById('strutturaCitta').value = struct.citta || '';
    document.getElementById('strutturaProvincia').value = struct.provincia || '';
    document.getElementById('strutturaCin').value = struct.codice_cin || '';
    document.getElementById('strutturaCir').value = struct.codice_cir || '';
    document.getElementById('strutturaOcr').value = struct.modalita_ocr || 'mock';
    document.getElementById('strutturaApiKey').value = struct.api_key_ocr || '';

    // Sblocca la sezione stanze
    document.getElementById('avvisoSelezionaStruttura').style.display = 'none';
    document.getElementById('sezioneStanze').style.display = 'block';
    document.getElementById('stanzaStrutturaId').value = struct.id;

    // Carica le stanze esistenti nella tabella del modale
    const tbody = document.getElementById('listaStanzeTabel');
    if (tbody) {
        tbody.innerHTML = '';
        if (struct.stanze) {
            struct.stanze.forEach(st => {
                tbody.innerHTML += `
                    <tr>
                        <td>${st.nome_stanza}</td>
                        <td>${st.tipologia || '-'}</td>
                        <td>${st.posti_letto}</td>
                        <td>€${parseFloat(st.prezzo_notte_base).toFixed(2)}</td>
                    </tr>
                `;
            });
        }
    }

    // Apre il modale principale di configurazione
    const strutturaModalEl = document.getElementById('strutturaModal');
    if (strutturaModalEl) {
        const modalConfig = new bootstrap.Modal(strutturaModalEl);
        modalConfig.show();
    }
}

// Funzioni globali di supporto per le card (Forzatura pulizia DOM per evitare errori di backdrop)
window.modificaStruttura = function (id) {
    const modalGestioneEl = document.getElementById('gestioneStruttureModal');

    if (modalGestioneEl) {
        // Rimuoviamo visivamente e strutturalmente il modale precedente senza passare da Bootstrap JS
        modalGestioneEl.classList.remove('show');
        modalGestioneEl.style.display = 'none';
    }

    // Pulisce eventuali residui di sfondi scuri bloccati nel DOM
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(b => b.remove());

    // Apre il modale di destinazione
    eseguiApriModaleConfigurazione(id);
};

window.gestisciStanzeStruttura = function (id) {
    window.modificaStruttura(id);
};