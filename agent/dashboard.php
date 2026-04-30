<?php
// agent/dashboard.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('agent');

$pdo     = getPDO();
$stmt    = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$agent   = $stmt->fetch();

$totalClients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();

$pageTitle  = 'Dashboard Agent';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'dash';
include __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon cyan">🎧</div>
      <div>
        <div class="stat-value">En ligne</div>
        <div class="stat-label">Statut agent</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon green">👤</div>
      <div>
        <div class="stat-value"><?= $totalClients ?></div>
        <div class="stat-label">Clients enregistrés</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="stat-card">
      <div class="stat-icon purple">📅</div>
      <div>
        <div class="stat-value"><?= date('d/m') ?></div>
        <div class="stat-label">Date du jour</div>
      </div>
    </div>
  </div>
</div>

<div class="panel" style="max-width:560px;">
  <div class="panel-header"><span class="panel-title">👤 Mes informations</span></div>
  <div class="panel-body">
    <table class="ecall-table">
      <tbody>
        <tr><td class="text-muted" style="width:140px;">Nom</td><td><?= htmlspecialchars($agent['nom']) ?></td></tr>
        <tr><td class="text-muted">Email</td><td><?= htmlspecialchars($agent['email']) ?></td></tr>
        <tr><td class="text-muted">Téléphone</td><td><?= htmlspecialchars($agent['telephone'] ?? '-') ?></td></tr>
        <tr><td class="text-muted">Rôle</td><td><span class="role-badge badge-pill-agent">AGENT</span></td></tr>
        <tr><td class="text-muted">Membre depuis</td><td><?= date('d/m/Y', strtotime($agent['created_at'])) ?></td></tr>
      </tbody>
    </table>
    <div class="mt-3">
      <a href="/ecall/agent/profile.php" class="btn-edit-ecall text-decoration-none">✏️ Modifier mon profil</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
