<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Notre histoire — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/notre-histoire.css">';
include __DIR__ . '/../includes/header.php';
?>

<div class="notre-histoire-page">

  <!-- NOTRE HISTOIRE HERO SECTION -->
  <section class="nh-hero">
    <div class="nh-hero__image">
      <img src="<?= $jbBase ?>/assets/images/slider/slider-1.jpg" alt="Bougie, bijoux et fleurs séchées">
    </div>
    <div class="nh-hero__content">
      <span class="nh-hero__label">NOTRE HISTOIRE</span>
      <h1 class="nh-hero__title">Jolly Beauty, une parenthèse de douceur.</h1>
      <p class="nh-hero__text">Jolly Beauty est née d'un désir simple: créer des bijoux et des soins qui subliment la beauté naturelle et accompagnent chaque femme dans ses moments précieux.</p>
      <p class="nh-hero__text">Chaque création est pensée avec amour, pour vous offrir bien plus qu'un produit: une expérience, une émotion, un moment rien que pour vous.</p>
      <a href="<?= $jbBase ?>/bijoux.php" class="nh-hero__btn">DÉCOUVRIR NOS COLLECTIONS</a>
    </div>
  </section>

  <!-- NOTRE MISSION SECTION -->
  <section class="nh-mission">
    <div class="nh-mission__left">
      <h2 class="nh-mission__title">Notre mission</h2>
      <svg class="nh-mission__heart" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
      </svg>
    </div>
    <div class="nh-mission__right">
      <p>Vous offrir des créations élégantes et des soins naturels qui prennent soin de vous, de votre peau et de votre confiance. Parce que vous méritez le meilleur, chaque jour.</p>
    </div>
  </section>

  <!-- FEATURES SECTION -->
  <section class="nh-features">
    <div class="nh-feature">
      <div class="nh-feature__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
        </svg>
      </div>
      <h3 class="nh-feature__title">Des ingrédients naturels</h3>
      <p class="nh-feature__text">Sélectionnés avec soin pour leur douceur et leur efficacité.</p>
    </div>
    <div class="nh-feature">
      <div class="nh-feature__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </div>
      <h3 class="nh-feature__title">Acier inoxydable</h3>
      <p class="nh-feature__text">Des bijoux résistants à l'eau, faits pour vous accompagner au quotidien.</p>
    </div>
    <div class="nh-feature">
      <div class="nh-feature__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
        </svg>
      </div>
      <h3 class="nh-feature__title">Pensé pour offrir</h3>
      <p class="nh-feature__text">Des coffrets délicats et raffinés pour faire plaisir ou se faire plaisir.</p>
    </div>
  </section>

  <!-- NOS VALEURS SECTION -->
  <section class="nh-values">
    <div class="nh-values__content">
      <span class="nh-values__label">NOS VALEURS</span>
      <h2 class="nh-values__title">Authenticité, qualité et bienveillance.</h2>
      <p class="nh-values__text">Nous croyons en une beauté vraie et naturelle. C'est pourquoi nous sélectionnons des matériaux de qualité et des ingrédients doux, respectueux de votre peau et de l'environnement.</p>
      <a href="<?= $jbBase ?>/contact.php" class="nh-values__link">EN SAVOIR PLUS →</a>
    </div>
    <div class="nh-values__image">
      <img src="<?= $jbBase ?>/assets/images/produits/ritual-beaute-1.jpg" alt="Huile nourrissante, crème hydratante et bijoux">
    </div>
  </section>

  <!-- DERRIÈRE JOLLY BEAUTY SECTION -->
  <section class="nh-founder">
    <div class="nh-founder__image">
      <img src="<?= $jbBase ?>/assets/images/slider/slider-5.jpg" alt="La fondatrice de Jolly Beauty">
    </div>
    <div class="nh-founder__content">
      <span class="nh-founder__label">DERRIÈRE JOLLY BEAUTY</span>
      <h2 class="nh-founder__title">Bonjour, je suis la fondatrice.</h2>
      <p class="nh-founder__text">Passionnée par l'univers de la beauté et du bien-être, j'ai créé Jolly Beauty pour partager avec vous ce qui m'anime chaque jour: la volonté de sublimer la beauté naturelle avec des produits sains, élégants et accessibles. Merci de faire partie de cette belle aventure.</p>
      <p class="nh-founder__signature">Avec amour, Jolly</p>
    </div>
  </section>

  <!-- BEST-SELLERS SECTION -->
  <section class="nh-bestsellers">
    <div class="nh-bestsellers__header">
      <span class="nh-bestsellers__label">BEST-SELLERS</span>
      <h2 class="nh-bestsellers__title">Nos coups de cœur</h2>
      <p class="nh-bestsellers__text">Découvrez les produits qui font le succès de Jolly Beauty</p>
    </div>
    <div class="nh-bestsellers__grid">
      <div class="nh-product">
        <div class="nh-product__image">
          <img src="<?= $jbBase ?>/assets/images/bijoux/bijoux-bracelet-1.jpg" alt="Bracelet Jolly Beauty">
        </div>
        <div class="nh-product__content">
          <h3 class="nh-product__name">Bracelet Élégance</h3>
          <p class="nh-product__price">34,90&nbsp;€</p>
          <button class="nh-product__btn">Ajouter au panier</button>
        </div>
      </div>
      <div class="nh-product">
        <div class="nh-product__image">
          <img src="<?= $jbBase ?>/assets/images/produits/ritual-beaute-2.jpg" alt="Eau Florale Rose">
        </div>
        <div class="nh-product__content">
          <h3 class="nh-product__name">Eau Florale Rose</h3>
          <p class="nh-product__price">16,90&nbsp;€</p>
          <button class="nh-product__btn">Ajouter au panier</button>
        </div>
      </div>
      <div class="nh-product">
        <div class="nh-product__image">
          <img src="<?= $jbBase ?>/assets/images/bijoux/bijoux-collier-1.jpg" alt="Collier Jolly Beauty">
        </div>
        <div class="nh-product__content">
          <h3 class="nh-product__name">Colonne Douceur</h3>
          <p class="nh-product__price">42,90&nbsp;€</p>
          <button class="nh-product__btn">Ajouter au panier</button>
        </div>
      </div>
      <div class="nh-product">
        <div class="nh-product__image">
          <img src="<?= $jbBase ?>/assets/images/produits/ritual-beaute-1.jpg" alt="Crème Hydratante">
        </div>
        <div class="nh-product__content">
          <h3 class="nh-product__name">Crème Hydratante</h3>
          <p class="nh-product__price">28,90&nbsp;€</p>
          <button class="nh-product__btn">Ajouter au panier</button>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER FEATURES -->
  <section class="nh-footer-features">
    <div class="nh-footer-feature">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
      </svg>
      <h4>LIVRAISON OFFERTE</h4>
      <p>Dès 60€ d'achat</p>
    </div>
    <div class="nh-footer-feature">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      <h4>PAIEMENT SÉCURISÉ</h4>
      <p>Transactions 100% sécurisées</p>
    </div>
    <div class="nh-footer-feature">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
      </svg>
      <h4>RETOURS FACILES</h4>
      <p>14 jours pour changer d'avis</p>
    </div>
    <div class="nh-footer-feature">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
      </svg>
      <h4>SERVICE CLIENT</h4>
      <p>À votre écoute</p>
    </div>
  </section>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
