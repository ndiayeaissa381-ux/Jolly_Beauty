<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Guide des tailles — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/bijoux/bijoux-bracelet-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Guide des tailles</h1>
      <p>Quelques repères simples pour choisir la bonne taille, en douceur.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Bracelets</h2>
      <p class="lead">Les mesures ci-dessous sont des repères. Si vous hésitez, privilégiez la taille la plus proche (ajustable si la pièce l’est).</p>
      <div class="sp-field" style="margin-top:0">
        <ul style="padding-left:18px;line-height:1.8;color:var(--text)">
          <li><strong>S — 16 cm</strong> : poignet fin</li>
          <li><strong>M — 18 cm</strong> : poignet standard (souvent le plus demandé)</li>
          <li><strong>L — 20 cm</strong> : poignet plus large</li>
        </ul>
      </div>

      <h2 style="margin-top:22px">Bagues</h2>
      <p class="lead">En cas de doute, un bijoutier peut mesurer votre tour de doigt en quelques secondes.</p>
      <div class="sp-field" style="margin-top:0">
        <ul style="padding-left:18px;line-height:1.8;color:var(--text)">
          <li><strong>52</strong> : doigt fin</li>
          <li><strong>54–56</strong> : taille fréquente</li>
          <li><strong>58+</strong> : doigt large</li>
        </ul>
      </div>

      <p style="margin-top:16px;font-size:.86rem;color:var(--muted);line-height:1.7">
        Une question ? Écrivez-nous via la page <a href="<?= $jbBase ?>/contact.php" style="color:var(--rose-deep);font-weight:600">Contact</a> en indiquant le produit qui vous intéresse.
      </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
