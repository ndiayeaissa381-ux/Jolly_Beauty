<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Livraison & retours — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-2.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Livraison &amp; retours</h1>
      <p>Transparence, simplicité, et sérénité — comme tout ce que nous faisons chez Jolly Beauty.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Livraison</h2>
      <p class="lead">Délais indicatifs (hors période forte charge / jours fériés).</p>
      <ul style="padding-left:18px;line-height:1.85;color:var(--text)">
        <li><strong>Expédition</strong> : sous 24–48h ouvrées (selon disponibilité)</li>
        <li><strong>Suivi</strong> : transmis dès l’envoi (email)</li>
        <li><strong>France</strong> : livraison suivie (délais porteur)</li>
      </ul>

      <h2 style="margin-top:22px">Retours</h2>
      <p class="lead">Vous avez 30 jours pour changer d’avis sur les articles éligibles.</p>
      <ul style="padding-left:18px;line-height:1.85;color:var(--text)">
        <li>Les articles doivent être <strong>non portés</strong>, dans leur <strong>emballage d’origine</strong></li>
        <li>Contactez le service client avec votre <strong>numéro de commande</strong> pour obtenir la marche à suivre</li>
      </ul>

      <p style="margin-top:16px;font-size:.86rem;color:var(--muted);line-height:1.7">
        Besoin d’aide ? <a href="<?= $jbBase ?>/contact.php" style="color:var(--rose-deep);font-weight:600">Contact</a> — merci d’indiquer votre <strong>référence de commande</strong> si possible.
      </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
