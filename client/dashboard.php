<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('client');

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$client = $stmt->fetch();

$totalAppels = $pdo->query("SELECT COUNT(*) FROM appels WHERE client_id = " . $_SESSION['user_id'])->fetchColumn();
$appelsEnCours = $pdo->query("SELECT COUNT(*) FROM appels WHERE client_id = " . $_SESSION['user_id'] . " AND statut = 'en_cours'")->fetchColumn();
$appelsTermines = $pdo->query("SELECT COUNT(*) FROM appels WHERE client_id = " . $_SESSION['user_id'] . " AND statut = 'termine'")->fetchColumn();

$pageTitle = 'Dashboard Client';
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_nom'];
$activeMenu = 'dash';
include __DIR__ . '/../includes/header.php';
?>

<style>

:root {
    --c-primary: #933B5B;
    --c-primary-dark: #7A2F4C;
    --c-primary-light: #B5728A;
    --c-bg: #F5F2EB;
    --c-surface: #FFFFFF;
    --c-border: #E3D6BF;
    --c-text: #2D2A24;
    --c-text-light: #6B6556;
    --c-success: #8BA88A;
    --c-danger: #D46B6B;
    --c-warning: #E2B86B;
    --radius: 12px;
    --radius-lg: 20px;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--c-text);
    margin-bottom: 2rem;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.stat-icon.total {
    background: rgba(147, 59, 91, 0.12);
    color: var(--c-primary);
}

.stat-icon.en-cours {
    background: rgba(226, 184, 107, 0.12);
    color: var(--c-warning);
}

.stat-icon.termine {
    background: rgba(139, 168, 138, 0.12);
    color: var(--c-success);
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--c-text);
    line-height: 1.2;
}

.stat-label {
    color: var(--c-text-light);
    font-size: 0.8rem;
    margin-top: 0.2rem;
}

.row-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.panel {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    overflow: hidden;
}

.panel-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--c-border);
    background: #FBF9F5;
}

.panel-title {
    font-weight: 600;
    color: var(--c-text);
    font-size: 1rem;
}

.panel-body {
    padding: 1.5rem;
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-row {
    display: flex;
    align-items: center;
    padding: 0.8rem 0;
    border-bottom: 1px solid var(--c-border);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    width: 130px;
    font-weight: 600;
    color: var(--c-text-light);
    font-size: 0.85rem;
}

.info-value {
    flex: 1;
    color: var(--c-text);
    font-weight: 500;
    word-break: break-word;
}

.btn-call {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: var(--c-primary);
    color: white;
    border: none;
    border-radius: var(--radius);
    padding: 0.8rem 2rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s;
    cursor: pointer;
}

.btn-call:hover {
    background: var(--c-primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-logout {
    background: rgba(212, 107, 107, 0.1);
    color: var(--c-danger);
    border: 1px solid rgba(212, 107, 107, 0.3);
    padding: 0.4rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.btn-logout:hover {
    background: rgba(212, 107, 107, 0.2);
}

.text-center {
    text-align: center;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .row-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .info-label {
        width: 100px;
        font-size: 0.8rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="dashboard-container">
    <h1 class="page-title">Tableau de bord Client</h1>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon total">📞</div>
            <div class="stat-info">
                <div class="stat-value"><?= $totalAppels ?></div>
                <div class="stat-label">Total des appels</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon en-cours">🟡</div>
            <div class="stat-info">
                <div class="stat-value"><?= $appelsEnCours ?></div>
                <div class="stat-label">Appels en cours</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon termine">✅</div>
            <div class="stat-info">
                <div class="stat-value"><?= $appelsTermines ?></div>
                <div class="stat-label">Appels terminés</div>
            </div>
        </div>
    </div>

    <div class="row-grid">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">📞 Lancer un appel</span>
            </div>
            <div class="panel-body text-center">
                <a href="/ecall/client/appel.php" class="btn-call">
                    🟢 Lancer un appel
                </a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">👤 Mes informations</span>
            </div>
            <div class="panel-body">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value"><?= htmlspecialchars($client['nom']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($client['email']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value"><?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Membre depuis</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($client['created_at'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>