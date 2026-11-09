document.addEventListener('DOMContentLoaded', function () {
    const modalOspiti = document.getElementById('gestioneOspitiModal');

    // Quando il modale viene aperto, carichiamo gli ospiti della struttura attiva
    if (modalOspiti) {
        modalOspiti.addEventListener('show.bs.modal', function () {
            caricaOspiti();
        });
    }
});

// Funzione per recuperare gli ospiti dal backend filtrati per struttura
function caricaOspiti() {
    // Leggiamo la struttura attiva dal selettore globale che abbiamo sistemato prima
    const selectStruttura = document.getElementById('selectStrutturaGlobal');
    const strutturaId = selectStruttura ? selectStruttura.value : 'all';

    // Chiamata AJAX al backend PHP (es. get_ospiti.php?struttura_id=...)
    fetch(`backend/get_ospiti.php?struttura_id=${strutturaId}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabellaOspitiBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Nessun ospite trovato per questa struttura.</td></tr>`;
                return;
            }

            data.forEach(ospite => {
                let tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ospite.nome}</td>
                    <td>${ospite.cognome}</td>
                    <td>${ospite.email || '-'}</td>
                    <td>${ospite.cellulare || '-'}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="apriModificaOspite(${ospite.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminaOspite(${ospite.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Errore nel caricamento degli ospiti:', error);
            document.getElementById('tabellaOspitiBody').innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Errore durante il caricamento dei dati.</td></tr>`;
        });
}

function apriModificaOspite(id) {
    console.log("Modifica ospite con ID:", id);
    // Qui puoi aprire un sott-modale o popolare un form di modifica
}

function eliminaOspite(id) {
    if (confirm("Sei sicuro di voler eliminare questo ospite?")) {
        console.log("Elimina ospite con ID:", id);
        // Chiamata AJAX per l'eliminazione
    }
}