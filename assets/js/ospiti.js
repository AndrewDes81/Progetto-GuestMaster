document.addEventListener('DOMContentLoaded', function () {
    const modalOspiti = document.getElementById('gestioneOspitiModal');

    // Quando il modale viene aperto, carichiamo gli ospiti
    if (modalOspiti) {
        modalOspiti.addEventListener('show.bs.modal', function () {
            caricaOspiti();
        });
    }

    // Filtro di ricerca rapida locale nella tabella ospiti
    const cercaInput = document.getElementById('cercaOspiteInput');
    if (cercaInput) {
        cercaInput.addEventListener('input', function () {
            const filtro = this.value.toLowerCase();
            const righe = document.querySelectorAll('#tabellaOspitiBody tr');
            
            righe.forEach(riga => {
                const testo = riga.textContent.toLowerCase();
                riga.style.display = testo.includes(filtro) ? '' : 'none';
            });
        });
    }
});

// Funzione per recuperare gli ospiti dal backend
function caricaOspiti() {
    const selectStruttura = document.getElementById('selectStrutturaGlobal');
    const strutturaId = selectStruttura ? selectStruttura.value : 'all';

    fetch(`php/clienti/get_ospiti.php?struttura_id=${strutturaId}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabellaOspitiBody');
            if (!tbody) return;
            tbody.innerHTML = '';

            const ospitiArray = Array.isArray(data) ? data : (data.clienti || []);

            if (ospitiArray.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Nessun ospite trovato.</td></tr>`;
                return;
            }

            ospitiArray.forEach(ospite => {
                let tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ospite.nome || ''}</td>
                    <td>${ospite.cognome || ''}</td>
                    <td>${ospite.email || '-'}</td>
                    <td>${ospite.cellulare || '-'}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="apriModificaOspite(${ospite.id})" title="Modifica">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminaOspite(${ospite.id})" title="Elimina">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Errore nel caricamento degli ospiti:', error);
            const tbody = document.getElementById('tabellaOspitiBody');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Errore durante il caricamento dei dati.</td></tr>`;
            }
        });
}

// Funzione per l'eliminazione dell'ospite con chiamata asincrona al backend
async function eliminaOspite(id) {
    if (!confirm("Sei sicuro di voler eliminare definitivamente questo ospite?")) {
        return;
    }

    try {
        const response = await fetch('php/clienti/elimina_ospite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (result.success) {
            // Ricarica la lista degli ospiti all'interno della modale
            caricaOspiti();
        } else {
            alert(result.error || 'Errore durante l\'eliminazione dell\'ospite.');
        }
    } catch (error) {
        console.error('Errore di rete:', error);
        alert('Si è verificato un errore di connessione durante l\'eliminazione.');
    }
}

// Funzione per la modifica dell'ospite
async function apriModificaOspite(id) {
    try {
        const response = await fetch(`php/clienti/get_cliente.php?id=${id}`);
        const cliente = await response.json();

        if (cliente.error) {
            alert(cliente.error);
            return;
        }

        // Impostiamo un attributo sul form per indicare che siamo in modifica (provenienza ospiti)
        const form = document.getElementById('formCliente');
        if (form) {
            form.setAttribute('data-provenienza', 'ospiti');
        }

        // Valorizziamo l'ID nascosto per la modifica
        const hiddenId = document.getElementById('clienteIdModale');
        if (hiddenId) hiddenId.value = cliente.id || '';

        // Popoliamo tutti i campi...
        const setFieldValue = (fieldId, value) => {
            const el = document.getElementById(fieldId);
            if (el) el.value = value || '';
        };

        setFieldValue('nome', cliente.nome);
        setFieldValue('cognome', cliente.cognome);
        setFieldValue('codice_fiscale', cliente.codice_fiscale);
        setFieldValue('sesso', cliente.sesso);
        setFieldValue('data_nascita', cliente.data_nascita);
        setFieldValue('luogo_nascita', cliente.luogo_nascita);
        setFieldValue('provincia_nascita', cliente.provincia_nascita);
        setFieldValue('tipo_documento', cliente.tipo_documento);
        setFieldValue('numero_documento', cliente.numero_documento);
        setFieldValue('rilasciato_da', cliente.rilasciato_da);
        setFieldValue('data_scadenza', cliente.data_scadenza);
        setFieldValue('email', cliente.email);
        setFieldValue('cellulare', cliente.cellulare);
        setFieldValue('indirizzo', cliente.indirizzo);
        setFieldValue('citta', cliente.citta);
        setFieldValue('note', cliente.note);

        // Apriamo la modale del cliente
        const modalEl = document.getElementById('clienteModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

    } catch (error) {
        console.error('Errore nel recupero dei dati del cliente:', error);
        alert('Impossibile caricare i dati del cliente per la modifica.');
    }
}