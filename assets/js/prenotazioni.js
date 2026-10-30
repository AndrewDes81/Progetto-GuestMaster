document.addEventListener('DOMContentLoaded', () => {
    // 1. Calcolo automatico del saldo quando cambiano Costo Totale o Caparra
    const costoTotaleInput = document.getElementById('costoTotale');
    const caparraInput = document.getElementById('caparra');
    const saldoInput = document.getElementById('saldo');

    function calcolaSaldo() {
        const totale = parseFloat(costoTotaleInput.value) || 0;
        const caparra = parseFloat(caparraInput.value) || 0;
        let saldo = totale - caparra;
        if (saldo < 0) saldo = 0;
        saldoInput.value = saldo.toFixed(2);
    }

    if (costoTotaleInput && caparraInput) {
        costoTotaleInput.addEventListener('input', calcolaSaldo);
        caparraInput.addEventListener('input', calcolaSaldo);
    }

    // 2. Gestione dell'invio del form di prenotazione (Inserimento o Modifica)
    const formPrenotazione = document.getElementById('formPrenotazione');
    if (!formPrenotazione) return;

    formPrenotazione.addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);

            // Verifichiamo se stiamo modificando un record esistente o creandone uno nuovo
            const editId = this.dataset.editId;
            let url = 'php/prenotazioni/salva_prenotazione.php';

            if (editId) {
                url = 'php/prenotazioni/modifica_prenotazione.php';
                formData.append('id', editId); // Aggiungiamo l'ID al form data per la modifica
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Rilascia il focus per evitare conflitti di accessibilità con Bootstrap
                if (document.activeElement) {
                    document.activeElement.blur();
                }

                // Chiudi la modale
                const modalElement = document.getElementById('prenotazioneModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.hide();
                }

                // Resetta il form e rimuovi l'eventuale ID di modifica
                this.reset();
                delete this.dataset.editId;

                // Pulizia forzata del backdrop di Bootstrap
                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                }, 200);

                // Notifica e ricaricamento pagina
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