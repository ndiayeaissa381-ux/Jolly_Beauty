<?php
/**
 * Synchronise le panier JS (localStorage) vers $_SESSION['cart'] pour checkout.php
 */
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$list = $data['cart'] ?? (is_array($data) ? $data : null);
if (!is_array($list)) {
    echo json_encode(['ok' => false, 'error' => 'invalid']);
    exit;
}

$normalized = [];
foreach ($list as $item) {
    if (!is_array($item)) {
        continue;
    }
    $qty = (int)($item['quantity'] ?? $item['qty'] ?? 1);
    if ($qty < 1) {
        $qty = 1;
    }
    $pid = $item['id'] ?? null;
    if ($pid !== null && is_numeric($pid)) {
        $pid = (int)$pid;
    }
    $normalized[] = [
        'id'       => $pid,
        'name'     => (string)($item['name'] ?? ''),
        'price'    => (float)($item['price'] ?? 0),
        'qty'      => $qty,
        'image'    => (string)($item['image'] ?? ''),
        'category' => (string)($item['category'] ?? ''),
    ];
}

$_SESSION['cart'] = $normalized;
echo json_encode(['ok' => true, 'count' => count($normalized)]);
