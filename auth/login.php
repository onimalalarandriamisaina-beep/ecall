<?php
// auth/login.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

if (isLoggedIn()) { 
    redirectByRole(); 
}

$error = '';
$success = '';

// Determine the type of connection
$role_type = $_GET['role'] ?? 'client';
$allowed_roles = ['admin', 'agent', 'client'];

if (!in_array($role_type, $allowed_roles)) {
    $role_type = 'client';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $pdo  = getPDO();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Vérifier le rôle pour l'accès
                if ($role_type === 'admin' && $user['role'] !== 'admin') {
                    $error = 'Accès réservé aux administrateurs. Cet email n\'a pas les droits admin.';
                } elseif ($role_type === 'agent' && $user['role'] !== 'agent') {
                    $error = 'Accès réservé aux agents. Cet email n\'a pas les droits agent.';
                } elseif ($role_type === 'client' && $user['role'] !== 'client' && $user['role'] !== 'admin') {
                    // Les admins peuvent aussi accéder à l'interface client
                    $error = 'Cet email n\'est pas associé à un compte client.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_nom']  = $user['nom'];
                    $_SESSION['user_role'] = $user['role'];
                    redirectByRole();
                }
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur de connexion à la base de données.';
        }
    }
}

$pageTitle = match($role_type) {
    'admin' => 'Connexion Administrateur',
    'agent' => 'Connexion Agent',
    default => 'Connexion Client'
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — E-CALL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ecall/assets/style.css">
    <style>
        .role-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            justify-content: center;
        }
        .role-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .role-btn.client {
            background: <?= $role_type === 'client' ? 'var(--c-primary)' : '#e2e8f0' ?>;
            color: <?= $role_type === 'client' ? 'white' : 'var(--c-text)' ?>;
        }
        .role-btn.agent {
            background: <?= $role_type === 'agent' ? 'var(--c-info)' : '#e2e8f0' ?>;
            color: <?= $role_type === 'agent' ? 'white' : 'var(--c-text)' ?>;
        }
        .role-btn.admin {
            background: <?= $role_type === 'admin' ? 'var(--c-primary)' : '#e2e8f0' ?>;
            color: <?= $role_type === 'admin' ? 'white' : 'var(--c-text)' ?>;
        }
        .role-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">E-CALL</div>
        
        <!-- Sélecteur de rôle -->
        <div class="role-selector">
            <a href="?role=client" class="role-btn client">👤 Client</a>
            <a href="?role=agent" class="role-btn agent">🎧 Agent</a>
            <a href="?role=admin" class="role-btn admin">👑 Admin</a>
        </div>
        
        <p class="auth-subtitle"><?= $pageTitle ?></p>

        <?php if ($error): ?>
            <div class="alert-ecall error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       placeholder="exemple@email.com" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-ecall">Se connecter</button>
        </form>

        <?php if ($role_type === 'client'): ?>
            <hr class="divider">
            <p class="text-center" style="font-size:0.85rem;">
                Pas encore de compte ?
                <a href="/ecall/auth/register.php" style="color: var(--c-primary);">S'inscrire</a>
            </p>
        <?php endif; ?>
        
    </div>
</div>
</body>
</html>