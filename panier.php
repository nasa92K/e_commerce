<?php
session_start();

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "anas";
$mot_de_passe = "anasdevelopper";
$base_de_donnees = "figurine_db";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour supprimer un produit du panier par ID (clé peut être int ou string : id DB ou c_xxx)
function removeFromCart($product_id) {
    if (!isset($_SESSION['panier'])) return;
    $product_id = (string) $product_id;
    foreach (array_keys($_SESSION['panier']) as $key) {
        if ((string) $key === $product_id) {
            unset($_SESSION['panier'][$key]);
            return;
        }
    }
}

// Fonction pour modifier la quantité d'un produit (id peut être int ou string pour articles personnalisés)
function updateQuantity($product_id, $new_quantity) {
    if (!isset($_SESSION['panier'])) return;
    $product_id = (string) $product_id;
    foreach (array_keys($_SESSION['panier']) as $key) {
        if ((string) $key === $product_id) {
            if ($new_quantity > 0) {
                $_SESSION['panier'][$key]['quantite'] = (int) $new_quantity;
            } else {
                unset($_SESSION['panier'][$key]);
            }
            return;
        }
    }
}

// Gestion des actions (suppression d'un article)
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'remove') {
    $product_id = urldecode((string)$_GET['id']);
    $product_id = trim($product_id);
    if ($product_id !== '') {
        removeFromCart($product_id);
    }
    $retour = isset($_GET['retour']) && $_GET['retour'] === 'checkout' ? 'checkout.php' : 'panier.php';
    header('Location: ' . $retour);
    exit;
}

// Gestion de la mise à jour des quantités via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        updateQuantity($product_id, (int)$quantity);
    }
    header('Location: panier.php');
    exit;
}

// Ajout au panier depuis le formulaire (produits sans ID DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['product_name']) && isset($_POST['product_price'], $_POST['product_image'])) {
    if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
    $key = 'c_' . uniqid();
    $qty = isset($_POST['product_quantity']) ? max(1, (int)$_POST['product_quantity']) : 1;
    $prix = (float)str_replace(',', '.', $_POST['product_price']);
    $_SESSION['panier'][$key] = [
        'nom' => $_POST['product_name'],
        'prix' => $prix,
        'image' => $_POST['product_image'],
        'quantite' => $qty
    ];
    header('Location: panier.php?added=1');
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
$current_page = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Panier | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>
    <div class="container mt-5 mb-5 theme-main" style="padding-top: 6rem;">
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
                                            <a href="panier.php?action=remove&id=<?php echo urlencode($product_id); ?>" 
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
                                <a href="products.php" class="btn btn-secondary ms-2">
                                    <i class="fa fa-arrow-left"></i> Continuer mes achats
                                </a>
                            </div>
                            <div class="col-md-6 text-md-end">
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

<?php include 'includes/footer.php'; ?>
</body>
</html>