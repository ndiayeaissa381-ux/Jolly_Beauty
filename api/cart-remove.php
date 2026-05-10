<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['item_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$itemId = $input['item_id'];
$cart = [];

// Get current cart from session or localStorage
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
}

// Remove item by ID (handle both numeric IDs and index fallbacks)
$removed = false;
if (is_array($cart)) {
    foreach ($cart as $key => $item) {
        // Check if the item matches by ID or by array index
        $itemMatches = false;
        
        // Case 1: Check by actual product ID
        if (isset($item['id']) && $item['id'] !== null && $item['id'] === $itemId) {
            $itemMatches = true;
        }
        // Case 2: Check by array index (when item has no ID)
        else if ((string)$key === (string)$itemId) {
            $itemMatches = true;
        }
        
        if ($itemMatches) {
            unset($cart[$key]);
            $removed = true;
            break;
        }
    }
    
    // Re-index array
    $cart = array_values($cart);
    
    // Update session
    $_SESSION['cart'] = $cart;
}

if ($removed) {
    echo json_encode([
        'success' => true,
        'message' => 'Article supprimé du panier',
        'cart_count' => count($cart),
        'cart_items' => $cart,
        'removed_item_id' => $itemId
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Article non trouvé dans le panier']);
}
?>
