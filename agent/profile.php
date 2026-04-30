<?php
// agent/profile.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('agent');

$pdo     = getPDO();
$error   = '';
$success = '';

// Load agent
$stmt  = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$agent = $stmt->fetch();

// DELETE account
if (isset($_POST['delete_account'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_SESSION['user_id']]);
    $_SESSION = [];
    session_destroy();
    header('Location: /ecall/auth/login.php');
    exit;
}

// UPDATE profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
    $nom       = trim($_POST['nom']       ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = trim($_POST['password']  ?? '');
    $confirm   = trim($_POST['confirm']   ?? '');

    if (!$nom || !$email) {
        $error = 'Nom et email obligatoires.';
    } elseif ($password && $password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif ($password && strlen($password) < 6) {
        $error = 'Mot de passe trop court (6 min).';
    } else {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=?,password=? WHERE id=?")
                ->execute([$nom, $email, $telephone, $hash, $_SESSION['user_id']]);
        } else {
            $pdo->prepare("UPDATE users SET nom=?,email=?,telephone=? WHERE id=?")
                ->execute([$nom, $email, $telephone, $_SESSION['user_id']]);
        }
        $_SESSION['user_nom'] = $nom;
        $success = 'Profil mis à jour.';
        // Reload
        $stmt->execute([$_SESSION['user_id']]);
        $agent = $stmt->fetch();
    }
}

$pageTitle  = 'Mon Profil';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'profile';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($error):   ?><div class="alert-ecall error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert-ecall success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-header"><span class="panel-title">✏️ Modifier mon profil</span></div>
      <div class="panel-body">
        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($agent['nom']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($agent['email']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($agent['telephone'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Nouveau mot de passe (optionnel)</label>
            <input type="password" name="password" class="form-control" placeholder="6 caractères min.">
          </div>
          <div class="mb-4">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="confirm" class="form-control" placeholder="••••••••">
          </div>
          <button type="submit" class="btn-ecall">Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="panel" style="border-color: rgba(244,63,94,.25);">
      <div class="panel-header" style="border-color: rgba(244,63,94,.2);">
        <span class="panel-title" style="color:var(--c-danger);">⚠️ Zone dangereuse</span>
      </div>
      <div class="panel-body">
        <p class="text-muted mb-3" style="font-size:.875rem;">
          La suppression de votre compte est définitive. Toutes vos données seront perdues.
        </p>
        <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
          <button type="submit" name="delete_account" class="btn-danger-ecall w-100" style="padding:.65rem;">
            🗑️ Supprimer mon compte
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
