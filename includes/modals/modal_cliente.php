<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuovo Cliente / Registrazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>

            <form id="formCliente">
                <div class="modal-body">
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

                    <div class="mb-3">
                        <label class="form-label">Codice Fiscale *</label>
                        <input type="text" name="codice_fiscale" id="codice_fiscale" class="form-control text-uppercase" required>
                        <div class="form-text">Digitando il codice fiscale il sistema cercherà se il cliente esiste già.</div>
                    </div>

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
                            <label class="form-label">Indirizzo</label>
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

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary">Salva e Procedi alla Prenotazione</button>
                </div>
            </form>
        </div>
    </div>
</div>