<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mail.php';
requireRole('client');

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM appels WHERE client_id = ? AND statut = 'en_cours' ORDER BY id DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$appelEnCours = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$client = $stmt->fetch();

if (isset($_POST['start_call'])) {
    $stmt = $pdo->prepare("INSERT INTO appels (client_id, statut, date_appel) VALUES (?, 'en_cours', NOW())");
    $stmt->execute([$_SESSION['user_id']]);
    $appelId = $pdo->lastInsertId();
    
    sendCallConfirmationEmail($client['email'], $client['nom'], $appelId, date('d/m/Y H:i:s'));
    
    header('Location: appel.php');
    exit;
}

if (isset($_POST['end_call']) && isset($_POST['appel_id']) && isset($_POST['duree'])) {
    $appelId = (int)$_POST['appel_id'];
    $duree = (int)$_POST['duree'];
    
    $stmt = $pdo->prepare("UPDATE appels SET statut = 'termine', duree = ? WHERE id = ? AND client_id = ?");
    $stmt->execute([$duree, $appelId, $_SESSION['user_id']]);
    
    header('Location: liste-appels.php');
    exit;
}

$pageTitle = 'Lancer un Appel';
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_nom'];
$activeMenu = 'appel';
include __DIR__ . '/../includes/header.php';
?>

<style>
.call-container {
    max-width: 500px;
    margin: 0 auto;
}
.call-card {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #E3D6BF;
}
.call-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #933B5B, #B5728A);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    animation: pulse 1.5s infinite;
}
.call-avatar span {
    font-size: 3rem;
}
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(147,59,91,0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(147,59,91,0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(147,59,91,0); }
}
.call-timer {
    font-size: 3rem;
    font-weight: 700;
    font-family: monospace;
    color: #933B5B;
    margin: 1rem 0;
}
.btn-hangup {
    background: #D46B6B;
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-hangup:hover {
    background: #c55a5a;
    transform: scale(1.02);
}
.btn-call-start {
    background: #8BA88A;
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-call-start:hover {
    background: #7a9a79;
    transform: scale(1.02);
}
.info-text {
    margin-top: 1rem;
    padding: 1rem;
    background: #FBF9F5;
    border-radius: 12px;
    font-size: 0.8rem;
    color: #6B6556;
    text-align: center;
    border: 1px solid #E3D6BF;
}
.status-text {
    font-weight: 500;
    margin: 0.5rem 0;
}
.status-active {
    color: #8BA88A;
}
.status-waiting {
    color: #6B6556;
}
.call-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2D2A24;
    margin-bottom: 0.5rem;
}
</style>

<div class="call-container">
    <?php if ($appelEnCours): ?>
        <div class="call-card">
            <div class="call-avatar">
                <span>🎧</span>
            </div>
            <div class="call-timer" id="timerDisplay">00:00</div>
            <div class="status-text status-active">🟢 Appel en cours</div>
            <div class="status-text status-waiting" style="font-size: 0.8rem;">Un agent va vous assister</div>
            
            <form method="POST" action="" id="endCallForm">
                <input type="hidden" name="appel_id" id="appelId" value="<?= $appelEnCours['id'] ?>">
                <input type="hidden" name="duree" id="dureeValue" value="0">
                <input type="hidden" name="end_call" value="1">
                <button type="submit" class="btn-hangup" onclick="return confirm('Terminer cet appel ?')">
                    📞 Raccrocher
                </button>
            </form>
        </div>

        <script>
            let seconds = 0;
            let timerInterval;
            
            function updateTimer() {
                seconds++;
                let minutes = Math.floor(seconds / 60);
                let secs = seconds % 60;
                let display = String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                document.getElementById('timerDisplay').textContent = display;
                document.getElementById('dureeValue').value = seconds;
            }
            
            timerInterval = setInterval(updateTimer, 1000);
        </script>
        
    <?php else: ?>
        <div class="call-card">
            <div class="call-avatar">
                <span>📞</span>
            </div>
            <div class="call-title">Appel E-CALL</div>
            <p style="color: #6B6556; margin-bottom: 1rem;">Un agent est disponible pour vous assister</p>
            
            <form method="POST" action="">
                <input type="hidden" name="start_call" value="1">
                <button type="submit" class="btn-call-start" id="startCallBtn">
                    🟢 Lancer l'appel
                </button>
            </form>
        </div>

    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>