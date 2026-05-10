<?php
/**
 * API pour gérer les favoris des utilisateurs
 */
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$userId = (int)$_SESSION['jb_user']['id'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        // Ajouter un produit aux favoris
        $productId = $_POST['product_id'] ?? '';
        if (empty($productId)) {
            echo json_encode(['success' => false, 'error' => 'ID produit invalide']);
            exit;
        }
        
        try {
            // Vérifier si le produit existe (base de données ou mock)
            if (strpos($productId, 'mock-') === 0) {
                // Produit mock - toujours accepter
                $productExists = true;
            } else {
                // Produit en base de données
                $stmt = getDB()->prepare('SELECT id FROM products WHERE id = ? AND active = 1');
                $stmt->execute([$productId]);
                $productExists = $stmt->fetch() !== false;
            }
            
            if (!$productExists) {
                echo json_encode(['success' => false, 'error' => 'Produit non trouvé']);
                exit;
            }
            
            // Vérifier si déjà en favoris
            $stmt = getDB()->prepare('SELECT id FROM favorites WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$userId, $productId]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Déjà en favoris']);
                exit;
            }
            
            // Ajouter aux favoris
            $stmt = getDB()->prepare('INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$userId, $productId]);
            
            echo json_encode(['success' => true, 'message' => 'Ajouté aux favoris']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
        break;
        
    case 'remove':
        // Retirer un produit des favoris
        $productId = $_POST['product_id'] ?? '';
        if (empty($productId)) {
            echo json_encode(['success' => false, 'error' => 'ID produit invalide']);
            exit;
        }
        
        try {
            $stmt = getDB()->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$userId, $productId]);
            
            echo json_encode(['success' => true, 'message' => 'Retiré des favoris']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
        break;
        
    case 'list':
        // Lister les favoris de l'utilisateur
        try {
            $stmt = getDB()->prepare("
                SELECT p.*, f.created_at as favorite_date,
                       GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order) as images
                FROM favorites f
                JOIN products p ON p.id = f.product_id
                LEFT JOIN product_images pi ON pi.product_id = p.id
                WHERE f.user_id = ? AND p.active = 1
                GROUP BY p.id
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$userId]);
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traiter les images
            foreach ($favorites as &$fav) {
                if (!empty($fav['images'])) {
                    $fav['images'] = explode(',', $fav['images']);
                } else {
                    $fav['images'] = [];
                }
            }
            
            echo json_encode(['success' => true, 'favorites' => $favorites]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
        break;
        
    case 'check':
        // Vérifier si un produit est en favoris
        $productId = $_GET['product_id'] ?? '';
        if (empty($productId)) {
            echo json_encode(['success' => false, 'error' => 'ID produit invalide']);
            exit;
        }
        
        try {
            $stmt = getDB()->prepare('SELECT id FROM favorites WHERE user_id = ? AND product_id = ?');
            $stmt->execute([$userId, $productId]);
            $isFavorite = $stmt->fetch() !== false;
            
            echo json_encode(['success' => true, 'is_favorite' => $isFavorite]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Action invalide']);
        break;
}
