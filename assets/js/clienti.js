document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formCliente');
    if (!form) return;

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

                // 4. Mostra un feedback visivo nel div esito (se presente) o un piccolo alert
                const esitoDiv = document.getElementById('esitoCliente');
                if (esitoDiv) {
                    const messaggioTesto = giaEsistente
                        ? 'Cliente già presente nei nostri sistemi. Selezionato automaticamente!'
                        : 'Nuovo cliente registrato correttamente!';
                    esitoDiv.innerHTML = `<div class="alert alert-success py-2 mb-2">${messaggioTesto}</div>`;
                }

                // 5. Abilita tutti i pulsanti prenotazione (sia desktop che mobile)
                const btnPrenotazioni = document.querySelectorAll('.btn-prenotazione-trigger');
                btnPrenotazioni.forEach(btn => {
                    btn.disabled = false;
                    btn.removeAttribute('disabled');
                });

                // 6. Rilascia il focus dal pulsante
                if (document.activeElement) {
                    document.activeElement.blur();
                }

                // 7. Chiudi la modale con Bootstrap dopo un breve istante (per permettere di leggere l'esito)
                setTimeout(() => {
                    const modalElement = document.getElementById('clienteModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
                        modal.hide();
                    }

                    this.reset();
                    if (esitoDiv) esitoDiv.innerHTML = '';
                }, 1000);

                // 8. Pulizia forzata di sicurezza per backdrop e scroll del body
                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
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