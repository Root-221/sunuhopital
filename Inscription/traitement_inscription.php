<?php
// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sunuhopital";

try {
    // Connexion à la base de données avec PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configuration des attributs PDO pour rapporter les erreurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la table "medecin" si elle n'existe pas
    // $sql_create_medecin_table = "CREATE TABLE IF NOT EXISTS medecin (
    //     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    //     prenom VARCHAR(30) NOT NULL,
    //     nom VARCHAR(30) NOT NULL,
    //     email VARCHAR(50) NOT NULL,
    //     telephone VARCHAR(15) NOT NULL,
    //     date_naissance DATE NOT NULL,
    //     sexe ENUM('homme', 'femme', 'autre') NOT NULL,
    //     specialite VARCHAR(50) NOT NULL,
    //     password_hash VARCHAR(255) NOT NULL
    // )";

    // $conn->exec($sql_create_medecin_table);

    // Création de la table "administrateur" si elle n'existe pas
    $sql_create_administrateur_table = "CREATE TABLE IF NOT EXISTS administrateur(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        prenom VARCHAR(30) NOT NULL,
        nom VARCHAR(30) NOT NULL,
        email VARCHAR(50) NOT NULL,
        telephone VARCHAR(15) NOT NULL,
        date_naissance DATE NOT NULL,
        sexe ENUM('homme', 'femme', 'autre') NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        UNIQUE (email)
    )";

    $conn->exec($sql_create_administrateur_table);

    // Création de la table "patient" si elle n'existe pas
    $sql_create_patient_table = "CREATE TABLE IF NOT EXISTS patient (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        prenom VARCHAR(30) NOT NULL,
        nom VARCHAR(30) NOT NULL,
        email VARCHAR(50) NOT NULL,
        telephone VARCHAR(15) NOT NULL,
        date_naissance DATE NOT NULL,
        sexe ENUM('homme', 'femme', 'autre') NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        UNIQUE (email)
    )";

    $conn->exec($sql_create_patient_table);

    // Vérification de l'existence de l'utilisateur dans la table medecin, patient ou administrateur
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les données du formulaire
        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $email = $_POST["email"];
        $telephone = $_POST["telephone"];
        $date_naissance = $_POST["date_naissance"];
        $sexe = $_POST["sexe"];
        $password = $_POST["password"];

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier si l'email existe déjà dans la table administrateur avec le domaine sunuhopital.sn
        $stmt = $conn->prepare("SELECT * FROM administrateur WHERE email=? AND email LIKE '%@sunuhopital.sn'");
        $stmt->execute([$email]);
        $existing_admin = $stmt->fetch();

        if ($existing_admin) {
            // Si l'utilisateur est un administrateur, mettre à jour le mot de passe
            $stmt = $conn->prepare("UPDATE administrateur SET password_hash=? WHERE email=?");
            $stmt->execute([$password_hash, $email]);
        } else {
            // Vérifier si l'email existe déjà dans la table medecin
            $stmt = $conn->prepare("SELECT * FROM medecin WHERE email=?");
            $stmt->execute([$email]);
            $existing_medecin = $stmt->fetch();

            if ($existing_medecin) {
                // Si l'utilisateur est un médecin, mettre à jour le mot de passe
                $stmt = $conn->prepare("UPDATE medecin SET password =? WHERE email=?");
                $stmt->execute([$password_hash, $email]);
            } else {
                // Si l'utilisateur n'est pas un médecin ou administrateur, l'ajouter dans la table patient
                $stmt = $conn->prepare("INSERT INTO patient (prenom, nom, email, telephone, date_naissance, sexe, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$prenom, $nom, $email, $telephone, $date_naissance, $sexe, $password_hash]);
            }
        }

        // Redirection après inscription
        header("Location: ../Connexion/connexion.html");
        exit(); // Assure que le script s'arrête après la redirection
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Fermeture de la connexion
$conn = null;
?>
