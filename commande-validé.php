<?php
session_start();

// On vide le panier après la validation
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande Validée</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .confirmation-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 150px);
            padding: 30px;
        }

        .confirmation-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .confirmation-box h2 {
            color: green;
            font-weight: bold;
        }

        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2 class="mb-0">BOUTIQUE DE FIGURINE <span style="color: red;">WEBSITE</span></h2>
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
                    <li class="nav-item"><a class="nav-link" href="panier.php">Panier</a></li>
                    <li class="nav-item"><a class="nav-link" href="checkout.php">Commande</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_SESSION['email']) && $_SESSION['email'] === 'anasfigurine@gmail.com'): ?>
                    <li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="admin_dashboard.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- CONTENU PRINCIPAL -->
<div class="container confirmation-container">
    <div class="confirmation-box">
        <h2>🎉 Votre commande a bien été passée !</h2>
        <p>Merci pour votre achat. Un email de confirmation vous sera envoyé prochainement.</p>
        <a href="index.php" class="btn btn-primary mt-4">Retour à l'accueil</a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>© 2025 Figurine.com</p>
    </div>
</footer>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
