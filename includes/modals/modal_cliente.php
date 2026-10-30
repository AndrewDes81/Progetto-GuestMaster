<div
    class="modal fade"
    id="clienteModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Nuovo Cliente
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <form
                id="formCliente">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Nome
                            </label>

                            <input
                                type="text"
                                name="nome"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Cognome
                            </label>

                            <input
                                type="text"
                                name="cognome"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Codice Fiscale
                        </label>

                        <input
                            type="text"
                            name="codice_fiscale"
                            id="codice_fiscale"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Cellulare
                        </label>

                        <input
                            type="text"
                            name="cellulare"
                            class="form-control">

                    </div>

                    <div id="esitoCliente"></div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Chiudi

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Salva Cliente

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>