<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=sunuhopital', 'root', '');
    // Définir le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupérer les enregistrements de l'historique des horaires depuis la base de données
$stmt_select_history = $pdo->prepare("SELECT * FROM historique_horaire");
$stmt_select_history->execute();
$historiques = $stmt_select_history->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des horaires - SunuHopital.sn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/accueil.css">
    <link rel="stylesheet" href="medecin.css">
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Liens de navigation -->
        <div class="collapse navbar-collapse flex-grow-1" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../medecin/medecin.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Patients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Rendez-vous</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Prescriptions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Dossiers médicaux</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Messagerie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Tâches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Formation</a>
                </li>
                <li class="nav-item">
                    <button class="nav-link bg-danger text-white fw-bold" id="btnLogout"
                            style="border-radius: 10px;">Déconnexion
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Historique des Horaires</h1>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Jour de la semaine</th>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Date d'ajout</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historiques as $historique): ?>
                        <tr>
                            <td><?php echo $historique['id']; ?></td>
                            <td><?php echo $historique['email']; ?></td>
                            <td><?php echo $historique['jour_semaine']; ?></td>
                            <td><?php echo $historique['heure_debut']; ?></td>
                            <td><?php echo $historique['heure_fin']; ?></td>
                            <td><?php echo $historique['date_ajout']; ?></td>
                            <td>
                                <form method="post" action="supprimer_horaire.php">
                                    <input type="hidden" name="id" value="<?php echo $historique['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- footer -->
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
    document.getElementById('btnLogout').addEventListener('click', function () {
        if (confirm('Voulez-vous vraiment quitter l\'application ?')) {
            // Rediriger vers la page de déconnexion
            window.location.href = '../Connexion/connexion.html';
        }
    });
</script>

</body>
</html>
