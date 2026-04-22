<?php
require_once __DIR__ . '/../includes/config.php';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

// ── AUTO-LOGIN ADMIN via compte utilisateur (role=admin) ───────
// Permet d'accéder à l'admin si l'utilisateur connecté est admin en base.
if (!isAdmin() && isLoggedIn()) {
    $uid = (int)($_SESSION['jb_user']['id'] ?? 0);
    if ($uid > 0) {
        try {
            $stmt = getDB()->prepare('SELECT role FROM users WHERE id=? LIMIT 1');
            $stmt->execute([$uid]);
            $role = (string)($stmt->fetchColumn() ?: '');
            if (strtolower($role) === 'admin') {
                $_SESSION['jb_admin'] = true;
                header('Location: ' . APP_URL . '/admin/index.php', true, 302);
                exit;
            }
        } catch (Throwable $e) {
            // En cas d'erreur DB, on ne bloque pas l'accès au formulaire admin classique
        }
    }
}

// ── LOGIN ─────────────────────────────────────────────────────
if (!isAdmin()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
        if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS_PLAIN) {
            $_SESSION['jb_admin'] = true;
         C   // Toujours revenir sur l'URL "canonique" de l'admin
            header('Location: ' . APP_URL . '/admin/index.php', true, 302);
            exit;
        }
        $loginError = 'Identifiants incorrects.';
    }
    ?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin — Jolly Beauty</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;1,400&family=Poppins:wght@400;500;600&display=swap">
    <style>*{box-sizing:border-box;margin:0;padding:0}:root{--rd:#D4788A;--rk:#B85C6E;--dk:#2C1A1D;--mu:#A07880;--sf:'Playfair Display',serif;--ss:'Poppins',sans-serif}body{font-family:var(--ss);background:linear-gradient(135deg,#FDF4F6,#FDE8EC 50%,#F8D7DA);min-height:100vh;display:grid;place-items:center}.card{background:#fff;border-radius:24px;padding:52px 44px;width:min(400px,92vw);box-shadow:0 24px 80px rgba(192,92,107,.15);text-align:center}.logo{font-family:var(--sf);font-size:2rem;font-style:italic;color:var(--dk);margin-bottom:4px}.sub{font-size:.7rem;letter-spacing:.16em;text-transform:uppercase;color:var(--mu);margin-bottom:36px}label{display:block;text-align:left;font-size:.7rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--mu);margin:16px 0 6px}input{width:100%;padding:12px 16px;border:1.5px solid #F0D8DC;border-radius:10px;font-family:var(--ss);font-size:.88rem;color:var(--dk);outline:none;transition:border .2s}input:focus{border-color:var(--rd)}.btn{margin-top:26px;width:100%;padding:14px;background:var(--rd);color:#fff;border:none;border-radius:50px;font-family:var(--ss);font-size:.78rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:background .2s}.btn:hover{background:var(--rk)}.err{background:#FDE8EC;color:var(--rk);font-size:.78rem;padding:10px 14px;border-radius:8px;margin-top:14px;border-left:3px solid var(--rd)}</style></head><body>
    <div class="card">
      <div style="font-size:2.5rem;margin-bottom:18px">🔐</div>
      <div class="logo">Jolly Beauty</div><div class="sub">Espace Administration</div>
      <form method="POST">
        <label>Identifiant</label><input type="text" name="username" required autofocus placeholder="admin">
        <label>Mot de passe</label><input type="password" name="password" required placeholder="••••••••">
        <?php if (!empty($loginError)): ?><div class="err">⚠ <?= htmlspecialchars($loginError) ?></div><?php endif; ?>
        <button type="submit" name="admin_login" class="btn">Se connecter</button>
      </form>
    </div></body></html><?php exit;
}

if (isset($_GET['logout'])) {
    unset($_SESSION['jb_admin']);
    header('Location: ' . APP_URL . '/admin/index.php', true, 302);
    exit;
}

$db = getDB();
$msg = ''; $msgType = 'success';
$page = $_GET['page'] ?? null;
$section = $_GET['section'] ?? null;
if ($section === null && $page !== null) {
    $map = [
        'dashboard' => 'dashboard',
        'orders' => 'orders',
        'commandes' => 'orders',
        'products' => 'products',
        'produits' => 'products',
        'users' => 'users',
        'clients' => 'users',
        'promo' => 'promo',
        'codes-promo' => 'promo',
    ];
    $section = $map[$page] ?? 'dashboard';
}
$section = $section ?: 'dashboard';

// ── CODES PROMO: actions ─────────────────────────────────────
if ($section === 'promo' && isset($_GET['toggle_promo']) && is_numeric($_GET['toggle_promo'])) {
    $id = (int)$_GET['toggle_promo'];
    $db->prepare('UPDATE promo_codes SET active = 1 - active WHERE id=?')->execute([$id]);
    $msg = 'Code promo mis à jour.'; $msgType = 'success';
}
if ($section === 'promo' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_promo'])) {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discount = (int)($_POST['discount'] ?? 0);
    $maxUses = (trim($_POST['max_uses'] ?? '') !== '') ? (int)$_POST['max_uses'] : null;
    $expiresAt = trim($_POST['expires_at'] ?? '');
    $activePromo = isset($_POST['active']) ? 1 : 0;

    if ($code === '' || $discount <= 0 || $discount > 90) {
        $msg = 'Code et réduction valides requis (1–90%).'; $msgType = 'error';
    } else {
        try {
            $stmt = $db->prepare('INSERT INTO promo_codes (code,discount,max_uses,active,expires_at) VALUES (?,?,?,?,?)');
            $stmt->execute([$code, $discount, $maxUses, $activePromo, ($expiresAt !== '' ? $expiresAt : null)]);
            $msg = 'Code promo créé.'; $msgType = 'success';
        } catch (PDOException $e) {
            $msg = 'Erreur code promo : ' . htmlspecialchars($e->getMessage()); $msgType = 'error';
        }
    }
}

// ── UPLOAD IMAGE ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../assets/images/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
        $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . $filename)) {
            echo json_encode(['success'=>true,'url'=> BASE_URL . '/assets/images/uploads/' . $filename]);
        } else { echo json_encode(['success'=>false,'error'=>'Erreur déplacement fichier']); }
    } else { echo json_encode(['success'=>false,'error'=>'Format non autorisé']); }
    exit;
}

// ── SUPPRIMER PRODUIT ────────────────────────────────────────
if (isset($_GET['delete_product']) && is_numeric($_GET['delete_product'])) {
    $db->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_GET['delete_product']]);
    $msg = 'Produit supprimé avec succès.'; $section = 'products';
}

// ── SAUVEGARDER PRODUIT ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $pid       = (int)($_POST['product_id'] ?? 0);
    $name      = trim($_POST['name'] ?? '');
    $slug      = trim($_POST['slug'] ?? '');
    $cat_id    = (int)($_POST['category_id'] ?? 1);
    $sub       = trim($_POST['sub'] ?? '');
    $short     = trim($_POST['short'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $price     = (float)str_replace(',', '.', $_POST['price'] ?? 0);
    $old_price = (trim($_POST['old_price'] ?? '') !== '') ? (float)str_replace(',', '.', $_POST['old_price']) : null;
    $badge     = trim($_POST['badge'] ?? '');
    $stock     = (int)($_POST['stock'] ?? 0);
    $featured  = isset($_POST['featured']) ? 1 : 0;
    $active    = isset($_POST['active']) ? 1 : 0;
    $images    = array_values(array_filter(array_map('trim', explode("\n", $_POST['images'] ?? ''))));
    $materials = array_values(array_filter(array_map('trim', explode("\n", $_POST['materials'] ?? ''))));
    $sizes     = array_values(array_filter(array_map('trim', explode("\n", $_POST['sizes'] ?? ''))));

    if (!$slug && $name) {
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$name))), '-');
        $base = $slug; $i = 1;
        while ((int)$db->query("SELECT COUNT(*) FROM products WHERE slug='".addslashes($slug)."' AND id!=$pid")->fetchColumn() > 0)
            $slug = $base . '-' . $i++;
    }

    if (!$name || $price <= 0) {
        $msg = 'Le nom et un prix valide sont obligatoires.'; $msgType = 'error'; $section = 'products';
    } else {
        try {
            if ($pid > 0) {
                $db->prepare('UPDATE products SET name=?,slug=?,category_id=?,sub=?,short=?,description=?,price=?,old_price=?,badge=?,stock=?,featured=?,active=? WHERE id=?')
                   ->execute([$name,$slug,$cat_id,$sub,$short,$desc,$price,$old_price,$badge,$stock,$featured,$active,$pid]);
                $db->prepare('DELETE FROM product_images WHERE product_id=?')->execute([$pid]);
                $db->prepare('DELETE FROM product_materials WHERE product_id=?')->execute([$pid]);
                $db->prepare('DELETE FROM product_sizes WHERE product_id=?')->execute([$pid]);
                $msg = '✅ Produit modifié avec succès.';
            } else {
                $db->prepare('INSERT INTO products (name,slug,category_id,sub,short,description,price,old_price,badge,stock,featured,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                   ->execute([$name,$slug,$cat_id,$sub,$short,$desc,$price,$old_price,$badge,$stock,$featured,$active]);
                $pid = (int)$db->lastInsertId();
                $msg = '🎉 Produit créé avec succès !';
            }
            $imgS = $db->prepare('INSERT INTO product_images (product_id,url,sort_order) VALUES (?,?,?)');
            foreach ($images as $i => $url) if ($url) $imgS->execute([$pid,$url,$i]);
            $matS = $db->prepare('INSERT INTO product_materials (product_id,value,sort_order) VALUES (?,?,?)');
            foreach ($materials as $i => $m) if ($m) $matS->execute([$pid,$m,$i]);
            $sizS = $db->prepare('INSERT INTO product_sizes (product_id,value,sort_order) VALUES (?,?,?)');
            foreach ($sizes as $i => $s) if ($s) $sizS->execute([$pid,$s,$i]);
        } catch (PDOException $e) {
            $msg = 'Erreur BD : ' . htmlspecialchars($e->getMessage()); $msgType = 'error';
        }
        $section = 'products';
    }
}

// ── STATS ────────────────────────────────────────────────────
$totalProducts = (int)$db->query('SELECT COUNT(*) FROM products WHERE active=1')->fetchColumn();
$totalOrders   = (int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalRevenue  = (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status!='cancelled'")->fetchColumn();
$totalUsers    = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$pendingOrders = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$lowStock      = (int)$db->query('SELECT COUNT(*) FROM products WHERE stock<=5 AND active=1')->fetchColumn();

$salesByMonth = $db->query(
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, COUNT(*) AS nb, COALESCE(SUM(total),0) AS revenue
     FROM orders WHERE status!='cancelled' AND created_at>=DATE_SUB(NOW(),INTERVAL 12 MONTH)
     GROUP BY month ORDER BY month ASC"
)->fetchAll();

$statusStats = $db->query("SELECT status, COUNT(*) AS nb FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

$salesByCat = $db->query(
    "SELECT c.name, COALESCE(SUM(oi.price*oi.qty),0) AS rev
     FROM order_items oi JOIN products p ON p.id=oi.product_id
     JOIN categories c ON c.id=p.category_id
     JOIN orders o ON o.id=oi.order_id AND o.status!='cancelled'
     GROUP BY c.id ORDER BY rev DESC"
)->fetchAll();

// Admin: montrer aussi les produits inactifs (sinon l'admin peut sembler "vide" alors que le site affiche des vignettes statiques)
$products   = getProducts(null, '', 'default', 500, true);
$categories = $db->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$orders     = getOrders(50);

$editProduct = null;
if (isset($_GET['edit_product']) && is_numeric($_GET['edit_product'])) {
    $section = 'products';
    $ep = $db->prepare(
        "SELECT p.*, GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '\n') AS img_list,
                GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '\n') AS mat_list,
                GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '\n') AS size_list
         FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id
         LEFT JOIN product_materials pm ON pm.product_id=p.id
         LEFT JOIN product_sizes ps ON ps.product_id=p.id
         WHERE p.id=? GROUP BY p.id"
    );
    $ep->execute([(int)$_GET['edit_product']]);
    $editProduct = $ep->fetch();
}

$chartMonths = $chartRevenue = $chartOrders = [];
foreach ($salesByMonth as $row) {
    $d = DateTime::createFromFormat('Y-m', $row['month']);
    $chartMonths[]  = $d ? $d->format('M y') : $row['month'];
    $chartRevenue[] = round((float)$row['revenue'], 2);
    $chartOrders[]  = (int)$row['nb'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Jolly Beauty</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --rose:#F2A7B0;--rp:#FDF4F6;--rd:#D4788A;--rk:#B85C6E;
  --bl:#F8D7DA;--dk:#2C1A1D;--tx:#5C3A3F;--mu:#A07880;
  --border:rgba(242,167,176,.25);--sf:'Playfair Display',serif;--ss:'Poppins',sans-serif;
  --sb:248px;--r:12px;--sh:0 2px 14px rgba(192,92,107,.09);--shm:0 6px 28px rgba(192,92,107,.13)
}
body{font-family:var(--ss);background:#F7EFF2;color:var(--tx);display:flex;min-height:100vh;font-size:.87rem}
a{color:inherit;text-decoration:none} img{max-width:100%;display:block}

/* SIDEBAR */
.sb{width:var(--sb);flex-shrink:0;background:var(--dk);display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0;z-index:50;overflow-y:auto}
.sb-logo{padding:26px 22px 20px;border-bottom:1px solid rgba(255,255,255,.07)}
.sb-logo .wm{font-family:var(--sf);font-style:italic;font-size:1.45rem;color:#fff}
.sb-logo .tg{font-size:.58rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--rose);margin-top:2px}
.sb-nav{padding:16px 10px;flex:1}
.ns{font-size:.57rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.28);padding:14px 12px 5px}
.ni{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;font-size:.78rem;font-weight:500;color:rgba(255,255,255,.65);cursor:pointer;transition:all .18s;margin-bottom:1px}
.ni:hover{background:rgba(255,255,255,.07);color:#fff}
.ni.active{background:var(--rd);color:#fff}
.ni .ic{font-size:.95rem;width:19px;text-align:center;flex-shrink:0;opacity:.95}
.nb{margin-left:auto;background:var(--rk);color:#fff;font-size:.58rem;font-weight:700;padding:2px 7px;border-radius:50px}
.sb-bot{padding:14px 10px;border-top:1px solid rgba(255,255,255,.07)}
.sb-user{display:flex;align-items:center;gap:10px;padding:10px 12px;color:rgba(255,255,255,.55);font-size:.74rem}
.sb-av{width:32px;height:32px;background:var(--rd);border-radius:50%;display:grid;place-items:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0}

/* MAIN */
.main{margin-left:var(--sb);flex:1;display:flex;flex-direction:column;min-height:100vh}
.tb{background:#fff;padding:0 28px;height:62px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:40;box-shadow:var(--sh)}
.tb-title{font-family:var(--sf);font-size:1.25rem;font-weight:500;color:var(--dk)}
.tb-r{display:flex;align-items:center;gap:10px}
.tbtn{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:50px;font-size:.72rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;border:none;cursor:pointer;transition:all .2s}
.t-rose{background:var(--rd);color:#fff}.t-rose:hover{background:var(--rk)}
.t-ghost{background:var(--bl);color:var(--dk)}.t-ghost:hover{background:var(--rose)}
.ct{padding:28px;flex:1}

/* ALERT */
.alert{display:flex;align-items:flex-start;gap:10px;padding:13px 18px;border-radius:var(--r);margin-bottom:22px;font-size:.82rem;font-weight:500}
.a-ok{background:#F0FBF4;color:#166534;border:1px solid #BBF7D0}
.a-err{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA}

/* STATS */
.sg{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
@media(max-width:1100px){.sg{grid-template-columns:repeat(2,1fr)}}
.sc{background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 10px 30px rgba(44,26,29,.06);display:flex;align-items:center;gap:16px;border:1px solid rgba(242,167,176,.18);position:relative;overflow:hidden}
.sc::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 var(--r) var(--r)}
.sc.cr::after{background:linear-gradient(90deg,var(--rd),var(--rose))}
.sc.cg::after{background:linear-gradient(90deg,#C9963A,#F0C060)}
.sc.cv::after{background:linear-gradient(90deg,#16A34A,#4ADE80)}
.sc.cb::after{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.si{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;font-size:1.35rem;flex-shrink:0}
.ir{background:linear-gradient(135deg,#FDE8EC,#F8D7DA)}
.ig{background:linear-gradient(135deg,#FEF9EC,#FDE68A)}
.iv{background:linear-gradient(135deg,#F0FBF4,#DCFCE7)}
.ib{background:linear-gradient(135deg,#EFF6FF,#DBEAFE)}
.sv{font-size:1.6rem;font-weight:700;color:var(--dk);line-height:1;letter-spacing:-.02em}
.sl{font-size:.67rem;color:var(--mu);margin-top:4px;text-transform:uppercase;letter-spacing:.09em;font-weight:600}
.chip{display:inline-flex;align-items:center;gap:4px;font-size:.62rem;font-weight:600;padding:2px 8px;border-radius:50px;margin-top:6px}
.ch-w{background:#FEF9EC;color:#92400E}.ch-ok{background:#DCFCE7;color:#166534}.ch-r{background:#FEF2F2;color:#991B1B}

/* MINI STATS */
.ms-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px}
.ms{background:#fff;border-radius:var(--r);padding:16px 18px;box-shadow:var(--sh);border:1px solid rgba(242,167,176,.12)}
.ms-v{font-size:1.2rem;font-weight:700;color:var(--dk)}
.ms-l{font-size:.68rem;color:var(--mu);margin-top:3px;text-transform:uppercase;letter-spacing:.08em}

/* CHART */
.cc{background:#fff;border-radius:var(--r);box-shadow:var(--sh);border:1px solid rgba(242,167,176,.12);overflow:hidden;margin-bottom:22px}
.ch{display:flex;align-items:center;justify-content:space-between;padding:18px 22px 14px;border-bottom:1px solid var(--border)}
.ct2{font-family:var(--sf);font-size:1.05rem;font-weight:500;color:var(--dk)}
.ctabs{display:flex;gap:6px}
.ctab{padding:5px 14px;border-radius:50px;border:1.5px solid var(--border);font-size:.7rem;font-weight:600;cursor:pointer;transition:all .2s;background:transparent;color:var(--mu)}
.ctab.active{background:var(--rd);color:#fff;border-color:var(--rd)}
.cb2{padding:18px 22px}

/* CARD */
.card{background:#fff;border-radius:var(--r);box-shadow:var(--sh);border:1px solid rgba(242,167,176,.12);overflow:hidden;margin-bottom:22px}
.card-h{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid var(--border)}
.card-t{font-family:var(--sf);font-size:1rem;font-weight:500;color:var(--dk)}
.sw{padding:12px 22px;border-bottom:1px solid var(--border)}
.sw input{width:100%;padding:9px 14px 9px 38px;border:1.5px solid #EDD5D9;border-radius:9px;font-size:.83rem;color:var(--dk);outline:none;transition:border .2s;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%23A07880' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 12px center}
.sw input:focus{border-color:var(--rd)}
.tw{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead th{padding:10px 14px;text-align:left;font-size:.64rem;font-weight:600;letter-spacing:.13em;text-transform:uppercase;color:var(--mu);background:#FDFAFB;border-bottom:1px solid var(--border)}
tbody td{padding:12px 14px;border-bottom:1px solid rgba(242,167,176,.1);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:rgba(253,244,246,.5)}
.ti{width:44px;height:44px;border-radius:8px;object-fit:cover;background:var(--bl)}
.tp{width:44px;height:44px;border-radius:8px;background:var(--bl);display:grid;place-items:center;font-size:1.2rem}
.tn{font-weight:600;color:var(--dk);font-size:.84rem}
.ts{font-size:.72rem;color:var(--mu);margin-top:1px}
.tpr{font-weight:700;color:var(--rd);font-size:.88rem}
.badge{display:inline-block;padding:3px 9px;border-radius:50px;font-size:.6rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase}
.br{background:#FDE8EC;color:var(--rk)}.bv{background:#DCFCE7;color:#166534}.bg{background:#FEF9EC;color:#92400E}.bb{background:#DBEAFE;color:#1E40AF}.bmu{background:#F3F4F6;color:#6B7280}.be{background:#FEF2F2;color:#991B1B}
.ab{width:30px;height:30px;border-radius:7px;border:none;cursor:pointer;display:grid;place-items:center;font-size:.82rem;transition:background .18s}
.av{background:#F0F7FF;color:#2563EB}.av:hover{background:#DBEAFE}
.ae{background:#F0FBF4;color:#166534}.ae:hover{background:#DCFCE7}
.ad{background:#FEF2F2;color:#991B1B}.ad:hover{background:#FECACA}
.dot{width:7px;height:7px;border-radius:50%;display:inline-block;flex-shrink:0}
.dg{background:#22C55E}.do{background:#F59E0B}.db{background:#3B82F6}.dm{background:#9CA3AF}.de{background:#EF4444}

/* 2 COL DASH */
.d2{display:grid;grid-template-columns:1.5fr 1fr;gap:20px;margin-bottom:22px}
@media(max-width:1050px){.d2{grid-template-columns:1fr}}
.sl2{padding:10px 0}
.sri{display:flex;align-items:center;justify-content:space-between;padding:9px 22px;border-bottom:1px solid var(--border)}
.sri:last-child{border-bottom:none}
.sri .lft{display:flex;align-items:center;gap:9px;font-size:.82rem;font-weight:500}

/* FORM PANEL */
.fp{background:#fff;border-radius:var(--r);box-shadow:var(--shm);border:1px solid rgba(242,167,176,.18);overflow:hidden;margin-bottom:24px;display:none}
.fp.open{display:block}
.fp-h{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid var(--border);background:linear-gradient(135deg,#FDF4F6,#fff)}
.fp-t{font-family:var(--sf);font-size:1.1rem;color:var(--dk);font-weight:500}
.xbtn{width:30px;height:30px;border-radius:7px;background:var(--bl);border:none;cursor:pointer;font-size:1rem;display:grid;place-items:center;color:var(--mu);transition:background .2s}
.xbtn:hover{background:var(--rose)}
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:22px}
.full{grid-column:1/-1}
.fg{display:flex;flex-direction:column;gap:5px}
.fl{font-size:.67rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--mu)}
.fi{padding:10px 13px;border:1.5px solid #EDD5D9;border-radius:9px;font-size:.84rem;color:var(--dk);outline:none;transition:border .2s;background:#fff;resize:vertical;width:100%}
.fi:focus{border-color:var(--rd)}
.fh{font-size:.65rem;color:var(--mu)}
.fck{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.82rem;font-weight:500;color:var(--dk)}
.fck input{width:16px;height:16px;accent-color:var(--rd);cursor:pointer}
.ff{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--border);background:#FDFAFB}
.bs{padding:10px 26px;background:var(--rd);color:#fff;border:none;border-radius:50px;font-size:.75rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;cursor:pointer;transition:background .2s}
.bs:hover{background:var(--rk)}
.bc{padding:10px 20px;background:var(--bl);color:var(--dk);border:none;border-radius:50px;font-size:.75rem;font-weight:600;cursor:pointer;transition:background .2s}
.bc:hover{background:var(--rose)}

/* UPLOAD */
.upz{border:2px dashed #EDD5D9;border-radius:10px;padding:22px;text-align:center;cursor:pointer;transition:border .2s,background .2s;position:relative}
.upz:hover,.upz.drag{border-color:var(--rd);background:#FDF4F6}
.upz input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.up-ico{font-size:2rem;margin-bottom:6px}
.up-txt{font-size:.78rem;color:var(--mu);line-height:1.6}
.up-txt strong{color:var(--rd)}
.prev-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:12px}
.prev-item{position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;background:var(--bl)}
.prev-item img{width:100%;height:100%;object-fit:cover}
.prev-item .rm{position:absolute;top:4px;right:4px;width:20px;height:20px;background:rgba(180,40,40,.85);color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:.7rem;display:grid;place-items:center}
.prg{height:4px;background:var(--bl);border-radius:4px;margin-top:8px;overflow:hidden;display:none}
.prg-b{height:100%;background:var(--rd);border-radius:4px;width:0;transition:width .3s}

/* EMPTY */
.empty{padding:48px 20px;text-align:center;color:var(--mu)}
.empty-i{font-size:2.2rem;opacity:.4;margin-bottom:10px}
</style>
</head>
<body>

<aside class="sb">
  <div class="sb-logo"><div class="wm">Jolly Beauty</div><div class="tg">Administration</div></div>
  <nav class="sb-nav">
    <div class="ns">Principal</div>
    <a href="?page=dashboard" class="ni <?= $section==='dashboard'?'active':'' ?>"><span class="ic">▣</span> Tableau de bord</a>
    <a href="?page=orders"    class="ni <?= $section==='orders'?'active':'' ?>">
      <span class="ic">📦</span> Commandes
      <?php if ($pendingOrders > 0): ?><span class="nb"><?= $pendingOrders ?></span><?php endif; ?>
    </a>
    <a href="?page=products"  class="ni <?= $section==='products'?'active':'' ?>"><span class="ic">💎</span> Produits <span style="margin-left:auto;font-size:.65rem;color:rgba(255,255,255,.4)"><?= $totalProducts ?></span></a>
    <a href="<?= $jbBase ?>/admin/add-product.php" class="ni"><span class="ic">＋</span> Ajouter produit</a>
    <a href="?page=users"     class="ni <?= $section==='users'?'active':'' ?>"><span class="ic">👥</span> Clients</a>
    <a href="?page=promo"     class="ni <?= $section==='promo'?'active':'' ?>"><span class="ic">🏷</span> Codes promo</a>
    <div class="ns">Boutique</div>
    <a href="<?= $jbBase ?>/index.php"    target="_blank" class="ni"><span class="ic">🌐</span> Voir le site</a>
    <a href="<?= $jbBase ?>/category.php?c=all" target="_blank" class="ni"><span class="ic">🛍</span> La boutique</a>
  </nav>
  <div class="sb-bot">
    <div class="sb-user"><div class="sb-av">A</div><div><div style="color:#fff;font-weight:600;font-size:.8rem">Admin</div><div>Jolly Beauty</div></div></div>
    <a href="?logout=1" class="ni" style="color:rgba(255,255,255,.45)"><span class="ic">🚪</span> Déconnexion</a>
  </div>
</aside>

<main class="main">
  <div class="tb">
    <div class="tb-title"><?php $ts=['dashboard'=>'Tableau de bord','products'=>'Produits','orders'=>'Commandes','users'=>'Clients','promo'=>'Codes promo']; echo $ts[$section]??'Admin'; ?></div>
    <div class="tb-r">
      <?php if ($section==='products'): ?><a href="<?= $jbBase ?>/admin/add-product.php" class="tbtn t-rose">＋ Ajouter un produit</a><?php endif; ?>
      <a href="<?= $jbBase ?>/index.php" target="_blank" class="tbtn t-ghost">🌐 Voir le site</a>
    </div>
  </div>

  <div class="ct">
    <?php if ($msg): ?>
    <div class="alert <?= $msgType==='success'?'a-ok':'a-err' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($section === 'dashboard'): ?>

    <!-- STAT CARDS -->
    <div class="sg">
      <div class="sc cv"><div class="si iv">💰</div><div><div class="sl">Chiffre d'affaires</div><div class="sv"><?= number_format($totalRevenue,0,',',' ') ?> €</div><div class="chip ch-ok">Commandes non annulées</div></div></div>
      <div class="sc cg"><div class="si ig">📦</div><div><div class="sl">Commandes</div><div class="sv"><?= $totalOrders ?></div><div class="chip <?= $pendingOrders>0?'ch-w':'ch-ok' ?>"><?= $pendingOrders>0 ? ('🕐 '.$pendingOrders.' en attente') : '✓ Tout est traité' ?></div></div></div>
      <div class="sc cr"><div class="si ir">💎</div><div><div class="sl">Produits</div><div class="sv"><?= $totalProducts ?></div><?php if($lowStock>0):?><div class="chip ch-w">⚠ <?=$lowStock?> stock bas</div><?php else:?><div class="chip ch-ok">✓ Stock OK</div><?php endif;?></div></div>
      <div class="sc cb"><div class="si ib">🧾</div><div><div class="sl">Panier moyen</div><div class="sv"><?= number_format($totalOrders>0?$totalRevenue/$totalOrders:0,0,',',' ') ?> €</div><div class="chip ch-ok">Sur <?= $totalOrders ?> commandes</div></div></div>
    </div>

    <!-- MINI STATS -->
    <div class="ms-grid">
      <div class="ms"><div class="ms-v"><?= $pendingOrders ?></div><div class="ms-l">En attente</div></div>
      <div class="ms"><div class="ms-v"><?= $statusStats['processing']??0 ?></div><div class="ms-l">En cours</div></div>
      <div class="ms"><div class="ms-v"><?= $statusStats['delivered']??0 ?></div><div class="ms-l">Livrées</div></div>
    </div>

    <!-- GRAPHIQUE -->
    <div class="cc">
      <div class="ch">
        <div class="ct2">Évolution des ventes</div>
        <div class="ctabs">
          <button class="ctab active" onclick="swC('revenue',this)">Chiffre d'affaires</button>
          <button class="ctab" onclick="swC('orders',this)">Commandes</button>
        </div>
      </div>
      <div class="cb2">
        <?php if (empty($chartMonths)): ?>
          <div class="empty"><div class="empty-i">📈</div><p>Aucune donnée disponible.</p></div>
        <?php else: ?>
          <canvas id="salesChart" height="90"></canvas>
        <?php endif; ?>
      </div>
    </div>

    <!-- DASH 2 COL -->
    <div class="d2">
      <div class="card">
        <div class="card-h"><div class="card-t">Dernières commandes</div><a href="?section=orders" style="font-size:.74rem;color:var(--rd);font-weight:600">Tout voir →</a></div>
        <div class="tw"><table>
          <thead><tr><th>Réf.</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th></tr></thead>
          <tbody>
          <?php if(empty($orders)):?><tr><td colspan="5"><div class="empty"><div class="empty-i">📦</div><p>Aucune commande.</p></div></td></tr><?php endif;?>
          <?php foreach(array_slice($orders,0,7) as $o):
            $sm=['pending'=>['En attente','bg'],'processing'=>['En cours','bb'],'shipped'=>['Expédiée','br'],'delivered'=>['Livrée','bv'],'cancelled'=>['Annulée','bmu']];
            [$sl,$sc]=$sm[$o['status']]??[$o['status'],'bmu'];
          ?><tr>
            <td><strong style="color:var(--rd)"><?= htmlspecialchars($o['order_ref']) ?></strong></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($o['client_name']??'Invité') ?></td>
            <td class="tpr"><?= formatPrice((float)$o['total']) ?></td>
            <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td>
            <td style="color:var(--mu);font-size:.76rem"><?= date('d/m/y',strtotime($o['created_at'])) ?></td>
          </tr><?php endforeach;?>
          </tbody>
        </table></div>
      </div>
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-h"><div class="card-t">Statuts commandes</div></div>
          <div class="sl2">
            <?php foreach(['pending'=>['En attente','do'],'processing'=>['En cours','db'],'shipped'=>['Expédiée','db'],'delivered'=>['Livrée','dg'],'cancelled'=>['Annulée','de']] as $k=>[$lb,$dt]):
              $n=$statusStats[$k]??0;
            ?><div class="sri"><div class="lft"><span class="dot <?=$dt?>"></span><?=$lb?></div><span class="badge <?=$n>0?'br':'bmu' ?>"><?=$n?></span></div><?php endforeach;?>
          </div>
        </div>
        <?php if (!empty($salesByCat)): ?>
        <div class="card">
          <div class="card-h"><div class="card-t">Ventes par catégorie</div></div>
          <div class="sl2">
            <?php foreach($salesByCat as $c): ?>
            <div class="sri"><div class="lft" style="font-size:.82rem"><?= htmlspecialchars($c['name']) ?></div><span class="tpr" style="font-size:.82rem"><?= formatPrice((float)$c['rev']) ?></span></div>
            <?php endforeach;?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php elseif ($section === 'products'): ?>

    <!-- FORM PANEL -->
    <div class="fp <?= ($editProduct||$msgType==='error')?'open':'' ?>" id="fp">
      <div class="fp-h">
        <div class="fp-t"><?= $editProduct?'✏️ Modifier le produit':'＋ Nouveau produit' ?></div>
        <button class="xbtn" onclick="closeFP()">✕</button>
      </div>
      <form method="POST" id="pform" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= (int)($editProduct['id']??0) ?>">
        <?php
          // Important: dans un attribut HTML, les sauts de ligne peuvent être normalisés,
          // ce qui "casse" la liste d'URLs et empêche la mise à jour des images.
          $imgListAttr = htmlspecialchars((string)($editProduct['img_list'] ?? ''), ENT_QUOTES, 'UTF-8');
          $imgListAttr = str_replace("\n", '&#10;', $imgListAttr);
        ?>
        <input type="hidden" name="images" id="img-field" value="<?= $imgListAttr ?>">
        <div class="fg2">

          <div class="fg full" style="flex-direction:row;gap:24px;padding:4px 0 10px;border-bottom:1px solid var(--border)">
            <label class="fck"><input type="checkbox" name="active" <?= (!$editProduct||$editProduct['active'])?'checked':'' ?>> Produit actif (visible sur le site)</label>
            <label class="fck"><input type="checkbox" name="featured" <?= !empty($editProduct['featured'])?'checked':'' ?>> ⭐ Produit vedette (page d'accueil)</label>
          </div>

          <div class="fg">
            <label class="fl">Nom du produit *</label>
            <input class="fi" type="text" name="name" value="<?= htmlspecialchars($editProduct['name']??'') ?>" placeholder="ex: Bracelet Charms Éclat" required oninput="autoSlug(this)">
          </div>
          <div class="fg">
            <label class="fl">Slug URL</label>
            <input class="fi" type="text" name="slug" id="slug-f" value="<?= htmlspecialchars($editProduct['slug']??'') ?>" placeholder="bracelet-charms-eclat">
            <span class="fh">Généré automatiquement — unique par produit</span>
          </div>

          <div class="fg">
            <label class="fl">Catégorie *</label>
            <select class="fi" name="category_id">
              <?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?= ($editProduct['category_id']??1)==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="fg">
            <label class="fl">Sous-catégorie</label>
            <input class="fi" type="text" name="sub" value="<?= htmlspecialchars($editProduct['sub']??'') ?>" placeholder="ex: Bracelets, Bagues, Corps…">
          </div>

          <div class="fg">
            <label class="fl">Prix (€) *</label>
            <input class="fi" type="number" name="price" step="0.01" min="0.01" value="<?= $editProduct['price']??'' ?>" placeholder="36.90" required>
          </div>
          <div class="fg">
            <label class="fl">Ancien prix barré (€)</label>
            <input class="fi" type="number" name="old_price" step="0.01" min="0" value="<?= $editProduct['old_price']??'' ?>" placeholder="Laisser vide si aucun">
          </div>

          <div class="fg">
            <label class="fl">Badge</label>
            <input class="fi" type="text" name="badge" value="<?= htmlspecialchars($editProduct['badge']??'') ?>" placeholder="Best-seller, Nouveau, Promo…">
          </div>
          <div class="fg">
            <label class="fl">Stock</label>
            <input class="fi" type="number" name="stock" min="0" value="<?= $editProduct['stock']??0 ?>">
          </div>

          <div class="fg full">
            <label class="fl">Accroche courte</label>
            <input class="fi" type="text" name="short" value="<?= htmlspecialchars($editProduct['short']??'') ?>" placeholder="Plaqué or 18k, charms symboliques, finition miroir.">
          </div>
          <div class="fg full">
            <label class="fl">Description complète</label>
            <textarea class="fi" name="description" rows="4" placeholder="Description détaillée du produit…"><?= htmlspecialchars($editProduct['description']??'') ?></textarea>
          </div>

          <!-- IMAGES -->
          <div class="fg full">
            <label class="fl">Images du produit</label>
            <div class="upz" id="dz" ondragover="dov(event)" ondragleave="dlv(event)" ondrop="ddr(event)">
              <input type="file" id="ffile" accept="image/jpeg,image/png,image/webp,image/gif" multiple onchange="hFiles(this.files)">
              <div class="up-ico">🖼️</div>
              <div class="up-txt"><strong>Cliquer pour choisir</strong> ou glisser-déposer<br><span style="font-size:.72rem">JPG, PNG, WEBP — max 5 Mo par image</span></div>
            </div>
            <div class="prg" id="prg"><div class="prg-b" id="prg-b"></div></div>
            <div style="margin-top:12px;display:flex;align-items:center;gap:10px">
              <div style="flex:1;height:1px;background:var(--border)"></div>
              <span style="font-size:.7rem;color:var(--mu);white-space:nowrap">ou coller une URL</span>
              <div style="flex:1;height:1px;background:var(--border)"></div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px">
              <input class="fi" type="url" id="url-in" placeholder="https://images.unsplash.com/photo-xxx?w=800" style="flex:1">
              <button type="button" onclick="addUrl()" style="padding:9px 16px;background:var(--bl);color:var(--dk);border:none;border-radius:9px;cursor:pointer;font-size:.78rem;font-weight:600;white-space:nowrap;transition:background .2s" onmouseover="this.style.background='var(--rose)'" onmouseout="this.style.background='var(--bl)'">+ Ajouter</button>
            </div>
            <div class="prev-grid" id="prev"></div>
            <span class="fh" style="margin-top:6px">La 1ère image = image principale du produit.</span>
          </div>

          <div class="fg">
            <label class="fl">Matériaux / Composition (un par ligne)</label>
            <textarea class="fi" name="materials" rows="4" placeholder="Acier inoxydable plaqué or 18k&#10;Résistant à l'eau&#10;Hypoallergénique"><?= htmlspecialchars($editProduct['mat_list']??'') ?></textarea>
          </div>
          <div class="fg">
            <label class="fl">Tailles disponibles (une par ligne)</label>
            <textarea class="fi" name="sizes" rows="4" placeholder="S — 16 cm&#10;M — 18 cm&#10;L — 20 cm"><?= htmlspecialchars($editProduct['size_list']??'') ?></textarea>
          </div>

        </div>
        <div class="ff">
          <button type="button" class="bc" onclick="closeFP()">Annuler</button>
          <button type="submit" name="save_product" class="bs"><?= $editProduct?'💾 Enregistrer les modifications':'✨ Créer le produit' ?></button>
        </div>
      </form>
    </div>

    <!-- LISTE PRODUITS -->
    <div class="card">
      <div class="card-h">
        <div class="card-t">Tous les produits <span style="color:var(--mu);font-weight:400;font-family:var(--ss);font-size:.82rem">(<?= count($products) ?>)</span></div>
      </div>
      <div class="sw"><input type="text" placeholder="Rechercher un produit…" oninput="fltrP(this.value)"></div>
      <div class="tw"><table>
        <thead><tr><th>Image</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody id="ptbl">
        <?php if(empty($products)):?><tr><td colspan="7"><div class="empty"><div class="empty-i">💎</div>
          <p><strong>Aucun produit en base.</strong> L’accueil peut quand même afficher des images “démo” (hors BDD) — importez <code>database.sql</code> (phpMyAdmin) ou créez un produit via « ＋ Ajouter un produit ».</p>
        </div></td></tr><?php endif;?>
        <?php foreach($products as $p): $img=!empty($p['images'])?$p['images'][0]:null; ?>
        <tr class="pr" data-n="<?= strtolower(htmlspecialchars($p['name'])) ?>">
          <td><?php if($img):?><img src="<?= htmlspecialchars($img) ?>" class="ti" loading="lazy"><?php else:?><div class="tp">🌸</div><?php endif;?></td>
          <td>
            <div class="tn"><?= htmlspecialchars($p['name']) ?></div>
            <div class="ts"><?= htmlspecialchars($p['sub']??'') ?></div>
            <?php if($p['featured']):?><span class="badge bg" style="margin-top:4px;font-size:.55rem">⭐ Vedette</span><?php endif;?>
          </td>
          <td><span class="badge br"><?= htmlspecialchars(ucfirst($p['category']??'')) ?></span></td>
          <td>
            <div class="tpr"><?= formatPrice($p['price']) ?></div>
            <?php if($p['old_price']):?><div style="font-size:.72rem;color:var(--mu);text-decoration:line-through"><?= formatPrice($p['old_price']) ?></div><?php endif;?>
          </td>
          <td><?php $st=(int)$p['stock'];?><span class="badge <?= $st>10?'bv':($st>0?'bg':'be') ?>"><?= $st>0?$st.' en stock':'Rupture' ?></span></td>
          <td>
            <?php if($p['active']):?><span style="display:flex;align-items:center;gap:6px"><span class="dot dg"></span>Actif</span>
            <?php else:?><span style="display:flex;align-items:center;gap:6px"><span class="dot dm"></span>Inactif</span><?php endif;?>
          </td>
          <td><div style="display:flex;gap:5px">
            <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="ab av" title="Voir">👁</a>
            <a href="?section=products&edit_product=<?= $p['id'] ?>" class="ab ae" title="Modifier">✏️</a>
            <a href="?section=products&delete_product=<?= $p['id'] ?>" class="ab ad" title="Supprimer" onclick="return confirm('Supprimer «<?= addslashes(htmlspecialchars($p['name'])) ?>» ? Action irréversible.')">🗑</a>
          </div></td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table></div>
    </div>

    <?php elseif ($section === 'orders'): ?>
    <div class="card">
      <div class="card-h"><div class="card-t">Toutes les commandes <span style="color:var(--mu);font-weight:400;font-family:var(--ss);font-size:.82rem">(<?= $totalOrders ?>)</span></div></div>
      <div class="tw"><table>
        <thead><tr><th>Réf.</th><th>Client</th><th>Total</th><th>Statut</th><th>Ville</th><th>Date</th></tr></thead>
        <tbody>
        <?php if(empty($orders)):?><tr><td colspan="6"><div class="empty"><div class="empty-i">📦</div><p>Aucune commande.</p></div></td></tr><?php endif;?>
        <?php foreach($orders as $o):
          $sm=['pending'=>['En attente','bg'],'processing'=>['En cours','bb'],'shipped'=>['Expédiée','br'],'delivered'=>['Livrée','bv'],'cancelled'=>['Annulée','bmu']];
          [$sl,$sc]=$sm[$o['status']]??[$o['status'],'bmu'];
        ?><tr>
          <td><strong style="color:var(--rd)"><?= htmlspecialchars($o['order_ref']) ?></strong></td>
          <td><div class="tn"><?= htmlspecialchars($o['client_name']??'Invité') ?></div></td>
          <td class="tpr"><?= formatPrice((float)$o['total']) ?></td>
          <td><span class="badge <?=$sc?>"><?=$sl?></span></td>
          <td style="font-size:.76rem;color:var(--mu)"><?= htmlspecialchars($o['shipping_city']??'—') ?></td>
          <td style="font-size:.76rem;color:var(--mu)"><?= date('d/m/Y H:i',strtotime($o['created_at'])) ?></td>
        </tr><?php endforeach;?>
        </tbody>
      </table></div>
    </div>

    <?php elseif ($section === 'users'):
    $users=$db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    ?>
    <div class="card">
      <div class="card-h"><div class="card-t">Clients inscrits <span style="color:var(--mu);font-weight:400;font-family:var(--ss);font-size:.82rem">(<?= count($users) ?>)</span></div></div>
      <div class="tw"><table>
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Code promo</th><th>Newsletter</th><th>Inscrit le</th></tr></thead>
        <tbody>
        <?php if(empty($users)):?><tr><td colspan="6"><div class="empty"><div class="empty-i">👥</div><p>Aucun client.</p></div></td></tr><?php endif;?>
        <?php foreach($users as $u):?><tr>
          <td><div class="tn"><?= htmlspecialchars($u['name']) ?></div></td>
          <td style="color:var(--mu);font-size:.82rem"><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge <?=$u['role']==='admin'?'br':'bb'?>"><?=$u['role']?></span></td>
          <td><?=$u['promo_code']?'<span class="badge bg">'.htmlspecialchars($u['promo_code']).'</span>':'—'?></td>
          <td><?=$u['newsletter']?'<span class="badge bv">✓ Abonné</span>':'<span class="badge bmu">Non</span>'?></td>
          <td style="font-size:.76rem;color:var(--mu)"><?= date('d/m/Y',strtotime($u['created_at'])) ?></td>
        </tr><?php endforeach;?>
        </tbody>
      </table></div>
    </div>
    <?php elseif ($section === 'promo'): ?>
    <?php $promoCodes = $db->query('SELECT * FROM promo_codes ORDER BY created_at DESC')->fetchAll(); ?>

    <div class="card">
      <div class="card-h"><div class="card-t">Créer un code promo</div></div>
      <div style="padding:22px">
        <form method="POST" style="display:grid;grid-template-columns:1.3fr .7fr .7fr 1fr;gap:12px;align-items:end">
          <div class="fg" style="margin:0">
            <label class="fl">Code</label>
            <input class="fi" name="code" placeholder="JOLLY15" required maxlength="40" style="text-transform:uppercase">
          </div>
          <div class="fg" style="margin:0">
            <label class="fl">Réduction (%)</label>
            <input class="fi" type="number" name="discount" min="1" max="90" step="1" placeholder="15" required>
          </div>
          <div class="fg" style="margin:0">
            <label class="fl">Max utilisations</label>
            <input class="fi" type="number" name="max_uses" min="1" step="1" placeholder="(illimité)">
          </div>
          <div class="fg" style="margin:0">
            <label class="fl">Expiration</label>
            <input class="fi" type="datetime-local" name="expires_at">
          </div>
          <div style="grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:6px">
            <label class="fck" style="margin:0"><input type="checkbox" name="active" checked> Actif</label>
            <button type="submit" name="create_promo" class="bs">＋ Créer</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-h"><div class="card-t">Liste des codes promo</div></div>
      <div class="tw"><table>
        <thead>
          <tr>
            <th>Code</th><th>Réduction</th><th>Utilisations</th><th>Expiration</th><th>Statut</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($promoCodes)): ?>
          <tr><td colspan="6"><div class="empty"><div class="empty-i">🏷</div><p>Aucun code promo.</p></div></td></tr>
        <?php endif; ?>
        <?php foreach ($promoCodes as $pc): ?>
          <tr>
            <td><strong style="color:var(--rd)"><?= htmlspecialchars($pc['code']) ?></strong></td>
            <td><?= (int)$pc['discount'] ?>%</td>
            <td><?= (int)$pc['used_count'] ?><?= $pc['max_uses'] ? ' / '.(int)$pc['max_uses'] : ' / ∞' ?></td>
            <td style="color:var(--mu);font-size:.78rem"><?= $pc['expires_at'] ? date('d/m/Y H:i', strtotime($pc['expires_at'])) : '—' ?></td>
            <td>
              <?php if ((int)$pc['active'] === 1): ?>
                <span class="badge bv">Actif</span>
              <?php else: ?>
                <span class="badge bmu">Inactif</span>
              <?php endif; ?>
            </td>
            <td>
              <a class="ab <?= (int)$pc['active']===1 ? 'ad' : 'ae' ?>" href="?page=promo&toggle_promo=<?= (int)$pc['id'] ?>" title="Activer/Désactiver">
                <?= (int)$pc['active']===1 ? '⏸' : '▶' ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
    <?php endif; ?>

  </div>
</main>

<script>
// ── CHART ─────────────────────────────────────────────────────
<?php if (!empty($chartMonths)): ?>
const CD = { labels:<?= json_encode($chartMonths) ?>, rev:<?= json_encode($chartRevenue) ?>, ord:<?= json_encode($chartOrders) ?> };
let SC;
function buildC(type) {
  const ctx = document.getElementById('salesChart'); if (!ctx) return;
  if (SC) SC.destroy();
  const isR = type==='revenue';
  SC = new Chart(ctx, {
    type: CD.labels.length>2 ? 'line' : 'bar',
    data: { labels: CD.labels, datasets: [{ label: isR?'CA (€)':'Commandes', data: isR?CD.rev:CD.ord,
      backgroundColor:'rgba(212,120,138,.12)', borderColor:'#D4788A', borderWidth:2.5,
      borderRadius:7, fill:true, tension:0.4, pointBackgroundColor:'#D4788A', pointRadius:5, pointHoverRadius:7 }] },
    options: { responsive:true, plugins:{ legend:{display:false}, tooltip:{ callbacks:{
      label: c => isR?' '+c.parsed.y.toLocaleString('fr-FR',{style:'currency',currency:'EUR'})
                     :' '+c.parsed.y+' commande(s)'
    }}}, scales:{ y:{beginAtZero:true,grid:{color:'rgba(242,167,176,.15)'},ticks:{color:'#A07880',font:{size:11}}}, x:{grid:{display:false},ticks:{color:'#A07880',font:{size:11}}} } }
  });
}
function swC(t, btn) {
  document.querySelectorAll('.ctab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active'); buildC(t);
}
buildC('revenue');
<?php endif; ?>

// ── FORM ──────────────────────────────────────────────────────
function openFP() { document.getElementById('fp').classList.add('open'); document.getElementById('fp').scrollIntoView({behavior:'smooth',block:'start'}); }
function closeFP(){ document.getElementById('fp').classList.remove('open'); }

// ── SLUG AUTO ─────────────────────────────────────────────────
const slugF = document.getElementById('slug-f');
let slugM = !!(slugF && slugF.value);
function autoSlug(inp) {
  if (slugM || !slugF) return;
  slugF.value = inp.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
}
slugF?.addEventListener('input', () => slugM = true);

// ── FILTRE TABLE ──────────────────────────────────────────────
function fltrP(v) {
  const q=v.toLowerCase().trim();
  document.querySelectorAll('#ptbl .pr').forEach(r=>r.style.display=(!q||r.dataset.n.includes(q))?'':'none');
}

// ── IMAGES ───────────────────────────────────────────────────
let imgs = [];
(function init(){
  const f=document.getElementById('img-field'); if(!f) return;
  const v=f.value.trim(); if(!v) return;
  v.split('\n').forEach(u=>u.trim()&&imgs.push(u.trim()));
  render();
})();

function render(){
  const g=document.getElementById('prev'); if(!g) return;
  g.innerHTML='';
  imgs.forEach((u,i)=>{
    const d=document.createElement('div'); d.className='prev-item';
    d.innerHTML=`<img src="${u}" loading="lazy" onerror="this.parentNode.style.background='var(--bl)'">
      <button type="button" class="rm" onclick="rmImg(${i})">✕</button>
      ${i===0?'<div style="position:absolute;bottom:4px;left:4px;background:var(--rd);color:#fff;font-size:.52rem;padding:2px 6px;border-radius:4px;font-weight:700">PRINCIPALE</div>':''}`;
    g.appendChild(d);
  });
  const f=document.getElementById('img-field'); if(f) f.value=imgs.join('\n');
}
function rmImg(i){ imgs.splice(i,1); render(); }
function addUrl(){
  const inp=document.getElementById('url-in'); const u=inp.value.trim();
  if(!u) return; if(!imgs.includes(u)) imgs.push(u); render(); inp.value='';
}
function hFiles(files){ Array.from(files).forEach(uploadF); }
function dov(e){ e.preventDefault(); document.getElementById('dz').classList.add('drag'); }
function dlv(e){ document.getElementById('dz').classList.remove('drag'); }
function ddr(e){ e.preventDefault(); document.getElementById('dz').classList.remove('drag'); hFiles(e.dataTransfer.files); }
async function uploadF(file){
  if(!file.type.startsWith('image/')){ alert('Format non supporté : '+file.name); return; }
  if(file.size>5*1024*1024){ alert('Fichier trop lourd (max 5 Mo) : '+file.name); return; }
  const p=document.getElementById('prg'), b=document.getElementById('prg-b');
  p.style.display='block'; b.style.width='30%';
  const fd=new FormData(); fd.append('product_image',file);
  try {
    b.style.width='65%';
    const r=await fetch(window.location.pathname,{method:'POST',body:fd});
    b.style.width='90%';
    const d=await r.json();
    if(d.success){ if(!imgs.includes(d.url)) imgs.push(d.url); render(); b.style.width='100%'; setTimeout(()=>{p.style.display='none';b.style.width='0';},500); }
    else { alert('Erreur : '+(d.error||'Inconnue')); p.style.display='none'; }
  } catch(e){ alert('Erreur réseau.'); p.style.display='none'; }
}

<?php if($editProduct): ?>document.getElementById('fp')?.classList.add('open');<?php endif; ?>
</script>
</body>
</html>