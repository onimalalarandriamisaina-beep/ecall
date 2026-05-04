<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';

if (isLoggedIn()) { 
    redirectByRole(); 
}

$error = '';

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
                if ($role_type === 'admin' && $user['role'] !== 'admin') {
                    $error = 'Accès réservé aux administrateurs.';
                } elseif ($role_type === 'agent' && $user['role'] !== 'agent') {
                    $error = 'Accès réservé aux agents.';
                } elseif ($role_type === 'client' && $user['role'] !== 'client' && $user['role'] !== 'admin') {
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
            max-width: 440px;
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

        .role-selector {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
            justify-content: center;
        }

        .role-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .role-btn.client {
            background: #E3D6BF;
            color: #2D2A24;
        }
        .role-btn.client.active {
            background: #933B5B;
            color: white;
        }
        .role-btn.agent {
            background: #E3D6BF;
            color: #2D2A24;
        }
        .role-btn.agent.active {
            background: #6B9BD4;
            color: white;
        }
        .role-btn.admin {
            background: #E3D6BF;
            color: #2D2A24;
        }
        .role-btn.admin.active {
            background: #933B5B;
            color: white;
        }
        .role-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
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

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: var(--c-text-muted);
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">E-CALL</div>
        
        <!-- Sélecteur de rôle -->
        <div class="role-selector">
            <a href="?role=client" class="role-btn client <?= $role_type === 'client' ? 'active' : '' ?>">👤 Client</a>
            <a href="?role=agent" class="role-btn agent <?= $role_type === 'agent' ? 'active' : '' ?>">🎧 Agent</a>
            <a href="?role=admin" class="role-btn admin <?= $role_type === 'admin' ? 'active' : '' ?>">👑 Admin</a>
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
                <a href="/ecall/auth/register.php" class="text-accent">S'inscrire</a>
            </p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>