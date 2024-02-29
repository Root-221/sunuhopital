<?php
// Définir la durée de vie de la session à 1 an (365 jours)
$expiration = 365 * 24 * 60 * 60; // 1 an en secondes

// Définir les paramètres du cookie de session
session_set_cookie_params($expiration);

// Démarrez la session
session_start();

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=sunuhopital', 'root', '');
    // Définir le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparer et exécuter la requête pour récupérer le médecin par email
    $stmt = $pdo->prepare("SELECT * FROM medecin WHERE email = :email");
    $stmt->execute(array(':email' => $email));
    $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le médecin existe et si le mot de passe est correct
    if ($medecin && password_verify($password, $medecin['password'])) {
        // Stocker les informations du médecin dans la session
        $_SESSION['user_id'] = $medecin['id'];
        $_SESSION['user_role'] = 'medecin';
        $_SESSION['user_email'] = $medecin['email'];

        // Rediriger le médecin vers la page médecin
        header("Location: ../medecin/medecin.php");
        exit();
    } else {
        // Si le médecin n'est pas trouvé, chercher dans la table patient
        $stmt = $pdo->prepare("SELECT * FROM patient WHERE email = :email");
        $stmt->execute(array(':email' => $email));
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si le patient existe et si le mot de passe est correct
        if ($patient && password_verify($password, $patient['password_hash'])) {
            // Stocker les informations du patient dans la session
            $_SESSION['user_id'] = $patient['id'];
            $_SESSION['user_role'] = 'patient';

            // Rediriger le patient vers la page utilisateur
            header("Location: ../utilisateur/utilisateur.php");
            exit();
        } else {
            // Vérifier si l'utilisateur est un administrateur
            $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE email = :email");
            $stmt->execute(array(':email' => $email));
            $administrateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($administrateur && password_verify($password, $administrateur['password_hash'])) {
                // Rediriger l'administrateur vers la page administrateur
                header("Location: ../administrateur/administrateur.html");
                exit();
            } else {
                // Redirection en cas d'échec d'authentification
                echo "<script>alert('Identifiants incorrects. Veuillez réessayer.');</script>";
                header("Location: ../Connexion/connexion.html");
                exit();
            }
        }
    }
} else {
    // Si la méthode de requête n'est pas POST, affichez un message d'erreur
    echo "Méthode de requête non valide.";
}
?>
