<?php
// ============================================================
// JOLLY BEAUTY — Configuration & Connexion Base de Données
// ============================================================
// Modifiez uniquement les constantes DB_* selon votre XAMPP

define('DB_HOST', 'localhost');
define('DB_NAME', 'jollybeauty');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP : vide par défaut
define('DB_PORT', 3306);

// ── ADMIN ────────────────────────────────────────────────────
define('ADMIN_USER', 'admin');
// Mot de passe admin (changez en production !)
define('ADMIN_PASS_PLAIN', 'JollyBeauty2025!');

// ── APP ──────────────────────────────────────────────────────
/** Chemin URL du dossier du site sous DOCUMENT_ROOT (ex. /Jolly_Beauty ou vide si vhost à la racine). */
function jb_detect_base_url(): string {
    // 1) Détection la plus fiable côté WAMP: partir du chemin URL exécuté.
    // Exemple: /Jolly2/Jolly_Beauty/admin/index.php -> /Jolly2/Jolly_Beauty
    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    $script = str_replace('\\', '/', $script);
    if ($script !== '') {
        $appDir = basename(str_replace('\\', '/', (string)realpath(__DIR__ . '/..')));
        if ($appDir !== '' && preg_match('~^(.*?/' . preg_quote($appDir, '~') . ')(?:/.*)?$~', $script, $m)) {
            return $m[1];
        }
    }

    // 2) Fallback: tentative via DOCUMENT_ROOT + realpath (utile en vhost)
    $doc = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if ($doc === '') {
        return '/Jolly_Beauty';
    }
    $docReal = realpath($doc);
    $appReal = realpath(__DIR__ . '/..');
    if ($docReal === false || $appReal === false) {
        return '/Jolly_Beauty';
    }
    $docNorm = str_replace('\\', '/', rtrim($docReal, '/'));
    $appNorm = str_replace('\\', '/', rtrim($appReal, '/'));
    if (!str_starts_with($appNorm, $docNorm)) {
        return '/Jolly_Beauty';
    }
    $rel = substr($appNorm, strlen($docNorm));
    return $rel === '' ? '' : $rel;
}

define('BASE_URL', jb_detect_base_url());
$jbHost   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$jbScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('APP_URL', $jbScheme . '://' . $jbHost . (BASE_URL === '' ? '' : BASE_URL));
define('APP_NAME', 'Jolly Beauty');

// ── SESSION ──────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── PDO CONNECTION ───────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST, DB_PORT, DB_NAME);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // En production, logguez l'erreur et affichez une page d'erreur
            die('<div style="font-family:sans-serif;padding:40px;text-align:center">
                <h2 style="color:#C97070">⚠ Connexion base de données impossible</h2>
                <p>Vérifiez que XAMPP est démarré et que la base <strong>jollybeauty</strong> existe.<br>
                Importez <code>database.sql</code> via phpMyAdmin.</p>
                <small style="color:#888">' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}

// ── HELPERS AUTH ─────────────────────────────────────────────
function isLoggedIn(): bool  { return !empty($_SESSION['jb_user']); }
function isAdmin(): bool     { return !empty($_SESSION['jb_admin']); }
function currentUser(): ?array { return $_SESSION['jb_user'] ?? null; }

// ── HELPERS FORMATAGE ────────────────────────────────────────
function formatPrice(float $p): string {
    return number_format($p, 2, ',', ' ') . ' €';
}

/** URL affichable pour la vignette panier / checkout (localStorage). */
function jb_cart_item_image_url(array $item): string {
    $url = trim((string)($item['image'] ?? ''));
    if ($url === '') {
        return '';
    }
    if (preg_match('~^https?://~i', $url)) {
        return $url;
    }
    if (BASE_URL !== '' && str_starts_with($url, BASE_URL . '/')) {
        return $url;
    }
    if (str_starts_with($url, '/assets/') || str_starts_with($url, '/images/')) {
        return (BASE_URL === '' ? '' : BASE_URL) . $url;
    }
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return $url;
    }
    $cat = trim((string)($item['category'] ?? ''));
    $fname = ltrim(str_replace('\\', '/', $url), '/');
    return BASE_URL . '/assets/images/' . ($cat !== '' ? $cat . '/' : '') . $fname;
}

function activeClass(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

function sanitize(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

// ── FONCTIONS PRODUITS ───────────────────────────────────────
function getProducts(
    ?string $cat = null,
    string $query = '',
    string $sort = 'default',
    int $limit = 100,
    bool $includeInactive = false
): array {
    $db  = getDB();
    $sql = "SELECT p.*, c.slug AS category,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                   GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                   GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
            FROM products p
            JOIN categories c ON c.id = p.category_id
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_materials pm ON pm.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            WHERE 1=1";
    $params = [];

    if (!$includeInactive) {
        $sql .= ' AND p.active = 1';
    }

    if ($cat && $cat !== 'all') {
        $sql    .= ' AND LOWER(c.slug) = LOWER(?)';
        $params[] = $cat;
    }
    if ($query !== '') {
        $sql    .= ' AND (p.name LIKE ? OR p.short LIKE ?)';
        $like    = "%{$query}%";
        $params[] = $like;
        $params[] = $like;
    }
    $sql .= ' GROUP BY p.id';
    if ($sort === 'price_asc')  $sql .= ' ORDER BY p.price ASC';
    elseif ($sort === 'price_desc') $sql .= ' ORDER BY p.price DESC';
    elseif ($sort === 'newest')  $sql .= ' ORDER BY p.id DESC';
    else $sql .= ' ORDER BY p.featured DESC, p.id ASC';
    $sql .= " LIMIT $limit";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return array_map('_hydrateProduct', $rows);
}

function getProductBySlug(string $slug): ?array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.slug AS category,
                GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
         FROM products p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id
         LEFT JOIN product_materials pm ON pm.product_id = p.id
         LEFT JOIN product_sizes ps ON ps.product_id = p.id
         WHERE LOWER(p.slug) = LOWER(?) AND p.active = 1
         GROUP BY p.id LIMIT 1"
    );
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ? _hydrateProduct($row) : null;
}

function getFeaturedProducts(): array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.slug AS category,
                GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
         FROM products p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id
         LEFT JOIN product_materials pm ON pm.product_id = p.id
         LEFT JOIN product_sizes ps ON ps.product_id = p.id
         WHERE p.featured = 1 AND p.active = 1
         GROUP BY p.id ORDER BY p.id ASC"
    );
    $stmt->execute();
    return array_map('_hydrateProduct', $stmt->fetchAll());
}

function _hydrateProduct(array $row): array {
    $row['images']    = $row['img_list']  ? explode('||', $row['img_list'])  : [];
    $row['materials'] = $row['mat_list']  ? explode('||', $row['mat_list'])  : [];
    $row['sizes']     = $row['size_list'] ? explode('||', $row['size_list']) : [];
    $row['price']     = (float) $row['price'];
    $row['old_price'] = $row['old_price'] ? (float) $row['old_price'] : null;
    unset($row['img_list'], $row['mat_list'], $row['size_list']);

    // Normalise les URLs d'images pour éviter les images "cassées"
    $cat = (string)($row['category'] ?? '');
    $row['images'] = array_values(array_filter(array_map(
        fn($u, $i) => resolveProductImageUrl((string)$u, $cat, (int)$i),
        $row['images'],
        array_keys($row['images'])
    )));
    if (empty($row['images'])) {
        $fb = resolveProductImageUrl('', $cat, 0);
        if ($fb) {
            $row['images'] = [$fb];
        }
    }
    return $row;
}

function jb_fs_to_public_url(string $absFile): ?string {
    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        return null;
    }
    $rp = realpath($absFile);
    if ($rp === false || !is_file($rp)) {
        return null;
    }
    $rootNorm = str_replace('\\', '/', $root);
    $fileNorm = str_replace('\\', '/', $rp);
    if (!str_starts_with($fileNorm, $rootNorm)) {
        return null;
    }
    $rel = substr($fileNorm, strlen($rootNorm));
    return BASE_URL . '/' . ltrim(str_replace('\\', '/', $rel), '/');
}

/** Tente de faire correspondre une valeur BDD à un fichier réel sous le projet. */
function jb_try_resolve_local_image(string $url): ?string {
    $root = realpath(__DIR__ . '/..');
    if ($root === false || trim($url) === '') {
        return null;
    }
    $u = str_replace('\\', '/', trim($url));
    $tryPaths = [];

    if (BASE_URL !== '' && str_starts_with($u, BASE_URL . '/')) {
        $tryPaths[] = $root . '/' . ltrim(substr($u, strlen(BASE_URL)), '/');
    }
    if (str_starts_with($u, '/assets/') || str_starts_with($u, '/images/')) {
        $tryPaths[] = $root . str_replace('/', DIRECTORY_SEPARATOR, $u);
        if (BASE_URL !== '') {
            $tryPaths[] = $root . DIRECTORY_SEPARATOR . trim(BASE_URL, '/') . str_replace('/', DIRECTORY_SEPARATOR, $u);
        }
    }
    if (str_starts_with($u, '/') && !str_starts_with($u, '//')) {
        $tryPaths[] = $root . str_replace('/', DIRECTORY_SEPARATOR, $u);
    }
    if (!preg_match('~^https?://~i', $u) && !str_starts_with($u, '/')) {
        $rel = ltrim($u, '/');
        $tryPaths[] = $root . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $tryPaths[] = $root . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    }

    foreach ($tryPaths as $fs) {
        $rp = realpath($fs);
        if ($rp !== false && is_file($rp)) {
            $got = jb_fs_to_public_url($rp);
            if ($got !== null) {
                return $got;
            }
        }
    }
    return null;
}

function getCategoryImagePool(string $category): array {
    static $cache = [];
    $category = strtolower(trim($category));
    if ($category === '') {
        return [];
    }
    if (isset($cache[$category])) {
        return $cache[$category];
    }

    $dir = __DIR__ . '/../assets/images/' . $category . '/';
    if (!is_dir($dir)) {
        return $cache[$category] = [];
    }

    $files = array_values(array_filter(scandir($dir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $f)));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    $cache[$category] = array_map(
        fn($f) => BASE_URL . '/assets/images/' . $category . '/' . $f,
        $files
    );
    return $cache[$category];
}

function pickCategoryImagePool(string $category, int $idx): ?string {
    $first = strtolower(trim($category));
    $order = array_values(array_unique(array_filter([$first, 'bijoux', 'soins', 'coffrets', 'produits', 'slider'])));
    foreach ($order as $try) {
        $p = getCategoryImagePool($try);
        if (!empty($p)) {
            return $p[$idx % count($p)];
        }
    }
    return null;
}

/**
 * Résout une URL d'image : vérifie le fichier sur disque, évite les chemins BDD faux,
 * et utilise les images locales du dossier catégorie si l’URL externe ou le chemin ne marche pas.
 */
function resolveProductImageUrl(string $url, string $category, int $idx = 0): ?string {
    $url = trim($url);
    if (str_starts_with($url, '//')) {
        $url = 'https:' . $url;
    }

    if (preg_match('~^([a-zA-Z]:[\\\\/]|\\\\\\\\)~', $url)) {
        $norm = str_replace('\\', '/', $url);
        if (preg_match('~/assets/images/(.+)$~i', $norm, $m)) {
            $url = 'assets/images/' . $m[1];
        } elseif (preg_match('~/([^/]+\.(jpe?g|png|webp|gif))$~i', $norm, $m)) {
            $url = $m[1];
        } else {
            return pickCategoryImagePool($category, $idx);
        }
    }

    if ($url === '') {
        return pickCategoryImagePool($category, $idx);
    }

    if (preg_match('~^https?://~i', $url)) {
        return pickCategoryImagePool($category, $idx) ?? $url;
    }

    $resolved = jb_try_resolve_local_image($url);
    if ($resolved !== null) {
        return $resolved;
    }

    return pickCategoryImagePool($category, $idx)
        ?? (BASE_URL . '/assets/images/' . ltrim(str_replace('\\', '/', $url), '/'));
}

function getCategories(): array {
    static $cats = null;
    if ($cats === null) {
        $cats = getDB()->query('SELECT * FROM categories ORDER BY sort_order ASC, id ASC')->fetchAll();
    }
    return $cats;
}

function getCategoryBySlug(string $slug): ?array {
    $stmt = getDB()->prepare('SELECT * FROM categories WHERE LOWER(slug) = LOWER(?) LIMIT 1');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

// ── FONCTIONS UTILISATEURS ───────────────────────────────────
function getUserByEmail(string $email): ?array {
    $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: null;
}

function createUser(string $name, string $email, string $password): int {
    $stmt = getDB()->prepare(
        'INSERT INTO users (name,email,password,promo_code) VALUES (?,?,?,?)'
    );
    $stmt->execute([
        trim($name),
        strtolower(trim($email)),
        password_hash($password, PASSWORD_BCRYPT),
        'BIENVENUE10',
    ]);
    return (int) getDB()->lastInsertId();
}

// ── COMMANDES ────────────────────────────────────────────────
function getOrders(int $limit = 20, int $offset = 0): array {
    $stmt = getDB()->prepare(
        "SELECT o.*, COALESCE(u.name, o.guest_name) AS client_name
         FROM orders o
         LEFT JOIN users u ON u.id = o.user_id
         ORDER BY o.created_at DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

function countOrders(): int {
    return (int) getDB()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
}

function getTotalRevenue(): float {
    return (float) getDB()->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
}

// ── PROMO ────────────────────────────────────────────────────
function validatePromoCode(string $code, float $subtotal = 0): ?array {
    $stmt = getDB()->prepare(
        "SELECT * FROM promo_codes
         WHERE code = ? AND active = 1
           AND (max_uses IS NULL OR used_count < max_uses)
           AND (expires_at IS NULL OR expires_at > NOW())
         LIMIT 1"
    );
    $stmt->execute([strtoupper($code)]);
    $row = $stmt->fetch();
    if (!$row) return null;

    // Schéma actuel: `discount` = pourcentage
    $row['discount_type']  = 'percent';
    $row['discount_value'] = (int)($row['discount'] ?? 0);
    return $row;
}

// ── NEWSLETTER ───────────────────────────────────────────────
function subscribeNewsletter(string $email): string|bool {
    try {
        $email = strtolower(trim($email));
        // Vérifier si l'email existe déjà
        $check = getDB()->prepare('SELECT id FROM newsletter WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) {
            return 'already';
        }
        
        $stmt = getDB()->prepare(
            'INSERT INTO newsletter (email, subscribed) VALUES (?, 1)'
        );
        if ($stmt->execute([$email])) {
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// ── FALLBACK si DB non dispo (mode statique) ─────────────────
// Garde les données statiques comme fallback en cas d'erreur DB
function getProductsFallback(): array {
    return [
        ['id'=>'p1','slug'=>'bracelet-charms-eclat','category'=>'bijoux','sub'=>'Bracelets',
         'name'=>'Bracelet Charms Éclat','short'=>'Plaqué or 18k, charms symboliques, finition miroir.',
         'description'=>'Le Bracelet Charms Éclat est la pièce signature de Jolly Beauty.','price'=>36.90,'old_price'=>null,
         'badge'=>'Best-seller','rating'=>5,'reviews'=>142,'stock'=>24,'featured'=>true,
         'images'=>['https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=800&q=80'],
         'materials'=>['Acier inoxydable plaqué or 18k'],'sizes'=>['S — 16 cm','M — 18 cm','L — 20 cm']],
    ];
}