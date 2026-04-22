<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?mode=login', true, 302);
    exit;
}

$pageTitle = 'Support — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Support</h1>
      <p>On est là pour vous — une question, un conseil, un souci de commande.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Nous contacter</h2>
      <p class="lead">Disponible du lundi au vendredi, 9h–18h.</p>

      <div class="sp-info-grid" style="background:transparent;padding:0;margin-top:18px">
        <div class="sp-info-card" style="border:1px solid rgba(242,167,176,.25)">
          <div class="ic">📧</div>
          <h3>Email</h3>
          <p><a href="mailto:contact@jollybeauty.fr" style="color:var(--rose-deep);font-weight:600">contact@jollybeauty.fr</a></p>
        </div>
        <div class="sp-info-card" style="border:1px solid rgba(242,167,176,.25)">
          <div class="ic">⏰</div>
          <h3>Horaires</h3>
          <p>Lun–Ven, 9h–18h</p>
        </div>
        <div class="sp-info-card" style="border:1px solid rgba(242,167,176,.25)">
          <div class="ic">📦</div>
          <h3>Commande</h3>
          <p>Indiquez votre email + référence de commande si possible.</p>
        </div>
      </div>

      <div style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
        <a href="<?= $jbBase ?>/contact.php" class="btn btn-primary">Ouvrir le formulaire de contact →</a>
        <a href="<?= $jbBase ?>/login.php" class="btn btn-outline">← Retour à mon espace</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

