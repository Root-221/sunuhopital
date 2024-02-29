<?php
// Configuration de la connexion à la base de données MySQL
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$database = "sunuhopital"; // Nom de votre base de données

try {
    // Connexion à la base de données MySQL
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Configuration de PDO pour afficher les erreurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer les données des médecins
    $sql = "SELECT * FROM medecin";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // En cas d'erreur de connexion à la base de données
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Espace Utilisateur - SunuHopital.sn</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../utilisateur/utilisateur.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="#">
      <img src="../Acceuil/images/logo.png" alt="Logo" width="70" height="55" class="d-inline-block align-top">
      SunuHopital
    </a>
    
    <!-- Bouton de bascule pour mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Liens de navigation -->
    <div class="collapse navbar-collapse flex-grow-1" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="#">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Rendez-vous</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Historique-RDV</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Paramètres</a>
        </li>
        <li class="nav-item">
            <button id="logout-btn" class="btn btn-sm logout-btn">Se Déconnecter</button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-3">
  <h2 class="text-center">Nos Médecins</h2>
  <div class="row">
    <?php
    // Affichage des données des médecins dans des conteneurs HTML
    foreach ($medecins as $medecin) {
        echo '<div class="col-md-4 mt-3">';
        echo '<div class="medecin-container">';
        echo '<img src="' . $medecin['image'] . '" alt="Image Médecin">';
        echo '<h3>Dr ' . $medecin['prenom'] . ' ' . $medecin['nom'] . '</h3>';
        echo '<p>Spécialité: ' . $medecin['specialite'] . '</p>';
        // Ajout des étoiles de notation
        echo '<div>';
        for ($i = 0; $i < 5; $i++) {
            echo '<i class="bi bi-star-fill star"></i>';
        }
        echo '</div>';
        // Bouton "Rendez-Vous"
        echo '<a href="rendez_vous.php?email=' . $medecin['email'] . '"><button class="btn btn-primary mt-3">Rendez-Vous</button><a>';
        echo '</div>';
        echo '</div>';
    }
    ?>
  </div>
</div>

<footer class="bg-dark text-white text-center py-5 ">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Conditions d'utilisation</h5>
                <p>Insérez ici les conditions d'utilisation de votre site.</p>
            </div>
            <div class="col-md-6">
                <h5>Réseaux Sociaux</h5>
                <ul class="list-unstyled d-flex justify-content-center">
                    <li class="me-4"><a href="#"><i class="bi bi-facebook"></i></a></li>
                    <li class="me-4"><a href="#"><i class="bi bi-twitter"></i></a></li>
                    <li class="me-4"><a href="#"><i class="bi bi-instagram"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h5>Adresse</h5>
                <p>123, Rue de l'Hôpital<br>Dakar, Sénégal</p>
            </div>
            <div class="col-md-6">
                <h5>Contact</h5>
                <p>Email: info@sunnuhospital.com<br>Téléphone: +1234567890</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
  // Script pour déconnexion
  document.getElementById("logout-btn").addEventListener("click", function() {
    var confirmation = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
    if (confirmation) {
      // Redirection vers la page de connexion
      window.location.href = "../Connexion/connexion.html";
    }
  });
</script>

</body>
</html>
