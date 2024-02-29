<?php
// Vérifier si l'e-mail du médecin est passé en paramètre
if(isset($_GET['email'])) {
    // Récupérer l'e-mail du médecin depuis l'URL
    $medecin_email = $_GET['email'];

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

        // Requête SQL pour récupérer les informations du médecin avec l'e-mail spécifié
        $sql_medecin = "SELECT * FROM medecin WHERE email = :email";
        $stmt_medecin = $conn->prepare($sql_medecin);
        $stmt_medecin->bindParam(':email', $medecin_email);
        $stmt_medecin->execute();
        $medecin = $stmt_medecin->fetch(PDO::FETCH_ASSOC);

        // Vérifier si le médecin a été trouvé
        if($medecin) {
            // Récupérer l'e-mail du médecin
            $medecin_email = $medecin['email'];

            // Requête SQL pour récupérer les horaires disponibles du médecin depuis la table horaire
            $sql_horaires = "SELECT * FROM horaire WHERE email = :medecin_email";
            $stmt_horaires = $conn->prepare($sql_horaires);
            $stmt_horaires->bindParam(':medecin_email', $medecin_email);
            $stmt_horaires->execute();
            $horaires = $stmt_horaires->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Rediriger vers une page d'erreur si le médecin n'est pas trouvé
            header("Location: erreur.php");
            exit();
        }

    } catch(PDOException $e) {
        // En cas d'erreur de connexion à la base de données
        echo "Erreur de connexion à la base de données: " . $e->getMessage();
    }
} else {
    // Rediriger vers une page d'erreur si l'e-mail du médecin n'est pas spécifié
    header("Location: erreur.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Médecin - SunuHopital.sn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../utilisateur/utilisateur.css">
    <style>
        .schedule-table {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 10px;
        }
    </style>
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

<div class="container mt-4">
    <h2 class="text-center">Mon Médecin</h2>
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card mb-3">
                <img src="<?= $medecin['image'] ?>" class="card-img-top" alt="Image du Médecin">
                <div class="card-body">
                    <h3 class="card-title">Dr <?= $medecin['prenom'] . ' ' . $medecin['nom'] ?></h3>
                    <p class="card-text">Spécialité: <?= $medecin['specialite'] ?></p>
                    <p class="card-text"><?= $medecin['adresse'] ?></p>
                    <a href="Rdv_vous.php?email=<?= $medecin['email'] ?>" class="btn btn-primary">Prendre Rendez-Vous</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="schedule-table">
                <h3 class="text-center">Horaires Disponibles</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Heure Debut</th>
                            <th>Heure Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horaires as $horaire): ?>
                            <tr>
                                <td><?= $horaire['jour_semaine'] ?></td>
                                <td><?= $horaire['heure_debut'] ?></td>
                                <td><?= $horaire['heure_fin'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
