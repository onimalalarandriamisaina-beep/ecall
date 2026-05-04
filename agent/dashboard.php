<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('agent');

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$agent = $stmt->fetch();

$totalClients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();

$pageTitle = 'Dashboard Agent';
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
    --c-secondary: #9F9679;   
    --c-secondary-light: #AABAAE;
    --c-bg: #F5F2EB;
    --c-surface: #FFFFFF;
    --c-border: #E3D6BF;
    --c-text: #2D2A24;
    --c-text-light: #6B6556;
    --c-text-muted: #9A9483;
    --c-success: #8BA88A;
    --c-danger: #D46B6B;
    --c-warning: #E2B86B;
    --c-info: #6B9BD4;
    --radius: 12px;
    --radius-lg: 20px;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
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

.stat-icon.agent {
    background: rgba(107, 155, 212, 0.12);
    color: var(--c-info);
}

.stat-icon.client {
    background: rgba(139, 168, 138, 0.12);
    color: var(--c-success);
}

.stat-icon.date {
    background: rgba(147, 59, 91, 0.12);
    color: var(--c-primary);
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--c-text);
    line-height: 1.2;
}

.stat-label {
    color: var(--c-text-light);
    font-size: 0.8rem;
    margin-top: 0.2rem;
}

.panel {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    overflow: hidden;
    max-width: 560px;
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

.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table tr {
    border-bottom: 1px solid var(--c-border);
}

.info-table tr:last-child {
    border-bottom: none;
}

.info-table td {
    padding: 0.8rem 0;
}

.info-table td:first-child {
    color: var(--c-text-light);
    font-weight: 500;
    width: 140px;
}

.info-table td:last-child {
    color: var(--c-text);
    font-weight: 500;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-pill-agent {
    background: rgba(107, 155, 212, 0.12);
    color: var(--c-info);
}

.btn-edit-ecall {
    display: inline-block;
    background: rgba(147, 59, 91, 0.1);
    color: var(--c-primary);
    border: 1px solid rgba(147, 59, 91, 0.3);
    border-radius: var(--radius);
    padding: 0.5rem 1rem;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-edit-ecall:hover {
    background: rgba(147, 59, 91, 0.2);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .panel {
        max-width: 100%;
    }
    
    .info-table td:first-child {
        width: 100px;
    }
}
</style>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon agent">🎧</div>
        <div class="stat-info">
            <div class="stat-value">En ligne</div>
            <div class="stat-label">Statut agent</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon client">👤</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalClients ?></div>
            <div class="stat-label">Clients enregistrés</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon date">📅</div>
        <div class="stat-info">
            <div class="stat-value"><?= date('d/m') ?></div>
            <div class="stat-label">Date du jour</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">👤 Mes informations</span>
    </div>
    <div class="panel-body">
        <table class="info-table">
            <tr>
                <td>Nom</td>
                <td><?= htmlspecialchars($agent['nom']) ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= htmlspecialchars($agent['email']) ?></td>
            </tr>
            <tr>
                <td>Téléphone</td>
                <td><?= htmlspecialchars($agent['telephone'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Rôle</td>
                <td><span class="role-badge badge-pill-agent">AGENT</span></td>
            </tr>
            <tr>
                <td>Membre depuis</td>
                <td><?= date('d/m/Y', strtotime($agent['created_at'])) ?></td>
            </tr>
        </table>
        
        <div class="mt-3" style="margin-top: 1rem;">
            <a href="/ecall/agent/informations.php" class="btn-edit-ecall">
                ✏️ Modifier mon profil
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>