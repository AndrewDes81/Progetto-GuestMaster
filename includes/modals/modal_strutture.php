<div class="modal fade" id="strutturaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Gestione Struttura e Stanze</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- COLONNA SINISTRA: Form Anagrafica Struttura -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary mb-3"><i class="bi bi-building"></i> Anagrafica Struttura</h6>
                        <form id="formStruttura">
                            <input type="hidden" name="id" id="struttura_id_edit">

                            <div class="mb-3">
                                <label class="form-label">Nome Struttura *</label>
                                <input type="text" name="nome" id="strutturaNome" class="form-control" required placeholder="Es. B&B Centro Storico">
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Indirizzo</label>
                                    <input type="text" name="indirizzo" id="strutturaIndirizzo" class="form-control" placeholder="Via Roma 10">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CAP</label>
                                    <input type="text" name="cap" id="strutturaCap" class="form-control" placeholder="73100">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Città</label>
                                    <input type="text" name="citta" id="strutturaCitta" class="form-control" placeholder="Lecce">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Provincia</label>
                                    <input type="text" name="provincia" id="strutturaProvincia" class="form-control" maxlength="2" placeholder="LE">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Codice CIN</label>
                                    <input type="text" name="codice_cin" id="strutturaCin" class="form-control" placeholder="IT...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Codice CIR</label>
                                    <input type="text" name="codice_cir" id="strutturaCir" class="form-control" placeholder="...">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Modalità OCR</label>
                                    <select name="modalita_ocr" id="strutturaOcr" class="form-select">
                                        <option value="mock">Mock (Simulato)</option>
                                        <option value="openai">OpenAI (GPT-4o)</option>
                                        <option value="gemini">Gemini</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">API Key OCR (opzionale)</label>
                                    <input type="password" name="api_key_ocr" id="strutturaApiKey" class="form-control" placeholder="sk-...">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Salva Struttura</button>
                        </form>
                    </div>

                    <!-- COLONNA DESTRA: Gestione Stanze della Struttura Selezionata -->
                    <div class="col-md-6">
                        <h6 class="text-success mb-3"><i class="bi bi-door-open"></i> Stanze Associate</h6>
                        
                        <!-- Avviso se non è stata salvata/selezionata una struttura -->
                        <div id="avvisoSelezionaStruttura" class="alert alert-warning">
                            Salva o seleziona prima una struttura per poter aggiungere le stanze.
                        </div>

                        <div id="sezioneStanze" style="display: none;">
                            <form id="formStanza" class="card card-body bg-light mb-3">
                                <input type="hidden" name="struttura_id" id="stanzaStrutturaId">
                                <div class="mb-2">
                                    <label class="form-label small">Nome Stanza / Appartamento *</label>
                                    <input type="text" name="nome_stanza" class="form-control form-control-sm" required placeholder="Es. Camera Doppia 101">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Tipologia</label>
                                        <input type="text" name="tipologia" class="form-control form-control-sm" value="Matrimoniale">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Posti Letto</label>
                                        <input type="number" name="posti_letto" class="form-control form-control-sm" value="2" min="1">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Prezzo Notte Base (€)</label>
                                    <input type="number" step="0.01" name="prezzo_notte_base" class="form-control form-control-sm" value="80.00">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-plus-circle"></i> Aggiungi Stanza</button>
                            </form>

                            <!-- Elenco delle stanze già inserite per questa struttura -->
                            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Stanza</th>
                                            <th>Tipologia</th>
                                            <th>Letti</th>
                                            <th>Prezzo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaStanzeTabel">
                                        <!-- Popolato via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>