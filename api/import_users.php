<?php
// Fichier : /api/import_users.php
// Rôle : Lire un fichier JSON téléversé et importer les données des utilisateurs dans db_BD.

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ------------------------------------------------
// ⚠️ MODIFIEZ AVEC VOS VRAIS ACCÈS MYSQL ⚠️
$servername = "localhost";        
$db_user = "root"; 
$db_pass = ""; 
$dbname = "db_BD";                
// ------------------------------------------------

// 1. Vérification du fichier JSON
if (empty($_FILES['json_file']['tmp_name'])) {
    echo json_encode(['success' => false, 'message' => 'Aucun fichier JSON reçu ou erreur de téléversement.']);
    exit;
}

$file_path = $_FILES['json_file']['tmp_name'];
$json_content = file_get_contents($file_path);
$users_data = json_decode($json_content, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($users_data)) {
    echo json_encode(['success' => false, 'message' => 'Fichier invalide ou JSON mal formé.']);
    exit;
}

$total_imported = 0;
$errors = [];

try {
    // 2. CONNEXION À LA BASE DE DONNÉES
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 3. REQUÊTE PRÉPARÉE : Utilisation de INSERT...ON DUPLICATE KEY UPDATE
    // Cela nécessite que la colonne USERNAME soit une clé UNIQUE dans votre table 'user'.
    // Si l'utilisateur existe, les champs sont mis à jour ; sinon, il est inséré.
    $stmt = $conn->prepare("
        INSERT INTO users (USERNAME, PW, TOTPOINTS, PHOTO) 
        VALUES (:username, :pw, :totpoints, :photo)
        ON DUPLICATE KEY UPDATE 
            PW = VALUES(PW),
            TOTPOINTS = VALUES(TOTPOINTS),
            PHOTO = VALUES(PHOTO)
    ");
    
    $conn->beginTransaction(); // Démarre une transaction pour plus de performance et de sécurité

    // 4. Boucle sur les données JSON
    foreach ($users_data as $user) {
        if (!isset($user['USERNAME'], $user['PW'])) {
             $errors[] = "Ligne ignorée: USERNAME ou PW manquant.";
             continue;
        }

        $username = $user['USERNAME'];
        $pw = $user['PW'];
        $totpoints = $user['TOTPOINTS'] ?? 0;
        $photo = $user['PHOTO'] ?? '';
        
        // Exécution de l'insertion/mise à jour
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':pw', $pw);
        $stmt->bindParam(':totpoints', $totpoints, PDO::PARAM_INT);
        $stmt->bindParam(':photo', $photo);
        
        $stmt->execute();
        $total_imported++;
    }
    
    $conn->commit(); // Confirme toutes les opérations
    
    echo json_encode([
        'success' => true, 
        'message' => "Importation réussie : {$total_imported} utilisateurs traités.",
        'errors' => $errors
    ]);

} catch(PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack(); // Annule en cas d'erreur
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur SQL lors de l\'importation : ' . $e->getMessage()]);
}

?>
