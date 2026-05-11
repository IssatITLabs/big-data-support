<?php
// Fichier : /api/login.php
// Gère la connexion et enregistre la session côté serveur pour protéger teacher.php.

// 1. Démarrer la session PHP (OBLIGATOIRE en premier)
session_start();

// 2. Configuration des en-têtes
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
http_response_code(200);

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? ''; 

// ⚠️ MODIFIEZ AVEC VOS VRAIS ACCÈS MYSQL ⚠️
$servername = "localhost";      
$db_user = "root"; // VOTRE NOM D'UTILISATEUR
$db_pass = ""; // VOTRE MOT DE PASSE
$dbname = "db_BD";          

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe manquant']);
    exit;
}

try {
    // A. CONNEXION À LA BASE DE DONNÉES MySQL via PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // B. REQUÊTE PRÉPARÉE : Récupère les données utilisateur, y compris le mot de passe (PW)
    $stmt = $conn->prepare("SELECT USERNAME, TOTPOINTS, PHOTO, PW FROM users WHERE USERNAME = :username"); 
    
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- Vérification de l'Authentification ---
    
    // Cas spécial pour l'administrateur
    $is_admin = ($username === 'admin@gmail.com');
    $is_admin_password_ok = $is_admin && ($password === 'admin');

    // Cas utilisateur normal (vérification du mot de passe en clair dans la DB)
    $is_normal_user_password_ok = $user && ($user['PW'] === $password);
    
    // Si l'une des vérifications est réussie
    if ($is_admin_password_ok || $is_normal_user_password_ok) {
        
        // C. SUCCÈS : ENREGISTREMENT DES SESSIONS PHP
        
        // 🚨 POINT CRITIQUE : Ces variables DOIVENT être définies.
        $_SESSION['authenticated_user'] = $username;
        $_SESSION['is_admin'] = $is_admin;
        
        // Sécurité : Retirer le mot de passe avant de l'envoyer au client (JS)
        if (isset($user['PW'])) {
            unset($user['PW']); 
        }
        
        // S'assurer que les données utilisateur existent si c'était l'admin qui n'est pas dans la table
        if (!$user) {
            $user = ['USERNAME' => $username, 'TOTPOINTS' => 0, 'PHOTO' => ''];
        }

        echo json_encode(['success' => true, 'user' => $user]);
        
    } else {
        // D. ÉCHEC : Identifiants incorrects
        echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
    }

} catch(PDOException $e) {
    // E. ERREUR DE CONNEXION/BASE DE DONNÉES
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Erreur serveur/BD : ' . $e->getMessage()]);
}

$conn = null;
?>
