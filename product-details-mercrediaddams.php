<?php
// Démarrer la session pour gérer les données du panier
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mercredi Addams | Figurine.com</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php"><h2>Boutique de Figurine</h2></a>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="panier.php">Panier</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <div class="page-heading header-text" style="background-image: url('imagefigurine/POPwednesday.jpg');">
        <div class="container">
            <h2>Mercredi Addams</h2>
        </div>
    </div>

    <div class="products">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="imagefigurine/POPwednesday.jpg" alt="Mercredi Addams" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>Mercredi Addams</h2>
                    <p><strong class="text-primary">€14.99</strong> <del>€19.99</del></p>
                    <p>Plongez dans l'univers sombre de la famille Addams avec cette superbe figurine de Mercredi Addams.</p>
                    <!-- Formulaire pour ajouter au panier -->
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="product_name" value="Mercredi Addams">
                        <input type="hidden" name="product_price" value="14.99">
                        <input type="hidden" name="product_image" value="imagefigurine/POPwednesday.jpg">
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
</body>

</html>