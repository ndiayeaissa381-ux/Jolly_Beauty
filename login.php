<?php
require_once __DIR__ . '/includes/config.php';

$error   = '';
$success = '';
$mode    = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'login') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $user  = getUserByEmail($email);
    if ($user && password_verify($pass, $user['password'])) {
      $_SESSION['jb_user'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email']];
      header('Location: /Jolly_Beauty/login.php');
      exit;
    } else {
      $error = 'Email ou mot de passe incorrect.';
    }
  }

  if ($action === 'register') {
    $name  = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($name) < 2)
      $error = 'Prénom & Nom requis (min. 2 caractères).';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
      $error = 'Adresse email invalide.';
    elseif (strlen($pass) < 6)
      $error = 'Mot de passe trop court (min. 6 caractères).';
    elseif ($pass !== $pass2)
      $error = 'Les mots de passe ne correspondent pas.';
    elseif (getUserByEmail($email))
      $error = 'Cet email est déjà utilisé. <a href="?mode=login" style="color:var(--rose-deep);font-weight:600">Se connecter →</a>';
    else {
      createUser($name, $email, $pass);
      $user = getUserByEmail($email);
      $_SESSION['jb_user'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email']];
      header('Location: /Jolly_Beauty/login.php?msg=welcome&name='.urlencode($name));
      exit;
    }
  }

  if ($action === 'logout') {
    unset($_SESSION['jb_user']);
    header('Location: /Jolly_Beauty/login.php?msg=logout');
    exit;
  }
}

if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'logout')   $success = 'À bientôt ! 👋';
  if ($_GET['msg'] === 'welcome')  $success = 'Bienvenue, '.sanitize($_GET['name'] ?? '').' ! Votre code −10% : BIENVENUE10 🌸';
}

$pageTitle = $mode === 'register' ? 'Créer un compte — Jolly Beauty' : 'Connexion — Jolly Beauty';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="/Jolly_Beauty/css/style.css">
<style>
/* ─── AUTH PAGE ────────────────────────────────────────────── */
.auth-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
}
@media(max-width:768px){ .auth-page{ grid-template-columns:1fr; } }

/* Côté gauche visuel */
.auth-visual {
  position: relative;
  overflow: hidden;
  background: var(--dark);
  min-height: 380px;
}
.auth-visual__img {
  width: 100%; height: 100%;
  object-fit: cover;
  position: absolute; inset: 0;
  opacity: .55;
}
.auth-visual__overlay {
  position: absolute; inset: 0;
  background: linear-gradient(160deg, rgba(44,26,29,.8) 0%, rgba(212,120,138,.15) 100%);
}
.auth-visual__content {
  position: relative; z-index: 1;
  height: 100%; padding: 52px;
  display: flex; flex-direction: column;
  justify-content: space-between;
}
.auth-visual__logo {
  font-family: var(--font-serif);
  font-size: 1.8rem; font-style: italic;
  color: #fff;
}
.auth-visual__tagline {
  font-family: var(--font-serif);
  font-size: clamp(2rem, 3vw, 3.2rem);
  font-style: italic; color: #fff;
  line-height: 1.18; margin-bottom: 18px;
}
.auth-visual__sub {
  font-size: .95rem;
  color: rgba(255,255,255,.5);
  margin-bottom: 36px;
}
.auth-perks { display: flex; flex-direction: column; gap: 14px; }
.auth-perk {
  display: flex; gap: 14px; align-items: center;
  font-size: .82rem; color: rgba(255,255,255,.6);
}
.auth-perk-icon {
  width: 34px; height: 34px; flex-shrink: 0;
  border: 1px solid rgba(242,167,176,.35);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: .9rem; color: var(--rose-l);
}
@media(max-width:768px){ .auth-visual{ display:none; } }

/* Côté droit formulaire */
.auth-form-side {
  display: flex; align-items: center; justify-content: center;
  padding: 60px 8%;
  background: var(--cream);
}
.auth-form-wrap { width: 100%; max-width: 440px; }
.auth-back-link {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: .68rem; letter-spacing: .14em; text-transform: uppercase;
  color: var(--muted); margin-bottom: 40px;
  transition: color .2s;
}
.auth-back-link:hover { color: var(--rose-deep); }

.auth-heading {
  font-family: var(--font-serif);
  font-size: 2.4rem; font-weight: 500; font-style: italic;
  color: var(--dark); line-height: 1.1; margin-bottom: 8px;
}
.auth-subheading {
  font-size: .92rem; color: var(--muted); margin-bottom: 34px;
}

.auth-alert-error {
  background: rgba(201,96,112,.08);
  border-left: 3px solid var(--rose-deep);
  color: var(--rose-dark);
  padding: 12px 16px; font-size: .82rem;
  border-radius: 0 8px 8px 0;
  margin-bottom: 22px;
}
.auth-alert-success {
  background: rgba(92,158,122,.1);
  border-left: 3px solid #5C9E7A;
  color: #3d7a5a;
  padding: 12px 16px; font-size: .82rem;
  border-radius: 0 8px 8px 0;
  margin-bottom: 22px;
}

.auth-form { display: flex; flex-direction: column; gap: 16px; }
.auth-field { display: flex; flex-direction: column; gap: 7px; }
.auth-label {
  font-size: .68rem; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--dark);
  display: flex; justify-content: space-between; align-items: center;
}
.auth-label a { color: var(--rose-deep); font-weight: 500; text-transform: none; letter-spacing: 0; font-size: .75rem; }
.auth-input {
  padding: 13px 16px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  font-family: var(--font-sans); font-size: .92rem;
  color: var(--dark); background: var(--white);
  outline: none; transition: border-color .2s, box-shadow .2s;
  width: 100%;
}
.auth-input:focus {
  border-color: var(--rose-deep);
  box-shadow: 0 0 0 3px rgba(212,120,138,.1);
}
.auth-input::placeholder { color: var(--muted); opacity: .6; }

.auth-submit {
  margin-top: 6px; width: 100%; padding: 15px;
  background: var(--dark); color: #fff;
  border: none; border-radius: 50px;
  font-family: var(--font-sans);
  font-size: .78rem; letter-spacing: .14em; text-transform: uppercase;
  font-weight: 600; cursor: pointer;
  transition: background .3s, transform .2s;
}
.auth-submit:hover { background: var(--rose-deep); transform: translateY(-1px); }

.auth-switch {
  text-align: center; font-size: .84rem;
  color: var(--muted); margin-top: 20px;
}
.auth-switch a { color: var(--rose-deep); font-weight: 600; }

/* ─── DASHBOARD (connecté) ─────────────────────────────────── */
.dash-page { min-height: 100vh; background: var(--cream); }
.dash-nav {
  background: var(--dark); padding: 0 5%; height: 68px;
  display: flex; align-items: center; justify-content: space-between;
}
.dash-nav-logo {
  font-family: var(--font-serif); font-size: 1.6rem;
  font-style: italic; color: #fff;
}
.dash-nav-logo span { color: var(--rose-l); }
.dash-nav-right { display: flex; align-items: center; gap: 16px; }
.dash-nav-email { font-size: .75rem; color: rgba(255,255,255,.45); }
.dash-logout-btn {
  padding: 8px 18px;
  border: 1px solid rgba(255,255,255,.15);
  background: transparent; color: rgba(255,255,255,.55);
  border-radius: 50px; font-size: .72rem;
  font-family: var(--font-sans); cursor: pointer;
  transition: all .2s;
}
.dash-logout-btn:hover { border-color: var(--rose-l); color: var(--rose-l); }

.dash-container { max-width: 1100px; margin: 0 auto; padding: 60px 5% 100px; }
.dash-eyebrow {
  font-size: .68rem; letter-spacing: .2em; text-transform: uppercase;
  color: var(--rose-deep); font-weight: 600;
  display: flex; align-items: center; gap: 8px; margin-bottom: 10px;
}
.dash-eyebrow::before { content:''; width:20px; height:1px; background:var(--rose-deep); }
.dash-welcome {
  font-family: var(--font-serif);
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 500; font-style: italic;
  color: var(--dark); margin-bottom: 8px;
}
.dash-welcome em { color: var(--rose-deep); }
.dash-sub { font-size: .9rem; color: var(--muted); margin-bottom: 40px; }

.dash-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; }
@media(max-width:900px){ .dash-grid{ grid-template-columns: 1fr 1fr; } }
@media(max-width:500px){ .dash-grid{ grid-template-columns: 1fr; } }

.dash-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: 14px; padding: 26px 22px;
  transition: transform .25s, box-shadow .25s, border-color .25s;
  display: block;
}
.dash-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow);
  border-color: var(--rose);
}
.dash-card-icon { font-size: 1.9rem; margin-bottom: 14px; display: block; }
.dash-card-title {
  font-family: var(--font-serif); font-size: 1.05rem;
  font-style: italic; color: var(--dark); margin-bottom: 7px;
}
.dash-card-desc { font-size: .8rem; color: var(--muted); line-height: 1.7; }

.success-banner {
  background: var(--rose-pale);
  border: 1px solid var(--blush);
  border-radius: 12px; padding: 16px 20px;
  margin-bottom: 32px;
  font-size: .88rem; color: var(--text);
  display: flex; align-items: center; gap: 12px;
}
.success-banner strong { color: var(--rose-deep); }
</style>
</head>
<body>

<?php if (isLoggedIn()): ?>
<!-- ─── DASHBOARD ──────────────────────────────────────────── -->
<div class="dash-page">
  <nav class="dash-nav">
    <a href="/Jolly_Beauty/index.php" class="dash-nav-logo">Jolly <span>Beauty</span></a>
    <div class="dash-nav-right">
      <span class="dash-nav-email"><?= sanitize(currentUser()['email']) ?></span>
      <form method="POST" style="display:inline">
        <input type="hidden" name="action" value="logout">
        <button class="dash-logout-btn">Se déconnecter</button>
      </form>
    </div>
  </nav>

  <div class="dash-container">
    <?php if($success): ?>
    <div class="success-banner">
      🌸 <span><?= $success ?></span>
    </div>
    <?php endif; ?>

    <div class="dash-eyebrow">Mon Espace</div>
    <h1 class="dash-welcome">Bonjour, <em><?= sanitize(currentUser()['name']) ?></em> ✨</h1>
    <p class="dash-sub">Bienvenue dans votre espace Jolly Beauty.</p>

    <div class="dash-grid">
      <a href="/Jolly_Beauty/products.php" class="dash-card">
        <span class="dash-card-icon">👜</span>
        <div class="dash-card-title">Mes Commandes</div>
        <p class="dash-card-desc">Suivez vos commandes et consultez votre historique d'achats.</p>
      </a>
      <a href="/Jolly_Beauty/products.php" class="dash-card">
        <span class="dash-card-icon">♡</span>
        <div class="dash-card-title">Mes Favoris</div>
        <p class="dash-card-desc">Retrouvez les pièces que vous avez sauvegardées.</p>
      </a>
      <div class="dash-card">
        <span class="dash-card-icon">⚙️</span>
        <div class="dash-card-title">Mon Profil</div>
        <p class="dash-card-desc">Gérez vos informations personnelles et préférences.</p>
      </div>
      <a href="/Jolly_Beauty/products.php?category=bijoux" class="dash-card">
        <span class="dash-card-icon">💍</span>
        <div class="dash-card-title">Bijoux</div>
        <p class="dash-card-desc">Découvrez nos nouvelles collections de bijoux délicats.</p>
      </a>
      <div class="dash-card">
        <span class="dash-card-icon">🎁</span>
        <div class="dash-card-title">Mon Code Promo</div>
        <p class="dash-card-desc"><strong style="color:var(--rose-deep)">BIENVENUE10</strong> — −10% sur votre prochaine commande.</p>
      </div>
      <div class="dash-card">
        <span class="dash-card-icon">💌</span>
        <div class="dash-card-title">Support</div>
        <p class="dash-card-desc">Disponible du lundi au vendredi, 9h–18h.<br>contact@jollybeauty.fr</p>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
<!-- ─── PAGE AUTH ──────────────────────────────────────────── -->
<div class="auth-page">

  <!-- Visuel gauche -->
  <div class="auth-visual">
    <?php
    $bgImg = null;
    $imgDir = __DIR__ . '/images/';
    if (is_dir($imgDir)) {
      $imgs = array_values(array_filter(scandir($imgDir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)));
      if (!empty($imgs)) $bgImg = '/Jolly_Beauty/images/' . $imgs[count($imgs) > 1 ? 1 : 0];
    }
    if ($bgImg): ?>
      <img src="<?= htmlspecialchars($bgImg) ?>" class="auth-visual__img" alt="">
    <?php endif; ?>
    <div class="auth-visual__overlay"></div>
    <div class="auth-visual__content">
      <div class="auth-visual__logo">Jolly Beauty</div>
      <div>
        <div class="auth-visual__tagline">La beauté des<br>moments doux</div>
        <p class="auth-visual__sub">Rejoignez notre communauté.</p>
        <div class="auth-perks">
          <div class="auth-perk"><div class="auth-perk-icon">✦</div>Accès aux ventes privées</div>
          <div class="auth-perk"><div class="auth-perk-icon">📦</div>Suivi de commandes en temps réel</div>
          <div class="auth-perk"><div class="auth-perk-icon">🎁</div>Code −10% pour les nouveaux membres</div>
          <div class="auth-perk"><div class="auth-perk-icon">♡</div>Liste de favoris personnalisée</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulaire droit -->
  <div class="auth-form-side">
    <div class="auth-form-wrap">

      <a href="/Jolly_Beauty/index.php" class="auth-back-link">← Retour à la boutique</a>

      <?php if ($error): ?>
        <div class="auth-alert-error">⚠ <?= $error ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="auth-alert-success">✓ <?= sanitize($success) ?></div>
      <?php endif; ?>

      <?php if ($mode === 'login'): ?>
      <!-- CONNEXION -->
      <h1 class="auth-heading">Bon retour<br>parmi nous ✨</h1>
      <p class="auth-subheading">Connectez-vous à votre espace.</p>
      <form method="POST" class="auth-form">
        <input type="hidden" name="action" value="login">
        <div class="auth-field">
          <label class="auth-label">Email</label>
          <input type="email" name="email" class="auth-input" placeholder="votre@email.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="auth-field">
          <label class="auth-label">
            Mot de passe
            <a href="#">Oublié ?</a>
          </label>
          <input type="password" name="password" class="auth-input" placeholder="Votre mot de passe" required>
        </div>
        <button type="submit" class="auth-submit">Se connecter</button>
      </form>
      <div class="auth-switch">
        Pas encore de compte ?
        <a href="/Jolly_Beauty/login.php?mode=register">Créer un compte gratuit →</a>
      </div>

      <?php else: ?>
      <!-- INSCRIPTION -->
      <h1 class="auth-heading">Créer<br>mon compte 🌸</h1>
      <p class="auth-subheading">Rejoignez la communauté Jolly Beauty.</p>
      <form method="POST" class="auth-form">
        <input type="hidden" name="action" value="register">
        <div class="auth-field">
          <label class="auth-label">Prénom &amp; Nom</label>
          <input type="text" name="name" class="auth-input" placeholder="Amina Diallo"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="auth-field">
          <label class="auth-label">Email</label>
          <input type="email" name="email" class="auth-input" placeholder="votre@email.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="auth-field">
          <label class="auth-label">Mot de passe <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0">(min. 6 caractères)</span></label>
          <input type="password" name="password" class="auth-input" placeholder="Choisissez un mot de passe" required minlength="6">
        </div>
        <div class="auth-field">
          <label class="auth-label">Confirmer le mot de passe</label>
          <input type="password" name="password2" class="auth-input" placeholder="Répétez le mot de passe" required>
        </div>
        <button type="submit" class="auth-submit">Créer mon compte</button>
      </form>
      <div class="auth-switch">
        Déjà un compte ?
        <a href="/Jolly_Beauty/login.php?mode=login">Se connecter →</a>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
<?php endif; ?>

<script src="/Jolly_Beauty/js/main.js"></script>
</body>
</html>