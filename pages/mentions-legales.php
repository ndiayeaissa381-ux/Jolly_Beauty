<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Mentions légales — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/brand/logo.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Mentions légales</h1>
      <p>Informations relatives à l’éditeur du site.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Éditeur</h2>
      <p><strong>Jolly Beauty</strong><br>
        Contact : <a href="mailto:contact@jollybeauty.fr" style="color:var(--rose-deep);font-weight:600">contact@jollybeauty.fr</a></p>
      <p class="lead" style="margin-top:12px">Les informations exactes (raison sociale, SIREN, hébergeur, directeur de publication) doivent être complétées selon votre situation juridique.</p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
