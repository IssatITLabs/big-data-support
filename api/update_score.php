<?php
// Fichier : /api/update_score.php
// Rôle : Mettre à jour le champ TOTPOINTS de l'utilisateur dans db_BD.

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
http_response_code(200);

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$score = (int)($input['score'] ?? 0); 

// ------------------------------------------------
// ⚠️ MODIFIEZ AVEC VOS VRAIS ACCÈS MYSQL ⚠️
$servername = "localhost"; 
$db_user = "root"; 
$db_pass = ""; 
$dbname = "db_BD"; 
// ------------------------------------------------

if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Identifiant utilisateur manquant']);
    exit;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour mettre à jour TOTPOINTS de l'utilisateur
    $stmt = $conn->prepare("UPDATE users SET TOTPOINTS = :score WHERE USERNAME = :username");
    
    $stmt->bindParam(':score', $score, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Score mis à jour avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé ou score inchangé']);
    }

} catch(PDOException $e) {
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}

$conn = null;
?>
