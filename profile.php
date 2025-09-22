<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'figurine_db';
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$profile_pic = $_SESSION["profile_pic"] ?? 'uploads/default.jpg';
$username = $_SESSION["username"] ?? 'Utilisateur';
$email = $_SESSION["email"] ?? 'Non renseigné';

// Liste des images de profil prédéfinies
$available_pfps = [
    'pfp/321351.jpg',
    'pfp/OIP.jpg',
    'pfp/Top-Free-Ghostface-PFP-1068x1186.jpg',
    'pfp/tumblr_pjdcndQPI01uhe1yu_1280.jpg'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Boutique de Figurines</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .profile-container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            border: none;
            background-color: #007bff;
        }

        .info-label {
            font-weight: 600;
            color: #343a40;
        }

        footer {
            margin-top: 50px;
            padding: 20px;
            background: #222;
            color: #fff;
            text-align: center;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-brand h2 {
            color: white;
            font-weight: bold;
        }

        .username-tag {
            background-color: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }

        .pfp-option {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin: 5px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .pfp-option.selected {
            border: 2px solid #007bff;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2>Boutique de Figurines</h2>
            </a>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="Deconnexion.php">
                            Déconnexion <span class="username-tag"><?= htmlspecialchars($username) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container profile-container">
    <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
        <div class="alert alert-success" role="alert">
            Vos informations ont été mises à jour avec succès !
        </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card p-4">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?= htmlspecialchars($profile_pic) ?>" class="profile-img mb-3" alt="Photo de profil">
                        <h4><?= htmlspecialchars($username) ?></h4>
                        <form action="update_profile.php" method="POST">
                            <label><i class="fas fa-image"></i> Choisissez une image :</label>
                            <div class="d-flex flex-wrap justify-content-center">
                                <?php foreach ($available_pfps as $img): ?>
                                    <label>
                                        <input type="radio" name="profile_pic" value="<?= $img ?>" style="display: none;">
                                        <img src="<?= $img ?>" class="pfp-option">
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
                        </form>
                    </div>

                    <div class="col-md-8">
                        <h5><i class="fas fa-user"></i> Informations personnelles</h5>
                        <hr>
                        <p><span class="info-label">Nom d'utilisateur:</span> <?= htmlspecialchars($username) ?></p>
                        <p><span class="info-label">Email:</span> <?= htmlspecialchars($email) ?></p>
                        <form action="update_info.php" method="POST">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur</label>
                                <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($username) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Adresse Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Mettre à jour les informations</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2025 Boutique de Figurines. Tous droits réservés.</p>
</footer>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    const inputs = document.querySelectorAll("input[name='profile_pic']");
    const images = document.querySelectorAll(".pfp-option");

    inputs.forEach((input, i) => {
        input.addEventListener('change', () => {
            images.forEach(img => img.classList.remove('selected'));
            images[i].classList.add('selected');
        });
    });
</script>
</body>
</html>