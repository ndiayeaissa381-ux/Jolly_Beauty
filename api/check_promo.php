<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$code = sanitize($_GET['code'] ?? '');
if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

$promo = validatePromoCode($code, 0); // pass 0 for min check, real check at order time
if ($promo) {
    $label = $promo['discount_type'] === 'percent'
        ? '-' . (int)$promo['discount_value'] . '%'
        : '-' . formatPrice($promo['discount_value']);
    echo json_encode(['valid' => true, 'label' => $label]);
} else {
    echo json_encode(['valid' => false]);
}