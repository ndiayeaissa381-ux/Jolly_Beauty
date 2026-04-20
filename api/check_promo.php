<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$code = sanitize($_GET['code'] ?? '');
if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

$promo = validatePromoCode($code, 0); // Vérifier le code avec montant minimum de 0
if ($promo) {
    $label = '-' . (int)($promo['discount_value'] ?? ($promo['discount'] ?? 0)) . '%';
    echo json_encode(['valid' => true, 'label' => $label]);
} else {
    echo json_encode(['valid' => false]);
}
exit;