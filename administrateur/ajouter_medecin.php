<?php
// Informations de connexion à la base de données
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données
$dbname = "sunuhopital"; // Nom de la base de données

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    // Configuration des options PDO pour afficher les erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la base de données si elle n'existe pas
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
    $pdo->exec($sql_create_db);

    // Sélection de la base de données sunuhopital
    $pdo->exec("USE $dbname");

    // Création de la table medecin
    $sql_create_table = "CREATE TABLE IF NOT EXISTS medecin (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        email VARCHAR(50) NOT NULL UNIQUE,
        specialite VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL
    )";
    $pdo->exec($sql_create_table);

    // Vérification si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification de l'e-mail
        if (strpos($_POST['email'], '@sunuhopital.sn') === false) {
            echo "L'e-mail doit contenir '@sunuhopital.sn'";
            exit();
        }

        // Vérification si l'e-mail existe déjà
        $check_email = $pdo->prepare("SELECT COUNT(*) FROM medecin WHERE email = :email");
        $check_email->bindParam(':email', $_POST['email']);
        $check_email->execute();
        $count = $check_email->fetchColumn();
        if ($count > 0) {
            echo "Cet e-mail existe déjà dans la base de données.";
            exit();
        }

        // Préparation de la requête SQL pour insérer les données dans la table medecin
        $sql = "INSERT INTO medecin (nom, prenom, telephone, email, specialite, adresse, password, image) 
                VALUES (:nom, :prenom, :telephone, :email, :specialite, :adresse, :password, :image)";
        $stmt = $pdo->prepare($sql);

        // Liaison des paramètres de la requête avec les valeurs du formulaire
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':prenom', $_POST['prenom']);
        $stmt->bindParam(':telephone', $_POST['telephone']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':specialite', $_POST['specialite']);
        $stmt->bindParam(':adresse', $_POST['adresse']);
        $stmt->bindParam(':password', $_POST['password']);
        $stmt->bindParam(':image', $_POST['image']); // Modifier si nécessaire selon votre structure de stockage d'images

        // Exécution de la requête pour insérer les données
        $stmt->execute();

        // Redirection vers la même page après l'insertion des données
        header("Location: ajouter_medecin.html");
        exit(); // Assure que le script s'arrête ici pour éviter toute exécution supplémentaire
    }
} catch (PDOException $e) {
    // En cas d'erreur, affichage de l'erreur PDO
    echo "Erreur lors de la connexion à la base de données : " . $e->getMessage();
}
?>
