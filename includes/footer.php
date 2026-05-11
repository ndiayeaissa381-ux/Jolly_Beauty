<?php $jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>
<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand-logo">
        <?php
        $footerLogoFile = $jbBase . '/assets/images/brand/logo.jpg';
        if (is_file(__DIR__ . '/../assets/images/brand/logo.jpg')): ?>
          <img src="<?= htmlspecialchars($footerLogoFile) ?>" alt="Jolly Beauty">
        <?php else: ?>
          Jolly <em>Beauty</em>
        <?php endif; ?>
      </div>
      <p class="footer-tagline">✨ Des bijoux délicats et des rituels sensoriels pensés pour sublimer votre féminité.</p>
      <div class="footer-socials">
        <a href="https://www.instagram.com/jollyy_beauty" target="_blank" rel="noopener" class="footer-social-btn" title="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
          </svg>
        </a>
        <a href="https://www.tiktok.com/@jollyybeauty" target="_blank" rel="noopener" class="footer-social-btn" title="TikTok">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
          </svg>
        </a>
        <a href="mailto:contact@jolly-beauty.fr" class="footer-social-btn" title="Email">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
            <polyline points="22,6 12,13 2,6"></polyline>
          </svg>
        </a>
      </div>
      <div class="footer-payments" style="margin-top:18px;">
        <span>Visa</span><span>Mastercard</span><span>PayPal</span><span>Apple Pay</span>
      </div>
    </div>

    <div class="footer-col">
      <h4>Collections</h4>
      <ul>
        <li><a href="<?= $jbBase ?>/bijoux.php">Toute la collection</a></li>
        <li><a href="<?= $jbBase ?>/bijoux.php">Bijoux</a></li>
        <li><a href="<?= $jbBase ?>/soins-rituels.php">Soins &amp; Rituels</a></li>
        <li><a href="<?= $jbBase ?>/coffrets.php">Coffrets Cadeaux</a></li>
        <li><a href="<?= $jbBase ?>/rituels.php">Rituels</a></li>
        <li><a href="<?= $jbBase ?>/index.php#bestsellers">Nouveautés</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Aide</h4>
      <ul>
        <li><a href="<?= $jbBase ?>/guide-tailles.php">Guide des tailles</a></li>
        <li><a href="<?= $jbBase ?>/livraison-retours.php">Livraison et retours</a></li>
        <li><a href="<?= $jbBase ?>/faq.php">FAQ</a></li>
        <li><a href="<?= $jbBase ?>/login.php">Mon compte</a></li>
        <li><a href="<?= $jbBase ?>/suivi-commande.php">Suivi de commande</a></li>
      </ul>
    </div>

    <div class="footer-col" id="contact">
      <h4>Contact</h4>
      <ul>
        <li><a href="mailto:contact@jolly-beauty.fr">contact@jolly-beauty.fr</a></li>
        <li><a href="tel:+330756957481">+33 07 56 95 74 81</a></li>
        <li><a href="https://wa.me/330756957481" target="_blank" rel="noopener">WhatsApp Business</a></li>
        <li><span style="color:rgba(255,255,255,.85)">Du lundi au vendredi, de 9h à 18h</span></li>
      </ul>
      <div style="margin-top:20px;">
        <h4 style="margin-bottom:12px;">Newsletter</h4>
        <form onsubmit="submitNewsletterFooter(event,this)" style="display:flex;flex-direction:column;gap:8px;">
          <input type="email" name="email" placeholder="Votre email" style="padding:9px 14px;border-radius:50px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff;font-family:var(--font-sans);font-size:.8rem;outline:none;" required>
          <button type="submit" style="padding:9px;border-radius:50px;background:var(--rose-deep);color:#fff;border:none;cursor:pointer;font-family:var(--font-sans);font-size:.72rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;transition:background .3s;">D'accord</button>
        </form>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <span>© 2026 Jolly Beauté. Tous droits réservés.</span>
    <div class="footer-legal">
      <a href="<?= $jbBase ?>/mentions-legales.php">Mentions légales</a>
      <a href="<?= $jbBase ?>/cgv.php">CGV</a>
      <a href="<?= $jbBase ?>/confidentialite.php">Confidentialité</a>
    </div>
  </div>
</footer>

<script>
window.JB_BASE=<?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;
window.IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;
</script>
<script src="<?= $jbBase ?>/assets/js/script.js"></script>

<script>
async function submitNewsletterFooter(e, form) {
  e.preventDefault();
  const btn = form.querySelector('button');
  btn.textContent = '...'; btn.disabled = true;
  try {
    const fd = new FormData(form);
    const r = await fetch('<?= $jbBase ?>/api/newsletter.php', {method:'POST', body:fd});
    const d = await r.json();
    showToast(d.message || 'Merci !');
    if (d.success) form.reset();
  } catch { showToast('Erreur réseau.'); }
  btn.textContent = "D'accord"; btn.disabled = false;
}
</script>

<!-- Cookie Banner RGPD -->
<div class="cookie-banner" id="cookie-banner">
  <div class="cookie-banner-content">
    <p class="cookie-banner-text">
      Nous utilisons des cookies pour améliorer votre expérience sur notre site. 
      En continuant votre navigation, vous acceptez notre <a href="<?= $jbBase ?>/confidentialite.php">politique de confidentialité</a>.
    </p>
    <div class="cookie-banner-actions">
      <button class="cookie-btn cookie-btn--accept" onclick="acceptCookies()">Accepter</button>
      <button class="cookie-btn cookie-btn--customize" onclick="window.location.href='<?= $jbBase ?>/confidentialite.php'">Personnaliser</button>
      <button class="cookie-btn cookie-btn--refuse" onclick="refuseCookies()">Refuser</button>
    </div>
  </div>
</div>

<script>
// Cookie Banner Logic
(function() {
  const COOKIE_CONSENT_KEY = 'jb_cookie_consent';
  const banner = document.getElementById('cookie-banner');
  
  function getCookie(name) {
    const value = '; ' + document.cookie;
    const parts = value.split('; ' + name + '=');
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
  }
  
  function setCookie(name, value, days) {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/; SameSite=Lax';
  }
  
  window.acceptCookies = function() {
    setCookie(COOKIE_CONSENT_KEY, 'accepted', 365);
    banner.classList.remove('show');
  };
  
  window.refuseCookies = function() {
    setCookie(COOKIE_CONSENT_KEY, 'refused', 365);
    banner.classList.remove('show');
  };
  
  // Show banner if no consent stored
  const consent = getCookie(COOKIE_CONSENT_KEY);
  if (!consent) {
    setTimeout(function() {
      banner.classList.add('show');
    }, 1000);
  }
})();
</script>

</body>
</html>