<?php
/** @var string $jbBase */
$coff = $jbBase . '/assets/images/coffrets/coffret-luxe.jpg';
$coff2 = $jbBase . '/assets/images/coffrets/coffret-prestige-1.jpg';
?>
<section class="jb-block jb-block--white">
  <div style="max-width:800px;margin:0 auto;text-align:center;padding:0 5%;" class="jb-prose">
    <h2>L’art du cadeau</h2>
    <p>Chaque coffret Jolly Beauty est une invitation à la douceur : des produits d’exception réunis dans un écrin élégant.</p>
  </div>
</section>

<section class="jb-block jb-block--alt">
  <h2>Coffrets à découvrir</h2>
  <div class="jb-size-grid" style="max-width:960px;">
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($coff) ?>" alt="Coffret" style="height:220px;width:100%;object-fit:cover;">
      <div style="padding:24px;">
        <h3 style="font-family:var(--font-serif,'Playfair Display',serif);margin-bottom:10px;">Moment précieux</h3>
        <p style="font-size:0.88rem;color:var(--jb-text-light);line-height:1.6;">Une sélection pour offrir ou se faire plaisir.</p>
      </div>
    </div>
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($coff2) ?>" alt="Coffret" style="height:220px;width:100%;object-fit:cover;">
      <div style="padding:24px;">
        <h3 style="font-family:var(--font-serif,'Playfair Display',serif);margin-bottom:10px;">Prestige</h3>
        <p style="font-size:0.88rem;color:var(--jb-text-light);line-height:1.6;">Rituels complets pour une expérience sensorielle.</p>
      </div>
    </div>
  </div>
</section>

<section class="jb-block jb-block--white">
  <h2>Services cadeaux</h2>
  <div class="jb-size-grid" style="max-width:1000px;">
    <div class="jb-size-card" style="text-align:center;"><span style="font-size:2rem;">🎁</span><h3 style="margin:12px 0 8px;font-size:1rem;">Emballage soigné</h3><p style="font-size:0.82rem;color:var(--jb-text-light);">Prêt à offrir.</p></div>
    <div class="jb-size-card" style="text-align:center;"><span style="font-size:2rem;">💌</span><h3 style="margin:12px 0 8px;font-size:1rem;">Message</h3><p style="font-size:0.82rem;color:var(--jb-text-light);">Une attention personnalisée.</p></div>
    <div class="jb-size-card" style="text-align:center;"><span style="font-size:2rem;">📦</span><h3 style="margin:12px 0 8px;font-size:1rem;">Livraison</h3><p style="font-size:0.82rem;color:var(--jb-text-light);">Suivi de commande.</p></div>
    <div class="jb-size-card" style="text-align:center;"><span style="font-size:2rem;">✨</span><h3 style="margin:12px 0 8px;font-size:1rem;">Carte cadeau</h3><p style="font-size:0.82rem;color:var(--jb-text-light);">À utiliser sur la boutique.</p></div>
  </div>
</section>
