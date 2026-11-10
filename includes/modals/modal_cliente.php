<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <!-- Aggiunto style max-height per vincolare il modale all'altezza dello schermo -->
        <div class="modal-content shadow-lg" style="max-height: 90vh;">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Registrazione Ospite / Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>

            <form id="formCliente" class="d-flex flex-column h-100" style="overflow: hidden;">
                <!-- Campo nascosto per gestire la modifica -->
                <input type="hidden" name="id" id="clienteIdModale">
                <!-- Aggiunto overflow-y: auto e max-height calcolata per attivare lo scrolling fluido -->
                <div class="modal-body" style="overflow-y: auto; max-height: calc(90vh - 130px);">
                    
                    <!-- SEZIONE 1: DATI ANAGRAFICI -->
                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-person me-1"></i> Dati Anagrafici</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" id="nome" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cognome *</label>
                            <input type="text" name="cognome" id="cognome" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Codice Fiscale</label>
                            <input type="text" name="codice_fiscale" id="codice_fiscale" class="form-control text-uppercase">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sesso</label>
                            <select name="sesso" id="sesso" class="form-select">
                                <option value="">Seleziona...</option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data di Nascita</label>
                            <input type="date" name="data_nascita" id="data_nascita" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Luogo di Nascita</label>
                            <input type="text" name="luogo_nascita" id="luogo_nascita" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provincia Nascita</label>
                            <input type="text" name="provincia_nascita" id="provincia_nascita" class="form-control" maxlength="2">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- SEZIONE 2: DOCUMENTO D'IDENTITÀ (Compilato da OCR / NFC) -->
                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-card-heading me-1"></i> Documento d'Identità</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo Documento</label>
                            <input type="text" name="tipo_documento" id="tipo_documento" class="form-control" placeholder="es. CARTA DI IDENTITA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Numero Documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rilasciato da</label>
                            <input type="text" name="rilasciato_da" id="rilasciato_da" class="form-control" placeholder="es. COMUNE DI ROMA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Scadenza Documento</label>
                            <input type="date" name="data_scadenza" id="data_scadenza" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- SEZIONE 3: CONTATTI E RESIDENZA -->
                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i> Contatti e Residenza</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cellulare</label>
                            <input type="text" name="cellulare" id="cellulare" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="con-label">Indirizzo Residenza</label>
                            <input type="text" name="indirizzo" id="indirizzo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Città</label>
                            <input type="text" name="citta" id="citta" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" id="note" class="form-control" rows="2"></textarea>
                    </div>

                    <div id="esitoCliente"></div>
                </div>

                <div class="modal-footer justify-content-between align-items-center flex-wrap bg-light">
                    <!-- Strumenti OCR e NFC a sinistra -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" id="btnNfc" class="btn btn-outline-primary btn-sm">📱 Leggi CIE NFC</button>
                        <button type="button" id="btnOcr" class="btn btn-outline-success btn-sm">📷 Scansiona Documento (OCR)</button>
                        <input type="file" id="inputOcrFile" accept="image/*" capture="environment" class="d-none">
                        
                        <small id="ocrStatus" class="text-muted"></small>
                        <small id="nfcStatus" class="text-muted"></small>
                    </div>

                    <!-- Pulsanti a destra -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary">Salva Cliente</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>