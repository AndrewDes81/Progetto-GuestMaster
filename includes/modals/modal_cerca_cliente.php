<!-- Modal Cerca Cliente -->
<div class="modal fade" id="cercaClienteModal" tabindex="-1" aria-labelledby="cercaClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="cercaClienteModalLabel">🔍 Cerca Cliente e Storico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>

            <div class="modal-body p-3 p-md-4">
                <!-- Campo di ricerca dinamico -->
                <div class="mb-3">
                    <label for="inputFiltroCliente" class="form-label fw-semibold text-secondary">Cerca per Nome, Telefono o Codice Fiscale:</label>
                    <input type="text" id="inputFiltroCliente" class="form-control form-control-lg bg-light" placeholder="Digita qui per cercare...">
                </div>

                <!-- Lista Clienti a Card (Responsive) -->
                <div id="listaClientiTrovati" class="row g-2" style="max-height: 320px; overflow-y: auto;">
                    <div class="col-12 text-center text-muted py-4">
                        Inizia a digitare per cercare un cliente...
                    </div>
                </div>

                <!-- Sezione Storico Soggiorni -->
                <div id="sezioneStoricoCliente" class="mt-4 pt-3 border-top" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">📁 Storico Soggiorni: <span id="nomeClienteSelezionatoStorico" class="text-primary"></span></h6>
                    </div>
                    
                    <div id="tabellaStoricoSoggiorni" class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                        <!-- Le schede dello storico verranno inserite qui -->
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>

        </div>
    </div>
</div>