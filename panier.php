<?php
session_start();

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base_de_donnees = "figurine_db";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour supprimer un produit du panier par ID
function removeFromCart($product_id) {
    if (isset($_SESSION['panier'][$product_id])) {
        unset($_SESSION['panier'][$product_id]);
    }
}

// Fonction pour modifier la quantité d'un produit
function updateQuantity($product_id, $new_quantity) {
    if (isset($_SESSION['panier'][$product_id]) && $new_quantity > 0) {
        $_SESSION['panier'][$product_id]['quantite'] = (int)$new_quantity;
    } elseif ($new_quantity <= 0) {
        removeFromCart($product_id);
    }
}

// Gestion des actions
if (isset($_GET['action'], $_GET['id'])) {
    $product_id = (int)$_GET['id'];
    
    if ($_GET['action'] === 'remove') {
        removeFromCart($product_id);
        header('Location: panier.php');
        exit;
    }
}

// Gestion de la mise à jour des quantités via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        updateQuantity((int)$product_id, (int)$quantity);
    }
    header('Location: panier.php');
    exit;
}

// Calcul du total du panier
function getCartTotal() {
    $total = 0;
    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $item) {
            $total += $item['prix'] * $item['quantite'];
        }
    }
    return $total;
}

// Compter le nombre total d'articles
function getCartItemCount() {
    $count = 0;
    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $item) {
            $count += $item['quantite'];
        }
    }
    return $count;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Panier | E-commerce Figurine</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
</head>

<body>
    <!-- Header -->
    <header class="">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php"><h2>Boutique de Figurine <em>Website</em></h2></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Accueil</a>
                        </li> 
                        <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Plus</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="about-us.php">A propos</a>
                                <a class="dropdown-item" href="blog.php">Blog</a>
                                <a class="dropdown-item" href="testimonials.php">Utilisateurs</a>
                                <a class="dropdown-item" href="terms.php">Information</a>
                            </div>
                        </li>
                        <li class="nav-item active"><a class="nav-link" href="panier.php">Panier (<?php echo getCartItemCount(); ?>)</a></li>
                        <li class="nav-item"><a class="nav-link" href="checkout.php">Commande</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <div class="container mt-5 mb-5" style="padding-top: 100px;">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Mon Panier</h2>
                
                <?php if (!empty($_SESSION['panier'])): ?>
                    <form method="POST" action="panier.php">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($_SESSION['panier'] as $product_id => $item): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                 alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo number_format($item['prix'], 2); ?>€
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="quantities[<?php echo $product_id; ?>]" 
                                                   value="<?php echo $item['quantite']; ?>" 
                                                   min="1" 
                                                   max="99" 
                                                   class="form-control" 
                                                   style="width: 80px;">
                                        </td>
                                        <td>
                                            <strong><?php echo number_format($item['prix'] * $item['quantite'], 2); ?>€</strong>
                                        </td>
                                        <td>
                                            <a href="panier.php?action=remove&id=<?php echo $product_id; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Retirer cet article du panier ?');">
                                                <i class="fa fa-trash"></i> Supprimer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr class="table-info">
                                    <th colspan="4" class="text-right">Total du panier</th>
                                    <th colspan="2" class="text-center">
                                        <h4><?php echo number_format(getCartTotal(), 2); ?>€</h4>
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button type="submit" name="update_quantity" class="btn btn-info">
                                    <i class="fa fa-refresh"></i> Mettre à jour les quantités
                                </button>
                                <a href="products.php" class="btn btn-secondary ml-2">
                                    <i class="fa fa-arrow-left"></i> Continuer mes achats
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="checkout.php" class="btn btn-success btn-lg">
                                    <i class="fa fa-shopping-cart"></i> Passer la commande
                                </a>
                            </div>
                        </div>
                    </form>
                    
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h4>Votre panier est vide</h4>
                        <p>Découvrez nos magnifiques figurines !</p>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fa fa-shopping-bag"></i> Voir nos produits
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="inner-content">
                        <p>Contact © 2025 Company Name - WEBSITE by Anas Fekhar: <a href="https://manga-story.fr/maintenance/">E-commerceFigurine.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
</body>
</html>