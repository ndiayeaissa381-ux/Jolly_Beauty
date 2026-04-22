<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Suivi de commande — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-4.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Suivi de commande</h1>
      <p>Indiquez-nous votre référence et nous vous guidons, étape par étape.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Ce dont nous avons besoin</h2>
      <p class="lead">Pour aller plus vite, merci d’inclure :</p>
      <ul style="padding-left:18px;line-height:1.85;color:var(--text)">
        <li><strong>La référence de commande</strong> (reçue par email)</li>
        <li><strong>L’email</strong> utilisé lors de l’achat</li>
        <li>Le <strong>nom</strong> sur la commande (si différent)</li>
      </ul>

      <div style="margin-top:16px">
        <a class="sp-submit" style="display:inline-block;text-align:center;" href="<?= $jbBase ?>/contact.php?subject=commande">Écrire au service client →</a>
      </div>

      <p style="margin-top:16px;font-size:.86rem;color:var(--muted);line-height:1.7">
        Astuce : le suivi apparaît aussi dans l’email d’<strong>expédition</strong> dès que votre colis est pris en charge.
      </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
