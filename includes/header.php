<?php
$pageTitle = $pageTitle ?? 'E-CALL';
$activeMenu = $activeMenu ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — E-CALL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ecall/assets/style.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        E-CALL
        <span>PANEL <?= strtoupper($userRole ?? '') ?></span>
    </div>
    <nav class="sidebar-nav">
        <?php if (($userRole ?? '') === 'admin'): ?>
            <a href="/ecall/admin/dashboard.php" class="<?= $activeMenu==='dash' ?'active':'' ?>">
                📊 Dashboard
            </a>
            <a href="/ecall/admin/appels.php" class="<?= $activeMenu==='appels' ?'active':'' ?>">
                📞 Appels
            </a>
            <a href="/ecall/admin/crud_agents.php" class="<?= $activeMenu==='agents' ?'active':'' ?>">
                🎧 Agents
            </a>
            <a href="/ecall/admin/crud_clients.php" class="<?= $activeMenu==='clients'?'active':'' ?>">
                👤 Clients
            </a>
            <a href="/ecall/admin/crud_admins.php" class="<?= $activeMenu==='admins' ?'active':'' ?>">
                👑 Admins
            </a>
        <?php elseif (($userRole ?? '') === 'agent'): ?>
            <a href="/ecall/agent/dashboard.php" class="<?= $activeMenu==='dash' ?'active':'' ?>">
                📊 Dashboard
            </a>
            <a href="/ecall/agent/appels.php" class="<?= $activeMenu==='appels' ?'active':'' ?>">
                📞 Appels
            </a>
            <a href="/ecall/agent/informations.php" class="<?= $activeMenu==='informations'?'active':'' ?>">
                👤 Mes Informations
            </a>
        <?php elseif (($userRole ?? '') === 'client'): ?>
            <a href="/ecall/client/dashboard.php" class="<?= $activeMenu==='dash'?'active':'' ?>">
                📊 Dashboard
            </a>
            <a href="/ecall/client/appel.php" class="<?= $activeMenu==='appel'?'active':'' ?>">
                📞 Lancer un Appel
            </a>
            <a href="/ecall/client/liste-appels.php" class="<?= $activeMenu==='mes-appels'?'active':'' ?>">
                📋 Mes Appels
            </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <strong><?= htmlspecialchars($userName ?? 'Utilisateur') ?></strong>
            <?= htmlspecialchars($userRole ?? '') ?>
        </div>
        <a href="/ecall/logout.php" class="btn-danger-ecall d-block text-center" style="padding:0.5rem;">
            Déconnexion
        </a>
    </div>
</aside>

<div class="main-wrap">
    <div class="topbar">
        <span class="topbar-title"><?= htmlspecialchars($pageTitle) ?></span>
        <span class="topbar-badge badge-<?= $userRole ?? 'client' ?>"><?= strtoupper($userRole ?? '') ?></span>
    </div>
    <div class="page-content">