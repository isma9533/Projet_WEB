
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
?>