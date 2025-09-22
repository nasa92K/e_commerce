<?php
// Démarrer la session pour gérer les données du panier
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>POP Haerin | Figurine.com</title>
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
    <div class="page-heading header-text" style="background-image: url('imagefigurine/POPhaerin.jpg');">
        <div class="container">
            <h2>POP Haerin</h2>
        </div>
    </div>

    <div class="products">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="imagefigurine/POPhaerin.jpg" alt="POP Haerin" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>POP Haerin</h2>
                    <p><strong class="text-primary">€19.99</strong> <del>€24.99</del></p>
                    <p>Kang Haerin est une chanteuse K-Pop du célèbre groupe NewJeans.</p>
                    <!-- Formulaire pour ajouter au panier -->
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="product_name" value="POP Haerin">
                        <input type="hidden" name="product_price" value="19.99">
                        <input type="hidden" name="product_image" value="imagefigurine/POPhaerin.jpg">
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