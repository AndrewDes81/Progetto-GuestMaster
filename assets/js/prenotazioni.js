document.addEventListener('DOMContentLoaded', () => {
    // 1. Collegamento esatto agli ID presenti in modal_prenotazione.php
    const stanzaSelect = document.getElementById('stanzaId');
    const dataArrivoInput = document.getElementById('dataDal');
    const dataPartenzaInput = document.getElementById('dataAl');
    const costoTotaleInput = document.getElementById('costoTotale');
    const caparraInput = document.getElementById('caparra');
    const saldoInput = document.getElementById('saldo');
    const numNottiDisplay = document.getElementById('numNottiDisplay');

    // Funzione di calcolo del costo totale
    function calcolaCostoTotale() {
        if (!stanzaSelect || !dataArrivoInput || !dataPartenzaInput || !costoTotaleInput) return;

        const stanzaId = stanzaSelect.value;
        const arrivoVal = dataArrivoInput.value;
        const partenzaVal = dataPartenzaInput.value;

        if (stanzaId && arrivoVal && partenzaVal) {
            const arrivo = new Date(arrivoVal);
            const partenza = new Date(partenzaVal);

            // Calcolo delle notti
            const diffTime = partenza - arrivo;
            const diffNotti = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffNotti > 0) {
                // Aggiorna il display visivo delle notti (se l'input esiste nella modale)
                if (numNottiDisplay) {
                    numNottiDisplay.value = diffNotti;
                }

                let prezzoNotte = 0;

                // Tentativo di lettura da window.struttureData
                if (typeof window.struttureData !== 'undefined' && Array.isArray(window.struttureData)) {
                    for (const struttura of window.struttureData) {
                        if (struttura.stanze) {
                            const stanzaTrovata = struttura.stanze.find(s => s.id == stanzaId);
                            if (stanzaTrovata) {
                                prezzoNotte = parseFloat(stanzaTrovata.prezzo_notte_base) || 0;
                                break;
                            }
                        }
                    }
                }

                // Fallback sull'attributo data-prezzo dell'option HTML
                if (prezzoNotte === 0 && stanzaSelect.selectedIndex >= 0) {
                    const selectedOption = stanzaSelect.options[stanzaSelect.selectedIndex];
                    prezzoNotte = parseFloat(selectedOption.getAttribute('data-prezzo')) || 0;
                }

                const totale = diffNotti * prezzoNotte;
                costoTotaleInput.value = totale.toFixed(2);
                
                calcolaSaldo();
            } else {
                costoTotaleInput.value = '0.00';
                calcolaSaldo();
            }
        }
    }

    // Calcolo automatico del saldo
    function calcolaSaldo() {
        if (!costoTotaleInput || !caparraInput || !saldoInput) return;
        const totale = parseFloat(costoTotaleInput.value) || 0;
        const caparra = parseFloat(caparraInput.value) || 0;
        let saldo = totale - caparra;
        if (saldo < 0) saldo = 0;
        saldoInput.value = saldo.toFixed(2);
    }

    // Registrazione degli eventi di ascolto sui campi corretti
    if (stanzaSelect) stanzaSelect.addEventListener('change', calcolaCostoTotale);
    if (dataArrivoInput) dataArrivoInput.addEventListener('change', calcolaCostoTotale);
    if (dataPartenzaInput) dataPartenzaInput.addEventListener('change', calcolaCostoTotale);

    if (costoTotaleInput && caparraInput) {
        costoTotaleInput.addEventListener('input', calcolaSaldo);
        caparraInput.addEventListener('input', calcolaSaldo);
    }

    // Gestione invio form di prenotazione
    const formPrenotazione = document.getElementById('formPrenotazione');
    if (!formPrenotazione) return;

    formPrenotazione.addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);
            const editId = this.dataset.editId;
            let url = 'php/prenotazioni/salva_prenotazione.php';

            if (editId) {
                url = 'php/prenotazioni/modifica_prenotazione.php';
                formData.append('id', editId);
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (document.activeElement) {
                    document.activeElement.blur();
                }

                const modalElement = document.getElementById('prenotazioneModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.hide();
                }

                this.reset();
                delete this.dataset.editId;

                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                }, 200);

                alert(editId ? 'Prenotazione modificata con successo!' : 'Prenotazione salvata con successo!');
                location.reload();

            } else {
                alert(result.message || 'Errore durante il salvataggio della prenotazione.');
            }

        } catch (error) {
            console.error('Errore di rete:', error);
            alert('Si è verificato un errore imprevisto durante il salvataggio.');
        }
    });
});