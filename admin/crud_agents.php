<?php
// admin/crud_agents.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('admin');

$pdo     = getPDO();
$error   = '';
$success = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='agent'")->execute([$id]);
    $success = 'Agent supprimé.';
}

// EDIT — load data
$editAgent = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $st = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='agent'");
    $st->execute([$id]);
    $editAgent = $st->fetch();
}

// POST: create or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom']       ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = trim($_POST['password']  ?? '');
    $editId    = (int)($_POST['edit_id']  ?? 0);

    if (!$nom || !$email) {
        $error = 'Nom et email obligatoires.';
    } else {
        if ($editId) {
            // UPDATE
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=?,password=? WHERE id=? AND role='agent'")
                    ->execute([$nom, $email, $telephone, $hash, $editId]);
            } else {
                $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=? WHERE id=? AND role='agent'")
                    ->execute([$nom, $email, $telephone, $editId]);
            }
            $success = 'Agent mis à jour.';
            $editAgent = null;
        } else {
            // CREATE
            if (!$password) { $error = 'Mot de passe obligatoire pour créer un agent.'; }
            else {
                $chk = $pdo->prepare('SELECT id FROM users WHERE email=?');
                $chk->execute([$email]);
                if ($chk->fetch()) {
                    $error = 'Email déjà utilisé.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("INSERT INTO users(nom,email,password,telephone,role) VALUES(?,?,?,?,'agent')")
                        ->execute([$nom, $email, $hash, $telephone]);
                    $success = 'Agent créé avec succès.';
                }
            }
        }
    }
}

$agents = $pdo->query("SELECT * FROM users WHERE role='agent' ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Gestion des Agents';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'agents';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($error):   ?><div class="alert-ecall error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert-ecall success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="row g-4">
  <!-- FORM -->
  <div class="col-lg-4">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title"><?= $editAgent ? '✏️ Modifier Agent' : '➕ Nouvel Agent' ?></span>
      </div>
      <div class="panel-body">
        <form method="POST" action="">
          <?php if ($editAgent): ?>
            <input type="hidden" name="edit_id" value="<?= $editAgent['id'] ?>">
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= htmlspecialchars($editAgent['nom'] ?? $_POST['nom'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($editAgent['email'] ?? $_POST['email'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="tel" name="telephone" class="form-control"
                   value="<?= htmlspecialchars($editAgent['telephone'] ?? '') ?>">
          </div>
          <div class="mb-4">
            <label class="form-label">Mot de passe <?= $editAgent ? '(laisser vide = inchangé)' : '*' ?></label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
          </div>
          <button type="submit" class="btn-ecall"><?= $editAgent ? 'Mettre à jour' : 'Créer l\'agent' ?></button>
          <?php if ($editAgent): ?>
            <a href="/ecall/admin/crud_agents.php" class="d-block text-center text-muted mt-2 text-decoration-none" style="font-size:.85rem;">Annuler</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="col-lg-8">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">🎧 Liste des agents (<?= count($agents) ?>)</span>
      </div>
      <div class="panel-body p-0">
        <table class="ecall-table">
          <thead>
            <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Créé le</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($agents as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['nom']) ?></td>
              <td><?= htmlspecialchars($a['email']) ?></td>
              <td><?= htmlspecialchars($a['telephone'] ?? '-') ?></td>
              <td class="text-muted" style="font-size:.8rem;"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <a href="?edit=<?= $a['id'] ?>" class="btn-edit-ecall me-1">Éditer</a>
                <a href="?delete=<?= $a['id'] ?>" class="btn-danger-ecall"
                   onclick="return confirm('Supprimer cet agent ?')">Suppr.</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$agents): ?>
              <tr><td colspan="5" class="text-center text-muted py-4">Aucun agent.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
