<?php
// client/appel.php
require_once __DIR__ . '/../includes/session.php';
requireRole('client');

$pageTitle  = 'Lancer un Appel';
$userRole   = $_SESSION['user_role'];
$userName   = $_SESSION['user_nom'];
$activeMenu = 'appel';
include __DIR__ . '/../includes/header.php';
?>

<div style="max-width:540px; margin:0 auto;">
  <div class="panel" style="text-align:center; padding:3rem 2rem;">
    <!-- ANIMATED CALL BUTTON (DÉSACTIVÉ) -->
    <div class="call-btn-wrap">
      <button class="btn-call" id="callBtn" onclick="showComingSoon()" disabled style="opacity:0.6; cursor:not-allowed;">
        📞
      </button>
    </div>
    <div id="callStatus" style="font-family:var(--ff-head); font-size:.95rem; color:var(--c-muted); margin-top:1.5rem;">
      📢 Fonctionnalité en cours de développement
    </div>
    <div id="callTimer" style="font-family:var(--ff-head); font-size:2rem; color:var(--c-accent); display:none; margin-top:.5rem;">
      00:00
    </div>
  </div>

  <div class="panel mt-3">
    <div class="panel-header"><span class="panel-title">ℹ️ Information</span></div>
    <div class="panel-body">
      <p class="text-muted" style="font-size:.875rem; margin:0;">
        ⚠️ La fonctionnalité d'appel est actuellement en développement. Elle sera disponible prochainement.<br>
        Merci de votre compréhension.
      </p>
    </div>
  </div>
</div>

<!-- Modal de notification -->
<div class="modal fade" id="comingSoonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="color:var(--c-accent);">🚧 Fonctionnalité en développement</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>La fonction d'appel n'est pas encore disponible.</p>
        <p class="text-muted mb-0" style="font-size:.85rem;">Cette fonctionnalité sera activée prochainement. Merci de votre patience !</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-ecall" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
function showComingSoon() {
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('comingSoonModal'));
  modal.show();
  
  // Notification animation
  const btn = document.getElementById('callBtn');
  btn.style.transform = 'scale(1.05)';
  setTimeout(() => {
    btn.style.transform = 'scale(1)';
  }, 200);
}

// Message in the console for developers
console.log('📢 Fonctionnalité d\'appel désactivée - En cours de développement');
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>