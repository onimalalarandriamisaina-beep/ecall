<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
requireRole('agent');

$pdo = getPDO();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT a.*, u.nom as client_nom 
        FROM appels a 
        JOIN users u ON a.client_id = u.id";

if (!empty($search)) {
    $sql .= " WHERE u.nom LIKE :search OR u.email LIKE :search";
}
$sql .= " ORDER BY a.date_appel DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$appels = $stmt->fetchAll();

if (!empty($search)) {
    $countSql = "SELECT COUNT(*) FROM appels a JOIN users u ON a.client_id = u.id WHERE u.nom LIKE :search OR u.email LIKE :search";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $total = $countStmt->fetchColumn();
} else {
    $total = $pdo->query("SELECT COUNT(*) FROM appels")->fetchColumn();
}
$totalPages = ceil($total / $limit);

$pageTitle = 'Gestion des Appels';
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_nom'];
$activeMenu = 'appels';
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
.search-box {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.search-box input {
    flex: 1;
}
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}
.pagination a {
    padding: 0.5rem 1rem;
    border: 1px solid #E3D6BF;
    border-radius: 8px;
    text-decoration: none;
    color: #933B5B;
    transition: all 0.2s;
}
.pagination a:hover {
    background: #933B5B;
    color: white;
}
.pagination .active {
    background: #933B5B;
    color: white;
    border-color: #933B5B;
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
.ecall-table tbody tr:hover {
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
.client-name {
    font-weight: 500;
    color: #2D2A24;
}
</style>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">📞 Appels clients</span>
    </div>
    <div class="panel-body">
        
        <?php if (count($appels) > 0): ?>
            <div class="table-wrapper">
                <table class="ecall-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Durée</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appels as $appel): ?>
                        <tr>
                            <td class="id-cell"><?= $appel['id'] ?></td>
                            <td class="client-name"><?= htmlspecialchars($appel['client_nom']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($appel['date_appel'])) ?></td>
                            <td class="duree-cell">
                                <?php 
                                if ($appel['duree']) {
                                    $minutes = floor($appel['duree'] / 60);
                                    $secondes = $appel['duree'] % 60;
                                    echo sprintf("%02d:%02d", $minutes, $secondes);
                                } else {
                                    echo '--:--';
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
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page-1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">← Précédent</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page+1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">Suivant →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <p style="color: #6B6556;">Aucun appel trouvé.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>