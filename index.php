<?php
// index.php - Page d'accueil
require_once __DIR__ . '/includes/session.php';

// Si déjà connecté, rediriger vers le dashboard approprié
if (isLoggedIn()) {
    redirectByRole();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-CALL - Solution de Call Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ecall/assets/style.css">
</head>
<body class="homepage">
    <nav class="navbar-home">
        <div class="nav-container">
            <div class="logo">E-CALL</div>
            <div class="nav-links">
                <a href="#features">Fonctionnalités</a>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="hero-content">
            <h1>Gestion professionnelle de <span class="highlight">Call Center</span></h1>
            <p>Gérez efficacement vos appels clients avec notre plateforme complète. Simple, rapide et fiable.</p>
            <div class="hero-buttons">
                <a href="/ecall/auth/register.php" class="btn-primary">S'inscrire</a>
                <a href="/ecall/auth/login.php" class="btn-secondary">Se connecter</a>
            </div>
        </div>
        <div class="hero-illustration">
            <div class="phone-animation">
                <span>📞</span>
            </div>
        </div>
    </div>

    <div class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Fonctionnalités principales</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👑</div>
                    <h3>Administration</h3>
                    <p>Gestion complète des agents, clients et statistiques</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎧</div>
                    <h3>Agents</h3>
                    <p>Interface dédiée pour la gestion des appels entrants</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👤</div>
                    <h3>Clients</h3>
                    <p>Lancez facilement des appels vers nos agents</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 E-CALL - Tous droits réservés</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>