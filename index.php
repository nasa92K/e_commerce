<?php 
session_start();
$canOrder = isset($_SESSION['cart']) && !empty($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <title>Figurine.com | Vente de figurines en ligne</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
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

        .nav-item .nav-link {
            display: flex;
            align-items: center;
        }

        .navbar {
            background-color: #222;
        }

        .navbar-brand h2 {
            color: white;
        }

        .navbar-brand h2 span {
            color: red;
        }

        .checkout-btn {
            text-align: center;
            margin: 30px 0;
        }

        .checkout-btn a {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        .checkout-btn a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="jumper"><div></div><div></div><div></div></div>
</div>

<!-- Header -->
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2>Boutique de figurine <span>Website</span></h2>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active"><a class="nav-link" href="index.php">Accueil</a></li>
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
</header>

<!-- Banner -->
<div class="banner header-text">
    <div class="owl-banner owl-carousel">
        <div class="banner-item-01">
            <div class="text-content">
                <h4>Trouvez ce qui vous convient!</h4>
                <h2>Vente de figurines</h2>
            </div>
        </div>
        <div class="banner-item-02">
            <div class="text-content">
                <h4>Beaucoup de choix</h4>
                <h2>Différentes catégories de figurines</h2>
            </div>
        </div>
        <div class="banner-item-03">
            <div class="text-content">
                <h4>Vente rapide</h4>
                <h2>Quantités limitées</h2>
            </div>
        </div>
    </div>
</div>

<!-- Produits -->
<div class="latest-products">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-heading">
                    <h2>Figurines en promotion</h2>
                    <a href="product-pouraccueil.php">Voir plus <i class="fa fa-angle-right"></i></a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="product-item">
                    <a href="product-pouraccueil.php?id=luffy"><img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" alt="Figurine Luffy"></a>
                    <div class="down-content">
                        <a href="product-details.php?id=luffy"><h4>Figurine Luffy</h4></a>
                        <h6><small><del>€199.00 </del></small> €169.99</h6>
                        <p>Cette figurine est Luffy, un personnage du manga célèbre One Piece.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="product-item">
                    <a href="product-pouraccueil.php?id=naruto"><img src="imagefigurine/Naruto.jpg" alt="Figurine Naruto"></a>
                    <div class="down-content">
                        <a href="product-details.php?id=naruto"><h4>Figurine Naruto</h4></a>
                        <h6><small><del>€99.00</del></small>  €79.00</h6>
                        <p>Cette figurine provenant du manga Naruto est une figurine du personnage du nom de Naruto.</p>
                    </div>
                </div>
            </div>

            <?php if ($canOrder): ?>
            <div class="col-md-12 checkout-btn">
                <a href="checkout.php">Aller à la page Commande</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="inner-content">
                    <p>Contact © 2025 Company Name - Website by Anas Fekhar:
                        <a href="https://manga-story.fr/maintenance/">E-commerceFigurine.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/owl.js"></script>
</body>
</html>
