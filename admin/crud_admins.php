<?php
// admin/crud_admins.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('admin');

$pdo     = getPDO();
$error   = '';
$success = '';

// DELETE — cannot delete yourself
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)$_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas supprimer votre propre compte.';
    } else {
        $pdo->prepare("DELETE FROM users WHERE id=? AND role='admin'")->execute([$id]);
        $success = 'Admin supprimé.';
    }
}

// EDIT
$editAdmin = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $st = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='admin'");
    $st->execute([$id]);
    $editAdmin = $st->fetch();
}

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom']      ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $editId   = (int)($_POST['edit_id'] ?? 0);

    if (!$nom || !$email) {
        $error = 'Nom et email obligatoires.';
    } elseif ($editId) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET nom=?,email=?,password=? WHERE id=? AND role='admin'")
                ->execute([$nom, $email, $hash, $editId]);
        } else {
            $pdo->prepare("UPDATE users SET nom=?,email=? WHERE id=? AND role='admin'")
                ->execute([$nom, $email, $editId]);
        }
        $success   = 'Admin mis à jour.';
        $editAdmin = null;
    } else {
        if (!$password) { $error = 'Mot de passe obligatoire.'; }
        else {
            $chk = $pdo->prepare('SELECT id FROM users WHERE email=?');
            $chk->execute([$email]);
            if ($chk->fetch()) { $error = 'Email déjà utilisé.'; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users(nom,email,password,role) VALUES(?,?,?,'admin')")
                    ->execute([$nom, $email, $hash]);
                $success = 'Admin créé.';
            }
        }
    }
}

$admins = $pdo->query("SELECT * FROM users WHERE role='admin' ORDER BY created_at ASC")->fetchAll();

$pageTitle  = 'Gestion des Admins';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'admins';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($error):   ?><div class="alert-ecall error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert-ecall success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title"><?= $editAdmin ? '✏️ Modifier Admin' : '➕ Nouvel Admin' ?></span>
      </div>
      <div class="panel-body">
        <form method="POST" action="">
          <?php if ($editAdmin): ?>
            <input type="hidden" name="edit_id" value="<?= $editAdmin['id'] ?>">
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= htmlspecialchars($editAdmin['nom'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($editAdmin['email'] ?? '') ?>" required>
          </div>
          <div class="mb-4">
            <label class="form-label">Mot de passe <?= $editAdmin ? '(optionnel)' : '' ?></label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
          </div>
          <button type="submit" class="btn-ecall"><?= $editAdmin ? 'Mettre à jour' : 'Créer l\'admin' ?></button>
          <?php if ($editAdmin): ?>
            <a href="/ecall/admin/crud_admins.php" class="d-block text-center text-muted mt-2 text-decoration-none" style="font-size:.85rem;">Annuler</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">👑 Administrateurs (<?= count($admins) ?>)</span>
      </div>
      <div class="panel-body p-0">
        <table class="ecall-table">
          <thead>
            <tr><th>Nom</th><th>Email</th><th>Créé le</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $a): ?>
            <tr>
              <td>
                <?= htmlspecialchars($a['nom']) ?>
                <?php if ($a['id'] == $_SESSION['user_id']): ?>
                  <span class="role-badge badge-pill-admin ms-1">Vous</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($a['email']) ?></td>
              <td class="text-muted" style="font-size:.8rem;"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <a href="?edit=<?= $a['id'] ?>" class="btn-edit-ecall me-1">Éditer</a>
                <?php if ($a['id'] != $_SESSION['user_id']): ?>
                  <a href="?delete=<?= $a['id'] ?>" class="btn-danger-ecall"
                     onclick="return confirm('Supprimer cet admin ?')">Suppr.</a>
                <?php else: ?>
                  <span class="text-muted" style="font-size:.75rem;"></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
