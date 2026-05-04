<?php
require_once __DIR__ . '/includes/session.php';

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

        .navbar-home {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--c-border);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-light));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--c-text-light);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--c-primary);
        }

        .hero-section {
            max-width: 1280px;
            margin: 0 auto;
            padding: 5rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            color: var(--c-text);
        }

        .hero-content .highlight {
            color: var(--c-primary);
        }

        .hero-content p {
            font-size: 1.125rem;
            color: var(--c-text-light);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-primary {
            background: var(--c-primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-primary:hover {
            background: var(--c-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: white;
            color: var(--c-primary);
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--c-border);
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-secondary:hover {
            border-color: var(--c-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .hero-illustration {
            display: flex;
            justify-content: center;
        }

        .phone-animation {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        .phone-animation span {
            font-size: 5rem;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(147,59,91,0.4); }
            70% { transform: scale(1.05); opacity: 0.9; box-shadow: 0 0 0 20px rgba(147,59,91,0); }
            100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(147,59,91,0); }
        }

        .features-section {
            background: var(--c-surface);
            padding: 5rem 2rem;
            border-top: 1px solid var(--c-border);
            border-bottom: 1px solid var(--c-border);
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--c-text);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--c-bg);
            padding: 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--c-border);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--c-primary-light);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--c-primary);
        }

        .feature-card p {
            color: var(--c-text-light);
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            padding: 2rem;
            color: var(--c-text-light);
            background: var(--c-surface);
            border-top: 1px solid var(--c-border);
        }

        @media (max-width: 768px) {
            .hero-section {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 3rem 1.5rem;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
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
            <h1>Bienvenue sur <span class="highlight"> Call Center</span></h1>
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
        <p>&copy; 2025 E-CALL - Tous droits réservés</p>
        <p style="font-size: 0.75rem; margin-top: 0.5rem;">Bienvenue sur Call Center</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>