<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('client');

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM appels WHERE client_id = ? ORDER BY date_appel DESC");
$stmt->execute([$_SESSION['user_id']]);
$appels = $stmt->fetchAll();

$pageTitle = 'Mes Appels';
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_nom'];
$activeMenu = 'mes-appels';
include __DIR__ . '/../includes/header.php';
?>

<style>
.badge-en-cours {
    background: rgba(226, 184, 107, 0.15);
    color: #E2B86B;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}
.badge-termine {
    background: rgba(139, 168, 138, 0.15);
    color: #8BA88A;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}
.table-wrapper {
    overflow-x: auto;
}
.ecall-table {
    width: 100%;
    border-collapse: collapse;
}
.ecall-table th, .ecall-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #E3D6BF;
}
.ecall-table th {
    background: #FBF9F5;
    font-weight: 600;
    color: #2D2A24;
}
.ecall-table tr:hover {
    background: #FBF9F5;
}
.duree-cell {
    font-family: monospace;
    font-weight: 500;
}
.id-cell {
    font-weight: 600;
    color: #933B5B;
}
.empty-state {
    text-align: center;
    padding: 3rem;
}
.empty-state p {
    color: #6B6556;
    margin-bottom: 1rem;
}
</style>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">📞 Historique de mes appels</span>
    </div>
    <div class="panel-body">
        <?php if (count($appels) > 0): ?>
            <div class="table-wrapper">
                <table class="ecall-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Durée</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appels as $appel): ?>
                        <tr>
                            <td class="id-cell"><?= $appel['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($appel['date_appel'])) ?></td>
                            <td class="duree-cell">
                                <?php 
                                if ($appel['duree']) {
                                    $minutes = floor($appel['duree'] / 60);
                                    $secondes = $appel['duree'] % 60;
                                    echo sprintf("%02d:%02d", $minutes, $secondes);
                                } else {
                                    echo '00:00';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($appel['statut'] == 'en_cours'): ?>
                                    <span class="badge-en-cours">🟡 En cours</span>
                                <?php else: ?>
                                    <span class="badge-termine">✅ Terminé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucun appel pour le moment.</p>
                <a href="/ecall/client/appel.php" class="btn-ecall" style="display: inline-block; width: auto;">
                    📞 Lancer un appel
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>