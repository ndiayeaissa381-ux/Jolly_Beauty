<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand-logo">
        <?php
        $logoFile = null;
        $imgDir = __DIR__ . '/../images/';
        if (is_dir($imgDir)) {
          $imgs = array_values(array_filter(scandir($imgDir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp|svg|gif)$/i', $f)));
          if (!empty($imgs)) $logoFile = '/Jolly_Beauty/images/' . $imgs[0];
        }
        if ($logoFile): ?><img src="<?= htmlspecialchars($logoFile) ?>" alt="Jolly Beauty"><?php else: ?>Jolly <em>Beauty</em><?php endif; ?>
      </div>
      <p class="footer-tagline">La beauté des instants doux <br>Des bijoux délicats et des rituels sensoriels pensés pour sublimer votre féminité.</p>
      <div class="footer-socials">
        <button class="footer-social-btn" title="Instagram">📸</button>
        <button class="footer-social-btn" title="TikTok">🎵</button>
        <button class="footer-social-btn" title="Pinterest">📌</button>
      </div>
      <div class="footer-payments" style="margin-top:18px;">
        <span>Visa</span><span>Mastercard</span><span>PayPal</span><span>Apple Pay</span>
      </div>
    </div>

    <div class="footer-col">
      <h4>Collections</h4>
      <ul>
        <li><a href="/Jolly_Beauty/products.php">Tous les produits</a></li>
        <li><a href="/Jolly_Beauty/products.php?category=bijoux">Bijoux</a></li>
        <li><a href="/Jolly_Beauty/products.php?category=soins">Soins &amp; Rituels</a></li>
        <li><a href="/Jolly_Beauty/products.php?category=coffrets">Coffrets Cadeaux</a></li>
        <li><a href="/Jolly_Beauty/products.php">Nouveautés</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Aide</h4>
      <ul>
        <li><a href="#">Guide des tailles</a></li>
        <li><a href="#">Livraison et retours</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="/Jolly_Beauty/login.php">Mon compte</a></li>
        <li><a href="#">Suivi de commande</a></li>
      </ul>
    </div>

    <div class="footer-col" id="contact">
      <h4>Contact</h4>
      <ul>
        <li><a href="mailto:contact@jollybeauty.fr">contact@jollybeauty.fr</a></li>
        <li><a href="#">Du lundi au vendredi, de 9h à 18h</a></li>
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
      <a href="#">Mentions légales</a>
      <a href="#">CGV</a>
      <a href="#">Confidentialité</a>
    </div>
  </div>
</footer>

<script src="/Jolly_Beauty/js/main.js"></script>

<script>
async function submitNewsletterFooter(e, form) {
  e.preventDefault();
  const btn = form.querySelector('button');
  btn.textContent = '...'; btn.disabled = true;
  try {
    const fd = new FormData(form);
    const r = await fetch('/Jolly_Beauty/api/newsletter.php', {method:'POST', body:fd});
    const d = await r.json();
    showToast(d.message || 'Merci !');
    if (d.success) form.reset();
  } catch { showToast('Erreur réseau.'); }
  btn.textContent = "D'accord"; btn.disabled = false;
}
</script>
</body>
</html>