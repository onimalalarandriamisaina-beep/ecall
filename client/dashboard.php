<?php
// client/dashboard.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('client');

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$client = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Client - E-CALL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ecall/assets/style.css">
</head>
<body>
    <header class="topbar">
        <div class="topbar-logo">E-CALL</div>
        <div class="topbar-user">
            <span>Bonjour, <?= htmlspecialchars($client['nom']) ?></span>
            <a href="/ecall/logout.php" class="btn-danger-ecall">Déconnexion</a>
        </div>
    </header>

    <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        <h1 style="margin-bottom: 2rem;">Tableau de bord Client</h1>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-title">📞 Lancer un appel</span>
                    </div>
                    <div class="panel-body text-center">
                        <a href="/ecall/client/appel.php" class="btn-ecall" style="max-width: 200px; margin: 0 auto;">
                            Lancer un appel
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-title">👤 Mes informations</span>
                    </div>
                    <div class="panel-body">
                        <p><strong>Nom :</strong> <?= htmlspecialchars($client['nom']) ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></p>
                        <p><strong>Membre depuis :</strong> <?= date('d/m/Y', strtotime($client['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>