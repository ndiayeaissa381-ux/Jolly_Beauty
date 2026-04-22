<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?mode=login', true, 302);
    exit;
}

$pageTitle = 'Mon Code Promo — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

$me = currentUser();
$code = '';
$discount = null;

try {
    $db = getDB();
    $stmt = $db->prepare('SELECT promo_code FROM users WHERE id=? LIMIT 1');
    $stmt->execute([(int)($me['id'] ?? 0)]);
    $code = (string)($stmt->fetchColumn() ?: '');

    if ($code !== '') {
        $promo = validatePromoCode($code, 0);
        if ($promo) {
            $discount = (int)($promo['discount_value'] ?? 0);
        }
    }
} catch (Throwable $e) {
    // On reste silencieux côté UI, la page doit rester affichable
}

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Mon Code Promo</h1>
      <p>Votre petit bonus douceur pour votre prochaine commande.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Votre code</h2>
      <p class="lead">Utilisez-le au moment du paiement.</p>

      <?php if ($code !== ''): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;
                    padding:14px 16px;border:1px solid rgba(242,167,176,.35);border-radius:14px;background:rgba(253,244,246,.7)">
          <div>
            <div style="font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);font-weight:600">Code promo</div>
            <div style="margin-top:6px;font-family:var(--font-serif);font-style:italic;font-size:1.55rem;color:var(--dark)">
              <?= htmlspecialchars(strtoupper($code)) ?>
            </div>
            <div style="margin-top:6px;color:var(--muted);font-size:.88rem;line-height:1.6">
              <?= $discount !== null && $discount > 0 ? ('−' . (int)$discount . '% sur votre commande') : 'Valable selon les conditions du code.' ?>
            </div>
          </div>

          <button type="button" class="btn btn-outline"
                  onclick="navigator.clipboard?.writeText('<?= htmlspecialchars(strtoupper($code), ENT_QUOTES, 'UTF-8') ?>'); showToast('Code copié ✨');">
            Copier
          </button>
        </div>
      <?php else: ?>
        <div class="sp-alert sp-alert--err">Aucun code promo n’est associé à votre compte.</div>
      <?php endif; ?>

      <div style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
        <a href="<?= $jbBase ?>/login.php" class="btn btn-outline">← Retour à mon espace</a>
        <a href="<?= $jbBase ?>/checkout.php" class="btn btn-primary">Aller au paiement →</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

