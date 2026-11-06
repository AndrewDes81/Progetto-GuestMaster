<?php
// index.php
require_once 'php/config/db.php';
require_once 'includes/statistiche.php';

$strutturaSelezionata = isset($_GET['struttura_id']) ? $_GET['struttura_id'] : 'all';
$listaStrutture = [];

try {
    // Eseguiamo la query e forziamo il recupero dei dati
    $stmtStrutture = $pdo->query("SELECT id, nome FROM strutture ORDER BY nome ASC");
    $listaStrutture = $stmtStrutture->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Se c'è un errore nella query, lo stampiamo a schermo per capire cos'è
    echo "<div style='color: red; padding: 10px; background: #ffe6e6;'>Errore database: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔑 GuestMaster – Property & Reservation Manager</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Esterno Principale -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS calendario -->
    <link rel="stylesheet" href="assets/css/calendario.css">
</head>

<body>

    <!-- 1. TOP NAVBAR A TUTTA LARGHEZZA IN CIMA -->
    <header class="top-navbar d-flex align-items-center justify-content-between shadow-sm px-3">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <!-- Pulsante Hamburger Mobile -->
            <button class="btn text-white d-lg-none p-1" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
            
            <!-- Logo + Brand -->
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="brand-key-icon">🔑</span>
                <div class="d-flex flex-column justify-content-center">
                    <span class="brand-title">GuestMaster</span>
                    <span class="brand-subtitle d-none d-sm-block">Property &amp; Reservation Manager</span>
                </div>
            </a>
        </div>

        <!-- Lato Destro: Notifiche e Avatar -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-light btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center text-secondary border-0 shadow-sm" style="width: 36px; height: 36px; min-width: 36px;">
                <i class="bi bi-bell fs-6"></i>
            </button>

            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small shadow-sm" style="width: 36px; height: 36px; min-width: 36px; background-color: #B7C5CF; color: #29577B !important;">
                GM
            </div>
        </div>
    </header>

    <!-- 2. SIDEBAR FISSA A SINISTRA -->
    <aside class="sidebar shadow-sm" id="sidebarMenu">
            
        <!-- Header interno alla Sidebar per Mobile -->
        <div class="sidebar-header-mobile d-lg-none">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">🔑</span>
                <div class="d-flex flex-column">
                    <span class="fw-bold fs-6 lh-1" style="color: #29577B;">GuestMaster</span>
                    <small class="text-muted" style="font-size: 0.65rem;">Property Manager</small>
                </div>
            </div>
            <button type="button" class="btn-close text-reset" id="sidebarCloseBtn" aria-label="Close"></button>
        </div>

        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- AZIONI RAPIDE A TENDINA -->
            <li class="nav-item mt-3">
                <a class="nav-link d-flex justify-content-between align-items-center text-dark bg-light fw-semibold" data-bs-toggle="collapse" href="#azioniRapideMenu" role="button" aria-expanded="false" aria-controls="azioniRapideMenu">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        <span>Azioni Rapide</span>
                    </span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                
                <div class="collapse ps-3 mt-1" id="azioniRapideMenu">
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item">
                            <a href="#" class="nav-link py-1 text-secondary small" id="btnCercaClienteDesktop">
                                <i class="bi bi-search"></i> Cerca Cliente
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Flusso booking-first via modale cliente -->
                            <a href="#" class="nav-link py-1 text-success small" data-bs-toggle="modal" data-bs-target="#clienteModal">
                                <i class="bi bi-calendar-plus-fill"></i> Nuova Prenotazione
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link py-1 text-primary small" data-bs-toggle="modal" data-bs-target="#strutturaModal">
                                <i class="bi bi-house-door-fill"></i> Nuova Struttura
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item mt-3">
                <span class="px-3 text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Gestione</span>
            </li>
            <li class="nav-item mt-1">
                <a href="#" class="nav-link">
                    <i class="bi bi-calendar-check"></i>
                    <span>Prenotazioni</span>
                </a>
            </li>
            <li class="nav-item">
                <!-- COLLEGATO AL MODALE GESTIONE STRUTTURE -->
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#gestioneStruttureModal">
                    <i class="bi bi-house-door"></i>
                    <span>Strutture</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Ospiti</span>
                </a>
            </li>
        </ul>

        <hr class="my-3 text-muted">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#impostazioniModal">
                    <i class="bi bi-gear"></i>
                    <span>Impostazioni</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Overlay scuro per mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- 3. WRAPPER CONTENUTO PRINCIPALE -->
    <div class="main-wrapper">
        <main class="dashboard-content">
            
            <!-- RIGA SUPERIORE: INCASSI + SELETTORE STRUTTURA -->
            <div class="row align-items-center g-2 g-md-3 mb-3">
                <!-- Incassi Mese -->
                <div class="col-6 col-md-3" style="max-width: 220px;">
                    <div class="card shadow-sm border-0 py-2 px-3 bg-white h-100">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.65rem;">Incassi Mese</span>
                        <h5 class="fw-bold mb-0 mt-1" style="color: #29577B;">
                            € <?= isset($totaleMeseIncassato) ? number_format($totaleMeseIncassato, 2, ',', '.') : '0,00' ?>
                        </h5>
                    </div>
                </div>
                
                <!-- Incassi Anno -->
                <div class="col-6 col-md-3" style="max-width: 220px;">
                    <div class="card shadow-sm border-0 py-2 px-3 bg-white h-100">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.65rem;">Incassi Anno</span>
                        <h5 class="fw-bold mb-0 mt-1" style="color: #29577B;">
                            € <?= isset($totaleAnnoIncassato) ? number_format($totaleAnnoIncassato, 2, ',', '.') : '0,00' ?>
                        </h5>
                    </div>
                </div>

                <!-- Selettore Struttura Attiva Dinamico da DB -->
                <div class="col-12 col-md-6 col-lg-4 ms-auto">
                    <div class="card shadow-sm border-0 py-2 px-3 bg-white">
                        <div class="w-100">
                            <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.65rem;">Struttura Attiva</span>
                            <select id="selectStrutturaGlobal" name="struttura_id" class="form-select form-select-sm fw-bold border-0 p-0 text-dark shadow-none bg-transparent" style="cursor: pointer; font-size: 0.9rem;">
                                <option value="all" <?= ($strutturaSelezionata == 'all') ? 'selected' : '' ?>>🏢 Tutte le strutture</option>
                                <?php foreach ($listaStrutture as $struttura): ?>
                                    <option value="<?= $struttura['id'] ?>" <?= ($strutturaSelezionata == $struttura['id']) ? 'selected' : '' ?>>
                                        🏡 <?= htmlspecialchars($struttura['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD CALENDARIO CON FILTRI ANNO E MESE CENTRATI -->
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-3 px-3 pb-1">
                    <div class="d-flex align-items-center justify-content-center gap-2 gap-sm-3 flex-wrap">
                        
                        <span class="text-muted small fw-semibold d-none d-sm-inline">Periodo:</span>

                        <!-- Selettore Mese -->
                        <div class="input-group input-group-sm" style="width: 160px;">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-month"></i></span>
                            <select id="mese" class="form-select form-select-sm fw-bold border-start-0 bg-light shadow-none">
                                <?php
                                $meseSelezionato = isset($_GET['mese']) ? (int)$_GET['mese'] : date('n');
                                $mesi = [
                                    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
                                    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
                                    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
                                ];
                                foreach ($mesi as $numero => $nome) {
                                    $selected = ($numero == $meseSelezionato) ? 'selected' : '';
                                    echo "<option value='$numero' $selected>$nome</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Selettore Anno -->
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                            <select id="anno" class="form-select form-select-sm fw-bold border-start-0 bg-light shadow-none text-center">
                                <?php
                                $annoSelezionato = isset($_GET['anno']) ? (int)$_GET['anno'] : date('Y');
                                for ($anno = date('Y') - 2; $anno <= date('Y') + 3; $anno++) {
                                    $selected = ($anno == $annoSelezionato) ? 'selected' : '';
                                    echo "<option value='$anno' $selected>$anno</option>";
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Corpo del Calendario -->
                <div class="card-body p-2 p-md-3">
                    <div id="calendar">
                        <?php 
                        if (file_exists('includes/calendario.php')) {
                            include 'includes/calendario.php'; 
                        } else {
                            echo '<p class="text-muted text-center my-3">File calendario.php non trovato.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- LEGENDA DINAMICA -->
            <div class="d-flex flex-wrap gap-3 mt-3 px-1">
                <?php foreach ($listaStrutture as $struttura): ?>
                    <small class="d-flex align-items-center gap-1 text-muted">
                        <span class="badge rounded-circle p-1" style="background-color: <?= !empty($struttura['colore']) ? htmlspecialchars($struttura['colore']) : '#29577B' ?>;"> </span> 
                        <?= htmlspecialchars($struttura['nome']) ?>
                    </small>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- MODALI -->
    <?php
    if (file_exists('includes/modals/modal_cliente.php')) { include 'includes/modals/modal_cliente.php'; }
    if (file_exists('includes/modals/modal_prenotazione.php')) { include 'includes/modals/modal_prenotazione.php'; }
    if (file_exists('includes/modals/modal_dettaglio.php')) { include 'includes/modals/modal_dettaglio.php'; }
    if (file_exists('includes/modals/modal_cerca_cliente.php')) { include 'includes/modals/modal_cerca_cliente.php'; }
    if (file_exists('includes/modals/modal_impostazioni.php')) { include 'includes/modals/modal_impostazioni.php'; }
    if (file_exists('includes/modals/modal_strutture.php')) { include 'includes/modals/modal_strutture.php'; }
    if (file_exists('includes/modals/modal_gestione_strutture.php')) { include 'includes/modals/modal_gestione_strutture.php'; }
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/clienti.js"></script>
    <script src="assets/js/cerca_cliente.js"></script>
    <script src="assets/js/prenotazioni.js"></script>
    <script src="assets/js/calendario.js"></script>
    <script src="assets/js/strutture.js"></script>

    <script>
        const sidebar = document.getElementById('sidebarMenu');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        toggleBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);
        closeBtn?.addEventListener('click', toggleSidebar);
    </script>
</body>

</html>