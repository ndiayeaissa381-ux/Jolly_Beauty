<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Jolly Beauty — Bijoux délicats & rituels de beauté pour sublimer votre féminité.">
<title><?= htmlspecialchars($pageTitle ?? 'Jolly Beauty') ?></title>
<?php $jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?= $jbBase ?>/assets/css/style.css">
<?php if (!empty($extraCss)) echo $extraCss; ?>
</head>
<body>
<div class="cursor-dot" id="cursor-dot"></div>
<div class="cursor-ring" id="cursor-ring"></div>

<div class="announce-bar"><div class="announce-track"><?php for($i=0;$i<2;$i++): ?><div class="announce-item">Livraison gratuite dès 60€ <span class="announce-dot">✦</span> Retours gratuits 30 jours <span class="announce-dot">✦</span> Acier inoxydable · Résistant à l'eau <span class="announce-dot">✦</span> Code JOLLY15 — 15% de réduction <span class="announce-dot">✦</span> Bijoux plaqués or 18k · Hypoallergéniques <span class="announce-dot">✦</span> Coffrets cadeaux disponibles <span class="announce-dot">✦</span></div><?php endfor; ?></div></div>

<nav class="nav" id="main-nav">
<?php
$logoFile = $jbBase . '/assets/images/brand/logo.jpg';
$cur = basename($_SERVER['PHP_SELF'], '.php');
?>
  <a href="<?= $jbBase ?>/index.php" class="nav-logo" aria-label="Jolly Beauty">
    <?php if ($logoFile && is_file(__DIR__ . '/../assets/images/brand/logo.jpg')): ?><img src="<?= htmlspecialchars($logoFile) ?>" alt="Jolly Beauty"><?php else: ?>Jolly <em>Beauty</em><?php endif; ?>
  </a>
  <ul class="nav-links">
    <li><a href="<?= $jbBase ?>/index.php" class="<?= $cur==='index'?'active':'' ?>">Accueil</a></li>
    <li><a href="<?= $jbBase ?>/bijoux.php" class="<?= $cur==='bijoux'?'active':'' ?>">Bijoux</a></li>
    <li><a href="<?= $jbBase ?>/soins-rituels.php" class="<?= $cur==='soins-rituels'?'active':'' ?>">Soins &amp; Rituels</a></li>
    <li><a href="<?= $jbBase ?>/coffrets.php" class="<?= $cur==='coffrets'?'active':'' ?>">Coffrets</a></li>
    <li><a href="<?= $jbBase ?>/media-gallery.php" class="<?= $cur==='media-gallery'?'active':'' ?>">Galerie</a></li>
    <li><a href="<?= $jbBase ?>/notre-histoire.php">Notre histoire</a></li>
    <li><a href="<?= $jbBase ?>/contact.php">Contact</a></li>
  </ul>
  <div class="nav-actions">
    <button class="nav-btn" onclick="toggleSearch()" aria-label="Rechercher"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></button>
    <button class="nav-btn" onclick="window.location='<?= $jbBase ?>/login.php'" aria-label="Mon compte"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></button>
    <button class="nav-btn" onclick="openCart()" aria-label="Panier" style="position:relative"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg><span class="cart-count" id="cart-count">0</span></button>
    <button class="nav-btn nav-hamburger" onclick="openMobileNav()" aria-label="Menu"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
  </div>
</nav>

<nav class="mobile-nav" id="mobile-nav">
  <button class="mobile-nav-close" onclick="closeMobileNav()">✕</button>
  <div class="mobile-nav-logo">Jolly <em>Beauty</em></div>
  <ul class="mobile-nav-links">
    <li><a href="<?= $jbBase ?>/index.php" onclick="closeMobileNav()">Accueil</a></li>
    <li><a href="<?= $jbBase ?>/bijoux.php" onclick="closeMobileNav()">Bijoux</a></li>
    <li><a href="<?= $jbBase ?>/soins-rituels.php" onclick="closeMobileNav()">Soins &amp; Rituels</a></li>
    <li><a href="<?= $jbBase ?>/coffrets.php" onclick="closeMobileNav()">Coffrets</a></li>
    <li><a href="<?= $jbBase ?>/media-gallery.php" onclick="closeMobileNav()">Galerie</a></li>
    <li><a href="<?= $jbBase ?>/notre-histoire.php" onclick="closeMobileNav()">Notre histoire</a></li>
    <li><a href="<?= $jbBase ?>/contact.php" onclick="closeMobileNav()">Contact</a></li>
    <li><a href="<?= $jbBase ?>/login.php" onclick="closeMobileNav()">Mon Compte</a></li>
  </ul>
</nav>

<div class="search-overlay" id="search-overlay" onclick="if(event.target===this)closeSearch()">
  <div class="search-box">
    <div class="search-input-wrap">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <form action="<?= $jbBase ?>/category.php" method="GET" style="flex:1;display:flex"><input type="hidden" name="c" value="all"><input type="text" name="q" class="search-input" placeholder="Rechercher des bijoux, soins..." id="search-input"></form>
      <button class="search-close-btn" onclick="closeSearch()">✕</button>
    </div>
    <hr class="search-divider">
    <p class="search-suggestions">Suggestions : <a href="<?= $jbBase ?>/bijoux.php">Bijoux</a> <a href="<?= $jbBase ?>/coffrets.php">Coffrets</a> <a href="<?= $jbBase ?>/category.php?c=all">Collection</a></p>
  </div>
</div>

<div class="cart-overlay-bg" id="cart-bg" onclick="closeCart()"></div>
<div class="cart-overlay" id="cart-overlay">
  <div class="cart-header"><h3>Mon Panier</h3><button class="cart-close" onclick="closeCart()">✕</button></div>
  <div class="shipping-bar-wrap"><div class="bar-label" id="shipping-label">🚚 Plus que <strong id="shipping-remaining">60,00 €</strong> pour la livraison gratuite !</div><div class="shipping-bar-track"><div class="shipping-bar-fill" id="shipping-fill" style="width:0%"></div></div></div>
  <div class="cart-items" id="cart-items-container"></div>
  <div class="cart-footer">
    <div class="cart-total"><span>Total</span><span id="cart-total-price">0,00 €</span></div>
    <a href="<?= $jbBase ?>/checkout.php" class="btn btn--rose btn--full" style="margin-bottom:10px">Commander →</a>
    <button onclick="closeCart()" class="btn btn--outline btn--full" style="font-size:.75rem">Continuer mes achats</button>
  </div>
</div>

<div class="toast-stack" id="toast-stack"></div>
<button class="back-top" id="back-top" onclick="scrollTo({top:0,behavior:'smooth'})">↑</button>