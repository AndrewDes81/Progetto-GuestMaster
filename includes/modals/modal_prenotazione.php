<div class="modal fade" id="prenotazioneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuova Prenotazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            
            <form id="formPrenotazione">
                <div class="modal-body">
                    <!-- Campo nascosto per l'ID della prenotazione (necessario per la modifica) -->
                    <input type="hidden" name="id" id="prenotazione_id">
                    <!-- Campo nascosto per associare l'ID del cliente -->
                    <input type="hidden" name="cliente_id" id="prenotazione_cliente_id">

                    <div class="mb-3">
                        <label class="form-label text-muted">Cliente Associato</label>
                        <input type="text" id="infoClienteSelezionato" class="form-control" readonly value="Ultimo cliente registrato">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dal (Check-in)</label>
                            <input type="date" name="dal" id="dataDal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Al (Check-out)</label>
                            <input type="date" name="al" id="dataAl" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Costo Totale (€)</label>
                            <input type="number" step="0.01" name="costo_totale" id="costoTotale" class="form-control" value="0.00" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Caparra (€)</label>
                            <input type="number" step="0.01" name="caparra" id="caparra" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Saldo (€ - Auto)</label>
                            <input type="number" step="0.01" name="saldo" id="saldo" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stato Prenotazione</label>
                        <select name="stato" class="form-select" required>
                            <option value="PRENOTATA">Prenotata</option>
                            <option value="CAPARRA_VERSATA" selected>Caparra Versata</option>
                            <option value="SALDO_PAGATO">Saldo Pagato</option>
                            <option value="CONCLUSA">Conclusa</option>
                            <option value="ANNULLATA">Annullata</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Eventuali richieste o note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success">Salva Prenotazione</button>
                </div>
            </form>
        </div>
    </div>
</div>