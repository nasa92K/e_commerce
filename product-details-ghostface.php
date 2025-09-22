<?php
// Démarrer la session pour gérer les données du panier
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>POP Ghost Face | Figurine.com</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php"><h2>Boutique de Figurine</h2></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                        aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="panier.php">Panier</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <div class="page-heading header-text" style="background-image: url('imagefigurine/POPghostface.jpg');">
        <div class="container">
            <h2>POP Ghost Face</h2>
        </div>
    </div>

    <div class="products">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="imagefigurine/POPghostface.jpg" alt="POP Ghost Face" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>POP Ghost Face</h2>
                    <p><strong class="text-primary">€14.99</strong> <del>€19.99</del></p>
                    <p>Figure emblématique du cinéma horreur Ghost Face du film Scream, bientôt disponible.</p>
                    <!-- Formulaire pour ajouter au panier -->
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="product_name" value="POP Ghost Face">
                        <input type="hidden" name="product_price" value="14.99">
                        <input type="hidden" name="product_image" value="imagefigurine/POPghostface.jpg">
                        <div class="form-group">
                            <label for="quantity">Quantité</label>
                            <input type="number" id="quantity" name="product_quantity" class="form-control" value="1" min="1">
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>© 2025 Figurine.com</p>
        </div>
    </footer>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>