<?php
// Informations de connexion à la base de données
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données
$dbname = "sunuhopital"; // Nom de la base de données

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configuration des options PDO pour afficher les erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupération des données du formulaire
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];

        // Requête SQL pour supprimer le médecin
        $sql = "DELETE FROM medecin WHERE nom = :nom AND prenom = :prenom AND email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        
        // Exécution de la requête
        $stmt->execute();

        echo "Le médecin a été supprimé avec succès.";

        // Redirection vers la même page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
} catch (PDOException $e) {
    // En cas d'erreur, affichage de l'erreur PDO
    echo "Erreur lors de la suppression du médecin : " . $e->getMessage();
}
?>
