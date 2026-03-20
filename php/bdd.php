
<?php
$host = "localhost";
$dbname = "bloutub";    
$user = "root";        
$pass = "";             

try {
   
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
   
    echo "Connexion à la base de données bloutub réussie !";

} catch (PDOException $e) {
   
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?><?php
require_once 'bdd.php';

function addFavorite($userId, $postId) {
    try {
        $stmt = $pdo->prepare('INSERT IGNORE INTO favorites (user_id, post_id) VALUES (:user_id, :post_id)');
        $stmt->execute([':user_id' => $userId, ':post_id' => $postId]);
        return true; // succès (ou ignoré si déjà favori)
    } catch (PDOException $e) {
        return false; // erreur
    }
}

// Exemple d'usage (dans une page PHP après connexion) :
if (isset($_POST['add_favorite']) && isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $postId = (int)$_POST['post_id'];
    if (addFavorite($userId, $postId)) {
        echo "Favori ajouté !";
    }
}