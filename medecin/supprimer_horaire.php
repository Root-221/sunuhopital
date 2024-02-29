<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=sunuhopital', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST["id"];

        $stmt_delete = $pdo->prepare("DELETE FROM historique_horaire WHERE id = :id");
        $stmt_delete->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt_delete->execute();

        // Redirection vers la page historique après la suppression de l'horaire
        header("Location: historique.php");
        exit();
    } catch(PDOException $e) {
        echo "Erreur lors de la suppression de l'horaire: " . $e->getMessage();
    }
} else {
    echo "Méthode de requête incorrecte ou aucun identifiant d'horaire fourni.";
}
?>
