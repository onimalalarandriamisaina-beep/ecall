<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('admin');

$pdo = getPDO();

$totalAgents  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='agent'")->fetchColumn();
$totalClients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$totalAdmins  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

$recentUsers  = $pdo->query("SELECT nom, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 8")->fetchAll();

$pageTitle  = 'Dashboard Admin';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
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

.stat-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
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

.stat-icon.admin {
    background: rgba(147, 59, 91, 0.12);
    color: var(--c-primary);
}

.stat-icon.agent {
    background: rgba(107, 155, 212, 0.12);
    color: var(--c-info);
}

.stat-icon.client {
    background: rgba(139, 168, 138, 0.12);
    color: var(--c-success);
}

.stat-icon.total {
    background: rgba(226, 184, 107, 0.12);
    color: var(--c-warning);
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
    font-weight: 500;
    margin-top: 0.2rem;
}

.ecall-table {
    width: 100%;
    background: var(--c-surface);
    border-collapse: collapse;
    border-radius: var(--radius);
    overflow: hidden;
}

.ecall-table thead th {
    background: #FBF9F5;
    color: var(--c-text);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--c-border);
}

.ecall-table tbody td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--c-border);
    color: var(--c-text);
}

.ecall-table tbody tr:hover td {
    background: #FBF9F5;
}

.role-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-weight: 600;
}

.badge-pill-admin {
    background: rgba(147, 59, 91, 0.12);
    color: var(--c-primary);
}

.badge-pill-agent {
    background: rgba(107, 155, 212, 0.12);
    color: var(--c-info);
}

.badge-pill-client {
    background: rgba(139, 168, 138, 0.12);
    color: var(--c-success);
}

.panel {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-top: 1.5rem;
}

.panel-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--c-border);
    background: #FBF9F5;
}

.panel-title {
    font-weight: 600;
    color: var(--c-text);
}
</style>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon admin">👑</div>
            <div>
                <div class="stat-value"><?= $totalAdmins ?></div>
                <div class="stat-label">Administrateurs</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon agent">🎧</div>
            <div>
                <div class="stat-value"><?= $totalAgents ?></div>
                <div class="stat-label">Agents</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon client">👤</div>
            <div>
                <div class="stat-value"><?= $totalClients ?></div>
                <div class="stat-label">Clients</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon total">📊</div>
            <div>
                <div class="stat-value"><?= $totalAgents + $totalClients + $totalAdmins ?></div>
                <div class="stat-label">Total utilisateurs</div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">📋 Derniers utilisateurs inscrits</span>
    </div>
    <div class="panel-body p-0">
        <table class="ecall-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentUsers as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="role-badge badge-pill-<?= $u['role'] ?>">
                            <?= strtoupper($u['role']) ?>
                        </span>
                    </td>
                    <td style="color: var(--c-text-light); font-size:0.8rem;">
                        <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>