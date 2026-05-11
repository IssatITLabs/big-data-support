<?php
// Fichier : teacher.php
// Rôle : Afficher tous les utilisateurs de la BD pour l'enseignant et se rafraîchir automatiquement.

// 1. Démarrer la session PHP (DOIT ÊTRE LA PREMIÈRE INSTRUCTION)
session_start();

// 2. Vérification de l'authentification et du rôle Administrateur
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Si l'utilisateur n'est PAS connecté OU s'il n'est PAS administrateur
if (!isset($_SESSION['authenticated_user']) || !$is_admin) {
    // Redirection immédiate vers la page de connexion
    header('Location: index.html');
    exit(); // Arrêter l'exécution du script
}

// ----------------------------------------------------------------------------------------------------------------------
// Les lignes ci-dessous ne seront exécutées que si l'utilisateur est 'admin@gmail.com'
// ----------------------------------------------------------------------------------------------------------------------

// ⚠️ MODIFIEZ AVEC VOS VRAIS ACCÈS MYSQL ⚠️
$servername = "localhost";      
$db_user = "root"; // 🚨 VOTRE nom d'utilisateur MySQL
$db_pass = ""; // 🚨 VOTRE mot de passe MySQL
$dbname = "db_BD";          

// Initialisation de la variable d'erreur
$error_message = null; 
$users = [];

try {
    // 1. CONNEXION À LA BASE DE DONNÉES
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. REQUÊTE : Sélectionner toutes les colonnes requises
    $stmt = $conn->prepare("SELECT ID, PHOTO, USERNAME, TOTPOINTS FROM users ORDER BY TOTPOINTS DESC, ID ASC");
    // NOTE : Si la table est 'users', veuillez vérifier que la requête ci-dessous correspond à la structure :
    // $stmt = $conn->prepare("SELECT ID, PHOTO, USERNAME, TOTPOINTS FROM users ORDER BY TOTPOINTS DESC, ID ASC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Si la connexion échoue, la variable d'erreur est définie, et $users est vide.
    $error_message = "Erreur de connexion à la base de données ou requête : " . $e->getMessage();
}

// FIN DU BLOC PHP DE TRAITEMENT
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Enseignant - Liste des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ... (Vos styles CSS) ... */
        body { font-family: Arial, sans-serif; background: #f4f7fb; color: #0b2738; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #0b7285; border-bottom: 2px solid #0b7285; padding-bottom: 10px; margin-bottom: 20px; }
        .error { color: #dc3545; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0b7285; color: white; }
        tr:nth-child(even) { background-color: #f8f8f8; }
        tr:hover { background-color: #f1f1f1; }
        
        .user-photo { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        
        .back-link { display: inline-block; margin-bottom: 20px; color: #0b7285; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }

        .import-section {
            background: #e6f7ff;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            border: 1px dashed #0b7285;
            display: flex; 
            align-items: center;
            justify-content: space-between;
        }
        .import-section input[type="file"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .import-section button {
            background-color: #0b7285;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .import-section button:hover {
            background-color: #085f6e;
        }
        #import-status {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Retour au Cours</a>
        <h1><i class="fas fa-chalkboard-teacher"></i> Espace Enseignant - Données Utilisateurs</h1>

        <div class="import-section">
            <p>Charger des utilisateurs depuis un fichier JSON (Mise à jour par USERNAME) :</p>
            <form id="import-form">
                <input type="file" id="json-input" name="json_file" accept=".json" required>
                <button type="submit" id="import-button"><i class="fas fa-upload"></i> Charger les Utilisateurs</button>
            </form>
        </div>
        
        <div id="import-status"></div>
        
        <?php if ($error_message !== null): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (empty($users)): ?>
            <p>Aucun utilisateur trouvé dans la base de données. Importez un fichier JSON.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>PHOTO</th>
                        <th>ID</th>
                        <th>USERNAME</th>
                        <th>TOTAL POINTS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php if (!empty($user['PHOTO'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['PHOTO']); ?>" alt="Photo de <?php echo htmlspecialchars($user['USERNAME']); ?>" class="user-photo">
                                <?php else: ?>
                                    <i class="fas fa-user-circle" style="font-size: 40px; color: #ccc;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['ID']); ?></td>
                            <td><?php echo htmlspecialchars($user['USERNAME']); ?></td>
                            <td><?php echo htmlspecialchars($user['TOTPOINTS']); ?></td>
                            <td><?php echo htmlspecialchars($user['TOTPOINTS']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script>
        // ... (Votre script JavaScript existant) ...
        const importApiEndpoint = './api/import_users.php';
        const importButton = document.getElementById('import-button'); 
        
        // ----------------------------------------------------
        // LOGIQUE D'IMPORTATION (AJAX)
        // ----------------------------------------------------
        document.getElementById('import-form').addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const form = e.target;
            const statusDiv = document.getElementById('import-status');
            
            statusDiv.style.display = 'block';
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement en cours...';
            importButton.disabled = true;

            const formData = new FormData(form);
            
            try {
                const response = await fetch(importApiEndpoint, {
                    method: 'POST',
                    body: formData 
                });

                const result = await response.json();
                
                if (result.success) {
                    statusDiv.style.backgroundColor = '#d4edda';
                    statusDiv.style.color = '#155724';
                    statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${result.message}<br>Rechargement de la page dans 3 secondes pour voir les données mises à jour.`;
                    
                    // Après succès, déclencher le rechargement pour voir les nouvelles données
                    setTimeout(() => window.location.reload(), 3000); 

                } else {
                    statusDiv.style.backgroundColor = '#f8d7da';
                    statusDiv.style.color = '#721c24';
                    statusDiv.innerHTML = `<i class="fas fa-times-circle"></i> Erreur d'importation: ${result.message}`;
                    importButton.disabled = false;
                }

            } catch (error) {
                statusDiv.style.backgroundColor = '#f8d7da';
                statusDiv.style.color = '#721c24';
                statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Erreur réseau ou serveur: ${error.message}`;
                importButton.disabled = false;
            }
        });
        
        // ----------------------------------------------------
        // LOGIQUE DE POLLING (RECHARGEMENT AUTOMATIQUE)
        // ----------------------------------------------------
        const refreshInterval = 10000; // Changé à 10 secondes pour être moins intrusif
        
        setInterval(function() {
            // Recharger uniquement si aucun processus d'importation n'est en cours (bouton non désactivé)
            if (!importButton.disabled) {
                 window.location.reload();
            }
        }, refreshInterval);
        
        console.log("La page Enseignant se rechargera automatiquement toutes les 10 secondes.");
    </script>
</body>
</html>
