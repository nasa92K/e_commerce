<?php
session_start();

$figurines = [
    'luffy' => [
        'title' => 'Figurine Luffy',
        'image' => 'imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg',
        'price' => 169.99,
        'old_price' => 199.00,
        'description' => 'Cette figurine est Luffy, un personnage du manga célèbre One Piece.'
    ],
    'naruto' => [
        'title' => 'Figurine Naruto',
        'image' => 'imagefigurine/Naruto.jpg',
        'price' => 79.00,
        'old_price' => 99.00,
        'description' => 'Cette figurine provenant du manga Naruto est une figurine du personnage du nom de Naruto.'
    ]
];

$id = $_GET['id'] ?? '';
$figurine = $figurines[$id] ?? null;

if (!$figurine) {
    echo "Produit introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($figurine['title']) ?> | Figurine.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap et styles -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .username {
            color: white;
            font-weight: bold;
            margin-left: 10px;
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
        }

        .navbar {
            background-color: #222;
        }

        .navbar-brand h2 {
            color: white;
            margin: 0;
        }

        .navbar-brand h2 span {
            color: red;
        }

        .product-container {
            margin-top: 100px;
            margin-bottom: 50px;
        }

        .btn-commander {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-commander:hover {
            background-color: #218838;
        }

        .retour-link {
            display: inline-block;
            margin-top: 15px;
        }

        footer {
            margin-top: 50px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <h2>BOUTIQUE DE FIGURINE <span>WEBSITE</span></h2>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false">Plus</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="about-us.php">À propos</a>
                        <a class="dropdown-item" href="profile.php">Profil</a>
                        <a class="dropdown-item" href="testimonials.php">Avis</a>
                        <a class="dropdown-item" href="terms.php">Information</a>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="checkout.php">Commande</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION["user_id"])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="Deconnexion.php">
                            Déconnexion <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu -->
<div class="container product-container">
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="<?= htmlspecialchars($figurine['image']) ?>" alt="<?= htmlspecialchars($figurine['title']) ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($figurine['title']) ?></h2>
            <p><strong>Prix :</strong>
                <del>€<?= number_format($figurine['old_price'], 2) ?></del>
                <span class="text-success">€<?= number_format($figurine['price'], 2) ?></span>
            </p>
            <p><?= htmlspecialchars($figurine['description']) ?></p>

            <a href="checkout.php" class="btn-commander">Commander</a><br>
            <a href="index.php" class="retour-link">← Retour à l'accueil</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>CONTACT © 2025 COMPANY NAME - WEBSITE BY ANAS FEKHAR :
            <a href="https://manga-story.fr/maintenance/">E-COMMERCEFIGURINE.COM</a>
        </p>
    </div>
</footer>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
