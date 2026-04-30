<?php
// auth/register.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

if (isLoggedIn()) { redirectByRole(); }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom']       ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = trim($_POST['password']  ?? '');
    $confirm   = trim($_POST['confirm']   ?? '');

    if (!$nom || !$email || !$password || !$confirm) {
        $error = 'Tous les champs obligatoires doivent être remplis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $pdo  = getPDO();
        $chk  = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $pdo->prepare('INSERT INTO users (nom, email, password, telephone, role) VALUES (?,?,?,?,?)');
            $ins->execute([$nom, $email, $hash, $telephone, 'client']);
            $success = 'Compte créé avec succès ! Vous pouvez vous connecter.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — E-CALL</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/ecall/assets/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:460px;">
    <div class="auth-logo">E<span>-</span>CALL</div>
    <p class="auth-subtitle">Créer votre compte client</p>

    <?php if ($error): ?>
      <div class="alert-ecall error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert-ecall success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Nom complet *</label>
        <input type="text" name="nom" class="form-control" placeholder="Jean Dupont"
               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" placeholder="vous@exemple.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="telephone" class="form-control" placeholder="+261 34 00 000 00"
               value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Mot de passe *</label>
        <input type="password" name="password" class="form-control" placeholder="6 caractères min." required>
      </div>
      <div class="mb-4">
        <label class="form-label">Confirmer le mot de passe *</label>
        <input type="password" name="confirm" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-ecall">Créer mon compte</button>
    </form>
    <?php endif; ?>

    <hr class="divider">
    <p class="text-center text-muted" style="font-size:.85rem;">
      Déjà un compte ? <a href="/ecall/auth/login.php" class="text-accent text-decoration-none">Se connecter</a>
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
