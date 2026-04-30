<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('admin');

$pdo = getPDO();

$totalAgents  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='agent'")->fetchColumn();
$totalClients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$totalAdmins  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

$recentUsers  = $pdo->query("SELECT nom, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 8")->fetchAll();

$pageTitle  = 'Dashboard';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'dash';
include __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon purple">👑</div>
      <div>
        <div class="stat-value"><?= $totalAdmins ?></div>
        <div class="stat-label">Administrateurs</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon cyan">🎧</div>
      <div>
        <div class="stat-value"><?= $totalAgents ?></div>
        <div class="stat-label">Agents</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon green">👤</div>
      <div>
        <div class="stat-value"><?= $totalClients ?></div>
        <div class="stat-label">Clients</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon red">📋</div>
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
          <td class="text-muted" style="font-size:.8rem;"><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
