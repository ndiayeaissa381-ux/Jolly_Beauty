<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?mode=login', true, 302);
    exit;
}

$pageTitle = 'Mon Profil — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

$me = currentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $db = getDB();
    $uid = (int)($me['id'] ?? 0);

    $name  = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));

    $currentPass = (string)($_POST['current_password'] ?? '');
    $newPass     = (string)($_POST['new_password'] ?? '');
    $newPass2    = (string)($_POST['new_password2'] ?? '');

    if ($uid <= 0) {
        $error = 'Session invalide. Veuillez vous reconnecter.';
    } elseif (strlen($name) < 2) {
        $error = 'Nom requis (min. 2 caractères).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        $stmt = $db->prepare('SELECT id,email,password,role FROM users WHERE id=? LIMIT 1');
        $stmt->execute([$uid]);
        $row = $stmt->fetch();
        if (!$row) {
            $error = "Compte introuvable. Veuillez vous reconnecter.";
        } else {
            // Email unique
            $chk = $db->prepare('SELECT id FROM users WHERE email=? AND id<>? LIMIT 1');
            $chk->execute([$email, $uid]);
            if ($chk->fetch()) {
                $error = 'Cet email est déjà utilisé.';
            }
        }

        $wantsPassChange = ($currentPass !== '' || $newPass !== '' || $newPass2 !== '');
        if ($error === '' && $wantsPassChange) {
            if ($currentPass === '' || $newPass === '' || $newPass2 === '') {
                $error = 'Pour changer le mot de passe, remplissez les 3 champs.';
            } elseif (!password_verify($currentPass, (string)$row['password'])) {
                $error = 'Mot de passe actuel incorrect.';
            } elseif (strlen($newPass) < 6) {
                $error = 'Nouveau mot de passe trop court (min. 6 caractères).';
            } elseif ($newPass !== $newPass2) {
                $error = 'Les nouveaux mots de passe ne correspondent pas.';
            }
        }

        if ($error === '') {
            if ($wantsPassChange) {
                $hash = password_hash($newPass, PASSWORD_BCRYPT);
                $upd = $db->prepare('UPDATE users SET name=?, email=?, password=? WHERE id=?');
                $upd->execute([$name, $email, $hash, $uid]);
            } else {
                $upd = $db->prepare('UPDATE users SET name=?, email=? WHERE id=?');
                $upd->execute([$name, $email, $uid]);
            }

            $_SESSION['jb_user'] = [
                'id'    => $uid,
                'name'  => $name,
                'email' => $email,
            ];

            // Si le compte en base est admin, garder l'accès admin ouvert
            if (!empty($row['role']) && strtolower((string)$row['role']) === 'admin') {
                $_SESSION['jb_admin'] = true;
            }

            $me = currentUser();
            $success = 'Profil mis à jour avec succès.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Mon Profil</h1>
      <p>Gérez vos informations personnelles et préférences.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Informations</h2>
      <p class="lead">Cet espace reprend les informations de votre compte.</p>

      <?php if ($error !== ''): ?>
        <div class="sp-alert sp-alert--err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success !== ''): ?>
        <div class="sp-alert sp-alert--ok"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= $jbBase ?>/pages/profile.php">
        <input type="hidden" name="save_profile" value="1">
        <div class="sp-row">
          <div class="sp-field">
            <label for="p_name">Nom *</label>
            <input id="p_name" name="name" required value="<?= htmlspecialchars((string)($me['name'] ?? '')) ?>">
          </div>
          <div class="sp-field">
            <label for="p_email">Email *</label>
            <input id="p_email" type="email" name="email" required value="<?= htmlspecialchars((string)($me['email'] ?? '')) ?>">
          </div>
        </div>

        <div class="sp-field" style="margin-top:10px">
          <label style="display:flex;align-items:center;justify-content:space-between;gap:12px">
            <span>Changer le mot de passe</span>
            <span style="color:var(--muted);font-weight:400">Laissez vide si inchangé</span>
          </label>
        </div>
        <div class="sp-row">
          <div class="sp-field">
            <label for="p_cur">Mot de passe actuel</label>
            <input id="p_cur" type="password" name="current_password" autocomplete="current-password" placeholder="••••••••">
          </div>
          <div class="sp-field">
            <label for="p_new">Nouveau mot de passe</label>
            <input id="p_new" type="password" name="new_password" autocomplete="new-password" placeholder="Min. 6 caractères">
          </div>
        </div>
        <div class="sp-field">
          <label for="p_new2">Confirmer le nouveau mot de passe</label>
          <input id="p_new2" type="password" name="new_password2" autocomplete="new-password" placeholder="Répétez le nouveau mot de passe">
        </div>

        <button type="submit" class="sp-submit" style="margin-top:14px">Enregistrer</button>
      </form>

      <div style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
        <a href="<?= $jbBase ?>/login.php" class="btn btn-outline">← Retour à mon espace</a>
        <a href="<?= $jbBase ?>/category.php?c=all" class="btn btn-primary">Découvrir la boutique →</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

