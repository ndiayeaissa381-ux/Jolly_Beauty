<?php
/**
 * Script de test pour diagnostiquer les problèmes de connexion
 */
require_once __DIR__ . '/includes/config.php';

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Debug Login - Jolly Beauty</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #d8a7a7; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
.success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
.error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
.info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #f2f2f2; }
.btn { display: inline-block; padding: 10px 20px; background: #d8a7a7; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
.btn:hover { background: #c48b8b; }
</style>
</head>
<body>
<div class="container">
    <h1>🔍 Diagnostic de connexion - Jolly Beauty</h1>
    
    <?php
    // Test 1: Connexion à la base de données
    echo '<div class="test-section">';
    echo '<h2>1. Connexion à la base de données</h2>';
    try {
        $db = getDB();
        echo '<div class="success">✅ Connexion à la base de données réussie</div>';
        echo '<p><strong>Base de données:</strong> ' . DB_NAME . '</p>';
        echo '<p><strong>Hôte:</strong> ' . DB_HOST . '</p>';
        echo '<p><strong>Port:</strong> ' . DB_PORT . '</p>';
    } catch (Exception $e) {
        echo '<div class="error">❌ Erreur de connexion: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';
    
    // Test 2: Vérification de la table users
    echo '<div class="test-section">';
    echo '<h2>2. Structure de la table users</h2>';
    try {
        $db = getDB();
        $stmt = $db->query("DESCRIBE users");
        $columns = $stmt->fetchAll();
        echo '<div class="success">✅ Table users trouvée</div>';
        echo '<table>';
        echo '<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th></tr>';
        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($col['Field']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Key']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } catch (Exception $e) {
        echo '<div class="error">❌ Erreur table users: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';
    
    // Test 3: Liste des utilisateurs
    echo '<div class="test-section">';
    echo '<h2>3. Utilisateurs dans la base de données</h2>';
    try {
        $db = getDB();
        $stmt = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY id");
        $users = $stmt->fetchAll();
        
        if (empty($users)) {
            echo '<div class="error">❌ Aucun utilisateur trouvé dans la base de données</div>';
            echo '<p><strong>Solution:</strong> Importez le fichier database.sql via phpMyAdmin</p>';
        } else {
            echo '<div class="success">✅ ' . count($users) . ' utilisateur(s) trouvé(s)</div>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Date création</th></tr>';
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                echo '<td>' . htmlspecialchars($user['name']) . '</td>';
                echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Erreur lecture utilisateurs: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';
    
    // Test 4: Test de fonction getUserByEmail
    echo '<div class="test-section">';
    echo '<h2>4. Test de la fonction getUserByEmail()</h2>';
    try {
        $testEmail = 'sophie@example.com';
        $user = getUserByEmail($testEmail);
        
        if ($user) {
            echo '<div class="success">✅ Utilisateur trouvé pour ' . htmlspecialchars($testEmail) . '</div>';
            echo '<pre>' . print_r($user, true) . '</pre>';
            
            // Test de vérification du mot de passe
            echo '<h3>Test de vérification du mot de passe</h3>';
            if (password_verify('demo1234', $user['password'])) {
                echo '<div class="success">✅ Mot de passe "demo1234" valide</div>';
            } else {
                echo '<div class="error">❌ Mot de passe "demo1234" invalide</div>';
            }
        } else {
            echo '<div class="error">❌ Aucun utilisateur trouvé pour ' . htmlspecialchars($testEmail) . '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Erreur fonction getUserByEmail: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';
    
    // Test 5: Configuration PHP
    echo '<div class="test-section">';
    echo '<h2>5. Configuration PHP</h2>';
    echo '<table>';
    echo '<tr><th>Paramètre</th><th>Valeur</th></tr>';
    echo '<tr><td>PHP Version</td><td>' . htmlspecialchars(phpversion()) . '</td></tr>';
    echo '<tr><td>Session Status</td><td>' . htmlspecialchars(session_status()) . '</td></tr>';
    echo '<tr><td>Session ID</td><td>' . htmlspecialchars(session_id()) . '</td></tr>';
    echo '<tr><td>upload_max_filesize</td><td>' . htmlspecialchars(ini_get('upload_max_filesize')) . '</td></tr>';
    echo '<tr><td>post_max_size</td><td>' . htmlspecialchars(ini_get('post_max_size')) . '</td></tr>';
    echo '<tr><td>memory_limit</td><td>' . htmlspecialchars(ini_get('memory_limit')) . '</td></tr>';
    echo '</table>';
    echo '</div>';
    
    // Test 6: Créer un utilisateur de test
    echo '<div class="test-section">';
    echo '<h2>6. Créer un utilisateur de test</h2>';
    if (isset($_POST['create_test_user'])) {
        try {
            $testName = 'Test User';
            $testEmail = 'test@jollybeauty.fr';
            $testPassword = 'test1234';
            
            // Vérifier si l'utilisateur existe déjà
            if (getUserByEmail($testEmail)) {
                echo '<div class="info">ℹ️ L\'utilisateur de test existe déjà</div>';
            } else {
                // Créer l'utilisateur
                $userId = createUser($testName, $testEmail, $testPassword);
                echo '<div class="success">✅ Utilisateur de test créé (ID: ' . $userId . ')</div>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($testEmail) . '</p>';
                echo '<p><strong>Mot de passe:</strong> ' . htmlspecialchars($testPassword) . '</p>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Erreur création utilisateur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<form method="post">';
        echo '<input type="hidden" name="create_test_user" value="1">';
        echo '<button type="submit" class="btn">Créer un utilisateur de test</button>';
        echo '</form>';
        echo '<p><small>Cela créera l\'utilisateur test@jollybeauty.fr avec le mot de passe test1234</small></p>';
    }
    echo '</div>';
    ?>
    
    <div class="test-section">
        <h2>🔧 Actions recommandées</h2>
        <div class="info">
            <p><strong>Si aucun utilisateur n'est trouvé:</strong></p>
            <ol>
                <li>Importez le fichier <code>database.sql</code> via phpMyAdmin</li>
                <li>Vérifiez que XAMPP est bien démarré (Apache + MySQL)</li>
                <li>Assurez-vous que la base de données <code>jollybeauty</code> existe</li>
            </ol>
            
            <p><strong>Comptes de démonstration disponibles:</strong></p>
            <ul>
                <li><strong>Admin:</strong> admin@jollybeauty.fr / demo1234</li>
                <li><strong>Client 1:</strong> sophie@example.com / demo1234</li>
                <li><strong>Client 2:</strong> amina@example.com / demo1234</li>
            </ul>
        </div>
        
        <p style="margin-top: 20px;">
            <a href="<?= $jbBase ?>/login.php" class="btn">→ Retour à la page de connexion</a>
        </p>
    </div>
</div>
</body>
</html>
