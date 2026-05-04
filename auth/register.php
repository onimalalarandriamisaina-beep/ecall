<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mail.php';

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
            
            sendWelcomeEmail($email, $nom);
            
            $success = '✅ Compte créé avec succès ! Un email de bienvenue vous a été envoyé.';
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
    <style>
    
        :root {
            --c-primary: #933B5B; 
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--c-bg) 0%, #EDE8DF 100%);
            color: var(--c-text);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 15px;
            line-height: 1.5;
            min-height: 100vh;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 460px;
            box-shadow: var(--shadow-lg);
        }

        .auth-logo {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-light));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            text-align: center;
            color: var(--c-text-light);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .form-label {
            color: var(--c-text);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: white !important;
            border: 1px solid var(--c-border) !important;
            color: var(--c-text) !important;
            border-radius: var(--radius) !important;
            padding: 0.6rem 0.9rem !important;
            transition: all 0.2s;
            width: 100%;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--c-primary) !important;
            box-shadow: 0 0 0 3px rgba(147, 59, 91, 0.1) !important;
            outline: none;
        }

        .btn-ecall {
            background: var(--c-primary);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            font-size: 1rem;
            font-family: inherit;
        }

        .btn-ecall:hover {
            background: var(--c-primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .alert-ecall {
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid;
            font-size: 0.875rem;
        }

        .alert-ecall.error {
            background: rgba(212, 107, 107, 0.1);
            border-color: rgba(212, 107, 107, 0.3);
            color: var(--c-danger);
        }

        .alert-ecall.success {
            background: rgba(139, 168, 138, 0.1);
            border-color: rgba(139, 168, 138, 0.3);
            color: var(--c-success);
        }

        .divider {
            margin: 1.5rem 0;
            border: 0;
            height: 1px;
            background: var(--c-border);
        }

        .text-accent {
            color: var(--c-primary);
            text-decoration: none;
        }

        .text-accent:hover {
            text-decoration: underline;
        }

        .text-muted {
            color: var(--c-text-muted) !important;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">E-CALL</div>
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
                <label class="form-label">Nom complet</label>
                <input type="text" name="nom" class="form-control" placeholder="Jean Dupont"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="vous@exemple.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="tel" name="telephone" class="form-control" placeholder="+261 34 00 000 00"
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="6 caractères min." required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-ecall">Créer mon compte</button>
        </form>
        <?php endif; ?>

        <hr class="divider">
        <p class="text-center" style="font-size:0.85rem;">
            Déjà un compte ? <a href="/ecall/auth/login.php" class="text-accent">Se connecter</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>