<?php
// includes/modals/modal_impostazioni.php
require_once __DIR__ . '/../../php/config/db.php';

$struttura_id = $_SESSION['struttura_id'] ?? 1;

// Valori predefiniti di sicurezza nel caso la tabella o il record non esistano
$modalitaAttuale = 'mock';
$apiKeyAttuale = '';

try {
    $stmt = $pdo->prepare("SELECT nome, modalita_ocr, api_key_ocr FROM strutture WHERE id = :id");
    $stmt->execute([':id' => $struttura_id]);
    $strutturaConf = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($strutturaConf) {
        $modalitaAttuale = $strutturaConf['modalita_ocr'] ?? 'mock';
        $apiKeyAttuale = $strutturaConf['api_key_ocr'] ?? '';
    }
} catch (PDOException $e) {
    // Se la tabella non esiste ancora, catturiamo l'errore senza bloccare l'intera pagina index.php
    $modalitaAttuale = 'mock';
    $apiKeyAttuale = '';
}
?>

<!-- Modal Impostazioni -->
<div class="modal fade" id="impostazioniModal" tabindex="-1" aria-labelledby="impostazioniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <form id="formImpostazioni" method="POST" action="php/salva_impostazioni.php">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="impostazioniModalLabel"><i class="bi bi-gear-fill me-2"></i>Impostazioni Struttura & OCR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    
                    <!-- Selezione Modalità OCR -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Seleziona Provider OCR per il Check-in</label>
                        
                        <div class="form-check p-3 border rounded mb-2 bg-white shadow-sm">
                            <input class="form-check-input" type="radio" name="modalita_ocr" id="ocrMockModal" value="mock" <?= $modalitaAttuale === 'mock' ? 'checked' : '' ?>>
                            <label class="form-check-label w-100 ps-2" for="ocrMockModal">
                                <strong>Piano Base (Gratuito / Mock)</strong>
                                <div class="text-muted small">Simulazione lettura documento con dati di test predefiniti (zero costi).</div>
                            </label>
                        </div>

                        <div class="form-check p-3 border rounded mb-2 bg-white shadow-sm">
                            <input class="form-check-input" type="radio" name="modalita_ocr" id="ocrOpenAIModal" value="openai" <?= $modalitaAttuale === 'openai' ? 'checked' : '' ?>>
                            <label class="form-check-label w-100 ps-2" for="ocrOpenAIModal">
                                <strong>OpenAI (GPT-4o / Vision)</strong>
                                <div class="text-muted small">Riconoscimento reale ad alta precisione tramite API Key di OpenAI.</div>
                            </label>
                        </div>

                        <div class="form-check p-3 border rounded bg-white shadow-sm">
                            <input class="form-check-input" type="radio" name="modalita_ocr" id="ocrGeminiModal" value="gemini" <?= $modalitaAttuale === 'gemini' ? 'checked' : '' ?>>
                            <label class="form-check-label w-100 ps-2" for="ocrGeminiModal">
                                <strong>Google Gemini Vision</strong>
                                <div class="text-muted small">Scansione reale tramite Google AI Studio API.</div>
                            </label>
                        </div>
                    </div>

                    <!-- Campo API Key -->
                    <div class="mb-3" id="sectionApiKeyModal">
                        <label for="api_key_ocr_modal" class="form-label fw-bold">API Key Provider</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="api_key_ocr_modal" name="api_key_ocr" 
                                   value="<?= htmlspecialchars($apiKeyAttuale) ?>" 
                                   placeholder="es. sk-proj-..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="toggleApiKeyModal">👁️</button>
                        </div>
                        <div class="form-text">
                            La chiave viene memorizzata in modo sicuro sul server ed è utilizzata esclusivamente per l'estrazione dati dei documenti.
                        </div>
                    </div>

                    <div id="impostazioniAlert" class="mt-3"></div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary px-4">💾 Salva Impostazioni</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
// Script di supporto visivo dentro la modale
document.getElementById('toggleApiKeyModal')?.addEventListener('click', function() {
    const input = document.getElementById('api_key_ocr_modal');
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
});
</script>