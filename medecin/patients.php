<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=sunuhopital', 'root', '');
    // Définir le mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérifier si la colonne "statut" existe dans la table rendez_vous
$stmt = $pdo->query("DESCRIBE rendez_vous");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('statut', $columns)) {
    // La colonne "statut" n'existe pas, nous allons l'ajouter
    try {
        $pdo->exec("ALTER TABLE rendez_vous ADD COLUMN statut VARCHAR(255) NOT NULL DEFAULT 'attente'");
    } catch(PDOException $e) {
        echo "Erreur lors de la modification de la structure de la table rendez_vous: " . $e->getMessage();
    }
}

// Récupérer l'ID du médecin connecté depuis la session
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'medecin') {
    // Rediriger l'utilisateur vers la page de connexion si non connecté ou non un médecin
    header("Location: ../connexion/connexion.php");
    exit();
}
$medecin_id = $_SESSION['user_email']; // Utilisez l'ID du médecin pour filtrer les rendez-vous

// Récupérer les informations des rendez-vous des patients avec le médecin concerné depuis la base de données
$stmt = $pdo->prepare("SELECT r.id, r.nom, r.prenom, r.email, r.telephone, r.date_rdv, r.heure_rdv, r.statut
                      FROM rendez_vous AS r
                      WHERE r.medecin_id = :medecin_id
                      ORDER BY r.date_rdv, r.heure_rdv"); // Ajout de la clause ORDER BY
$stmt->execute(array(':medecin_id' => $medecin_id));
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour le statut du rendez-vous si le bouton "Soigné" est cliqué
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_status'])) {
        $appointment_id = $_POST['appointment_id'];
        try {
            $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = 'soigné' WHERE id = :appointment_id");
            $stmt->execute(array(':appointment_id' => $appointment_id));
        } catch(PDOException $e) {
            echo "Erreur lors de la mise à jour du statut du rendez-vous: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Patients - SunuHopital.sn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

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

<div class="container mt-5 mb-5">
    <h2 class="text-center">Liste des Patients</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Prénom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Téléphone</th>
                    <th scope="col">Date de Rendez-vous</th>
                    <th scope="col">Heure de Rendez-vous</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Action</th> <!-- Nouvelle colonne pour l'action -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient) : ?>
                <tr>
                    <td><?php echo $patient['nom']; ?></td>
                    <td><?php echo $patient['prenom']; ?></td>
                    <td><?php echo $patient['email']; ?></td>
                    <td><?php echo $patient['telephone']; ?></td>
                    <td><?php echo $patient['date_rdv']; ?></td>
                    <td><?php echo $patient['heure_rdv']; ?></td>
                    <td><?php echo $patient['statut']; ?></td>
                    <td>
                        <!-- Formulaire pour mettre à jour le statut -->
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="appointment_id" value="<?php echo $patient['id']; ?>">
                            <button type="submit" class="btn btn-success" name="update_status">Soigné</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
