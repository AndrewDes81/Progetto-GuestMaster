<!-- Modale Gestione Ospiti -->
<div class="modal fade" id="gestioneOspitiModal" tabindex="-1" aria-labelledby="gestioneOspitiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="gestioneOspitiModalLabel">
            <i class="bi bi-people-fill text-primary"></i> Gestione Anagrafica Ospiti
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        
        <!-- Barra di ricerca o pulsante nuovo ospite interno -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input type="text" id="cercaOspiteInput" class="form-control form-control-sm w-25" placeholder="Cerca ospite...">
            <button type="button" class="btn btn-sm btn-success" id="btnNuovoOspite">
                <i class="bi bi-plus-lg"></i> Aggiungi Ospite
            </button>
        </div>

        <!-- Tabella Contenitore Ospiti (popolata via JS) -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Email</th>
                <th>Telefono</th>
                <th class="text-end">Azioni</th>
              </tr>
            </thead>
            <tbody id="tabellaOspitiBody">
              <!-- I dati verranno inseriti dinamicamente da ospiti.js -->
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Caricamento ospiti in corso...</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>
