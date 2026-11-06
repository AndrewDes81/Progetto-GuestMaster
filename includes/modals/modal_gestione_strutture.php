<div class="modal fade" id="gestioneStruttureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-buildings"></i> Gestione e Ricerca Strutture</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            
            <div class="modal-body bg-light">
                <!-- Barra di ricerca in tempo reale -->
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Cerca per Nome, Città o Codice CIN:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="inputCercaStrutturaCard" class="form-control" placeholder="Digita qui per cercare...">
                    </div>
                </div>

                <!-- Griglia delle Card Strutture (popolata dinamicamente via JS) -->
                <div class="row g-3" id="grigliaCardStrutture">
                    <!-- Le card verranno inserite qui dinamicamente -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>