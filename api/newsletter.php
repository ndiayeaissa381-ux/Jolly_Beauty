<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$email = sanitize($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
    exit;
}

$result = subscribeNewsletter($email);

if ($result === 'already') {
    echo json_encode(['success' => true, 'message' => 'Vous êtes déjà inscrit(e) à notre newsletter !']);
} elseif ($result === true) {
    echo json_encode(['success' => true, 'message' => 'Merci ! Vous êtes maintenant inscrit(e) à la newsletter Jolly Beauty.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}