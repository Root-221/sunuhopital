<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=sunuhopital', 'root', '');
    // Définir le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Création de la table historique_horaire si elle n'existe pas
$create_table_query = "
    CREATE TABLE IF NOT EXISTS historique_horaire (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        jour_semaine VARCHAR(255) NOT NULL,
        heure_debut TIME NOT NULL,
        heure_fin TIME NOT NULL,
        date_ajout DATETIME NOT NULL
    )
";

try {
    // Exécution de la requête de création de table
    $pdo->exec($create_table_query);
    // echo "Table historique_horaire créée avec succès.";
} catch(PDOException $e) {
    echo "Erreur lors de la création de la table historique_horaire: " . $e->getMessage();
}

// Récupérer les informations du médecin connecté depuis la session
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'medecin') {
    // Rediriger l'utilisateur vers la page de connexion si non connecté ou non un médecin
    header("Location: ../connexion/connexion.php");
    exit();
}
$id_medecin = $_SESSION['user_id'];

// Récupérer les informations du médecin connecté depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM medecin WHERE id = :id");
$stmt->execute(array(':id' => $id_medecin));
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le médecin existe
if (!$medecin) {
    echo "Médecin non trouvé.";
    exit();
}

// Extraire les informations du médecin
$nom = $medecin['nom'];
$prenom = $medecin['prenom'];

// Traitement du formulaire pour enregistrer l'horaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $jour_semaine = $_POST['jour_semaine'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $date_ajout = $_POST['date_ajout']; // Nouvelle ligne pour récupérer la date d'ajout

    // Préparation de la requête d'insertion
    $stmt_insert = $pdo->prepare("INSERT INTO horaire (email, jour_semaine, heure_debut, heure_fin, date_ajout) VALUES (:email, :jour_semaine, :heure_debut, :heure_fin, :date_ajout)");
    $stmt_insert->bindParam(':email', $medecin['email']);
    $stmt_insert->bindParam(':jour_semaine', $jour_semaine);
    $stmt_insert->bindParam(':heure_debut', $heure_debut);
    $stmt_insert->bindParam(':heure_fin', $heure_fin);
    $stmt_insert->bindParam(':date_ajout', $date_ajout); // Liaison du paramètre date_ajout

    // Exécution de la requête d'insertion
    if ($stmt_insert->execute()) {
        // Insertion réussie, insérer également dans la table historique_horaire
        $stmt_insert_history = $pdo->prepare("INSERT INTO historique_horaire (email, jour_semaine, heure_debut, heure_fin, date_ajout) VALUES (:email, :jour_semaine, :heure_debut, :heure_fin, :date_ajout)");
        $stmt_insert_history->bindParam(':email', $medecin['email']);
        $stmt_insert_history->bindParam(':jour_semaine', $jour_semaine);
        $stmt_insert_history->bindParam(':heure_debut', $heure_debut);
        $stmt_insert_history->bindParam(':heure_fin', $heure_fin);
        $stmt_insert_history->bindParam(':date_ajout', $date_ajout);
        $stmt_insert_history->execute();

        // Rediriger vers la même page pour éviter la répétition de l'insertion après un rechargement de la page
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "Erreur lors de l'enregistrement de l'horaire.";
    }
}

// Supprimer automatiquement les horaires passés de la base de données
$current_day = date('l'); // Jour actuel
$current_time = date('H:i:s'); // Heure actuelle
$stmt_delete = $pdo->prepare("DELETE FROM horaire WHERE email = :email AND jour_semaine = :jour_semaine AND heure_fin < :heure_fin");
$stmt_delete->execute(array(':email' => $medecin['email'], ':jour_semaine' => $current_day, ':heure_fin' => $current_time));
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - SunuHopital.sn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/accueil.css">
    <link rel="stylesheet" href="medecin.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        
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
                    <a class="nav-link" href="patients.php">Patients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Rendez-vous</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../medecin/historique.php">historiques</a>
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

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center mt-5">
            <h1 class="display-4">Bienvenue Dr <?php echo $prenom . " " . $nom; ?> sur <span style="color: blue; font-weight: bold;">SunuHopital</span></h1>
            <!-- Ajoutez ici d'autres éléments de la page d'accueil -->
        </div>
    </div>
</div>


<!-- ////////////////////////////////////////// -->
<!-- Section des fonctionnalités -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mb-4 shadow">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Gérer les rendez-vous</h3>
                    <p class="card-text">Planifiez et gérez les rendez-vous avec vos patients.</p>
                    <a href="#" class="btn btn-primary btn-block">Rendez-vous</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 shadow">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Prescriptions médicales</h3>
                    <p class="card-text">Créez et gérez les prescriptions médicales pour vos patients.</p>
                    <a href="#" class="btn btn-primary btn-block">Ordonnances</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4 shadow">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Dossiers médicaux</h3>
                    <p class="card-text">Consultez et mettez à jour les dossiers médicaux de vos patients.</p>
                    <a href="#" class="btn btn-primary btn-block">Accéder</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 shadow">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Messagerie</h3>
                    <p class="card-text">Échangez des messages sécurisés avec vos patients et collègues.</p>
                    <a href="#" class="btn btn-primary btn-block">Communiquer</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Section des statistiques et des analyses -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6 shadow">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiques</h5>
                    <p class="card-text">Visualisez les statistiques sur les patients, les rendez-vous, etc.</p>
                    <a href="#" class="btn btn-primary">Voir les statistiques</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 shadow">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Analyses</h5>
                    <p class="card-text">Effectuez des analyses avancées pour optimiser les soins aux patients.</p>
                    <a href="#" class="btn btn-primary">Voir les analyses</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ///////////////////////////////////////////// -->
<!-- Formulaire pour enregistrer l'horaire -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Enregistrer l'horaire de disponibilité</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="jour_semaine" class="form-label">Jour de la semaine</label>
                    <select class="form-select" id="jour_semaine" name="jour_semaine">
                        <option value="lundi">Lundi</option>
                        <option value="mardi">Mardi</option>
                        <option value="mercredi">Mercredi</option>
                        <option value="jeudi">Jeudi</option>
                        <option value="vendredi">Vendredi</option>
                        <option value="samedi">Samedi</option>
                        <option value="dimanche">Dimanche</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="heure_debut" class="form-label">Heure de début</label>
                    <input type="time" class="form-control" id="heure_debut" name="heure_debut" required>
                </div>
                <div class="mb-3">
                    <label for="heure_fin" class="form-label">Heure de fin</label>
                    <input type="time" class="form-control" id="heure_fin" name="heure_fin" required>
                </div>
                <!-- Champ caché pour la date d'ajout -->
                <input type="hidden" name="date_ajout" value="<?php echo date('Y-m-d H:i:s'); ?>">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
        <div class="col-md-6">
            <h2 class="text-center mb-4">Horaires enregistrés</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Jour</th>
                            <th scope="col">Heure de début</th>
                            <th scope="col">Heure de fin</th>
                            <th scope="col">Date d'ajout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Récupérer les horaires enregistrés du médecin
                        $stmt_select = $pdo->prepare("SELECT * FROM horaire WHERE email = :email");
                        $stmt_select->execute(array(':email' => $medecin['email']));
                        $horaires = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

                        // Afficher les horaires
                        foreach ($horaires as $horaire) {
                            echo "<tr>";
                            echo "<td>" . $horaire['jour_semaine'] . "</td>";
                            echo "<td>" . $horaire['heure_debut'] . "</td>";
                            echo "<td>" . $horaire['heure_fin'] . "</td>";
                            echo "<td>" . $horaire['date_ajout'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ////////////////////////////////// -->

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
                    <li class="me-4"><a href="#"><i class="fab fa-facebook"></i></a></li>
                    <li class="me-4"><a href="#"><i class="fab fa-twitter"></i></a></li>
                    <li class="me-4"><a href="#"><i class="fab fa-instagram"></i></a></li>
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
