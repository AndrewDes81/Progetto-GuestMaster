<!-- MODALE DETTAGLIO PRENOTAZIONE -->
<div class="modal fade" id="dettaglioPrenotazioneModal" tabindex="-1" aria-labelledby="dettaglioPrenotazioneLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="dettaglioPrenotazioneLabel">🏠 Dettagli Soggiorno e Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dettaglioContenuto">
                <!-- I dati verranno inseriti dinamicamente via JS -->
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnEliminaPrenotazione">🗑️ Elimina</button>
                    <!-- Eventuale tasto modifica -->
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnModificaPrenotazione">✏️ Modifica</button>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>