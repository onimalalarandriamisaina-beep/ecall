<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('admin');

$pdo     = getPDO();
$error   = '';
$success = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='client'")->execute([$id]);
    $success = 'Client supprimé.';
}

$editClient = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $st = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='client'");
    $st->execute([$id]);
    $editClient = $st->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom']       ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = trim($_POST['password']  ?? '');
    $editId    = (int)($_POST['edit_id']  ?? 0);

    if (!$nom || !$email) {
        $error = 'Nom et email obligatoires.';
    } elseif ($editId) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=?,password=? WHERE id=? AND role='client'")
                ->execute([$nom, $email, $telephone, $hash, $editId]);
        } else {
            $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=? WHERE id=? AND role='client'")
                ->execute([$nom, $email, $telephone, $editId]);
        }
        $success    = 'Client mis à jour.';
        $editClient = null;
    }
}

$clients = $pdo->query("SELECT * FROM users WHERE role='client' ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Gestion des Clients';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'clients';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($error):   ?><div class="alert-ecall error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert-ecall success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<?php if ($editClient): ?>
<div class="panel mb-4" style="max-width:480px;">
  <div class="panel-header"><span class="panel-title">✏️ Modifier Client</span></div>
  <div class="panel-body">
    <form method="POST" action="">
      <input type="hidden" name="edit_id" value="<?= $editClient['id'] ?>">
      <div class="mb-3">
        <label class="form-label">Nom complet</label>
        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($editClient['nom']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editClient['email']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($editClient['telephone'] ?? '') ?>">
      </div>
      <div class="mb-4">
        <label class="form-label">Nouveau mot de passe (optionnel)</label>
        <input type="password" name="password" class="form-control" placeholder="Laisser vide = inchangé">
      </div>
      <button type="submit" class="btn-ecall">Enregistrer</button>
      <a href="/ecall/admin/crud_clients.php" class="d-block text-center text-muted mt-2 text-decoration-none" style="font-size:.85rem;">Annuler</a>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <span class="panel-title">👤 Liste des clients (<?= count($clients) ?>)</span>
  </div>
  <div class="panel-body p-0">
    <table class="ecall-table">
      <thead>
        <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Inscription</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($clients as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['nom']) ?></td>
          <td><?= htmlspecialchars($c['email']) ?></td>
          <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
          <td class="text-muted" style="font-size:.8rem;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
          <td>
            <a href="?edit=<?= $c['id'] ?>" class="btn-edit-ecall me-1">Éditer</a>
            <a href="?delete=<?= $c['id'] ?>" class="btn-danger-ecall"
               onclick="return confirm('Supprimer ce client ?')">Suppr.</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$clients): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Aucun client.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
