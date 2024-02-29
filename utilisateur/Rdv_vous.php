<?php
// Configuration de la connexion à la base de données MySQL
$servername = "localhost"; // Nom du serveur
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$database = "sunuhopital"; // Nom de votre base de données

// Création de la table rendez_vous si elle n'existe pas
try {
    // Connexion à la base de données MySQL
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Configuration de PDO pour afficher les erreurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour créer la table rendez_vous si elle n'existe pas
    $sql_create_table = "CREATE TABLE IF NOT EXISTS rendez_vous (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        medecin_id VARCHAR(100) NOT NULL,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        date_rdv DATE,
        heure_rdv TIME,
        -- message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (medecin_id) REFERENCES medecin(id)
    )";

    // Exécution de la requête pour créer la table
    $conn->exec($sql_create_table);
} catch(PDOException $e) {
    // En cas d'erreur lors de la création de la table
    echo "Erreur lors de la création de la table rendez_vous: " . $e->getMessage();
}

// Déclaration d'une variable pour stocker le message de confirmation
$message_confirmation = "";

// Vérification de la soumission du formulaire de rendez-vous
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Traitement des données du formulaire
    $medecin_id = $_POST['medecin_id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $date_rdv = $_POST['date_rdv'];
    $heure_rdv = $_POST['heure_rdv'];
    // $message = $_POST['message'];

    try {
        // Requête SQL pour insérer les données du rendez-vous dans la table
        $sql_insert_rdv = "INSERT INTO rendez_vous (medecin_id, nom, prenom, email, telephone, date_rdv, heure_rdv) 
                           VALUES ('$medecin_id', '$nom', '$prenom', '$email', '$telephone', '$date_rdv', '$heure_rdv')";
        
        // Exécution de la requête d'insertion
        $conn->exec($sql_insert_rdv);
        // Définir le message de confirmation
        // $message_confirmation = "Rendez-vous pris avec succès!";
        header("Location: confirmation.php");
    } catch(PDOException $e) {
        // En cas d'erreur lors de l'insertion des données du rendez-vous
        echo "Erreur lors de la prise de rendez-vous: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prise de Rendez-vous - SunuHopital.sn</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
          <a class="nav-link" href="utilisateur.php">Accueil</a>
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

<div class="container mt-3 mb-5">
  <h2 class="text-center">Prise de Rendez-vous</h2>
  <div class="row">
    <div class="col-md-6 offset-md-3">
    <form method="post" action="Rdv_vous.php">
    <input type="hidden" name="medecin_id" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">
        <div class="mb-3">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
          <label for="prenom" class="form-label">Prénom</label>
          <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="telephone" class="form-label">Téléphone</label>
          <input type="tel" class="form-control" id="telephone" name="telephone" required>
        </div>
        <div class="mb-3">
          <label for="date_rdv" class="form-label">Date de rendez-vous</label>
          <input type="date" class="form-control" id="date_rdv" name="date_rdv" required>
        </div>
        <div class="mb-3">
          <label for="heure_rdv" class="form-label">Heure de rendez-vous</label>
          <input type="time" class="form-control" id="heure_rdv" name="heure_rdv" required>
        </div>
        <button type="submit" class="btn btn-primary">Prendre Rendez-vous</button>
      </form>
    </div>
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
