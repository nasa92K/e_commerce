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

// Classe pour gérer les commandes
class GestionnaireCommande {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function genererNumeroCommande() {
        return 'CMD-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
    }
    
    public function calculerTotalPanier($panier) {
        $total = 0;
        foreach ($panier as $product_id => $item) {
            // Utiliser le prix déjà stocké dans la session ou récupérer depuis la DB
            if (isset($item['prix'])) {
                $total += $item['prix'] * $item['quantite'];
            } else {
                // Fallback: récupérer depuis la base de données
                $stmt = $this->pdo->prepare("SELECT prix_original, prix_promotion FROM produits WHERE id = ?");
                $stmt->execute([$product_id]);
                $produit = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($produit) {
                    $prix = ($produit['prix_promotion'] && $produit['prix_promotion'] > 0) 
                        ? $produit['prix_promotion'] 
                        : $produit['prix_original'];
                    $total += $prix * $item['quantite'];
                }
            }
        }
        return $total;
    }
    
    public function creerCommande($donneesClient, $panier) {
        $numeroCommande = $this->genererNumeroCommande();
        $sousTotal = $this->calculerTotalPanier($panier);
        $fraisLivraison = ($sousTotal > 50) ? 0 : 5.99; // Livraison gratuite au-dessus de 50€
        $total = $sousTotal + $fraisLivraison;
        
        $this->pdo->beginTransaction();
        
        try {
            // Insérer la commande principale
            $sqlCommande = "INSERT INTO commandes (
                numero_commande, nom_complet, adresse, ville, code_postal, 
                methode_paiement, sous_total, frais_livraison, total_commande,
                statut_commande, statut_paiement, date_creation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'nouvelle', 'en_attente', NOW())";
            
            $stmt = $this->pdo->prepare($sqlCommande);
            $stmt->execute([
                $numeroCommande,
                $donneesClient['fullname'],
                $donneesClient['address'],
                $donneesClient['city'],
                $donneesClient['postal'],
                $donneesClient['card_type'],
                $sousTotal,
                $fraisLivraison,
                $total
            ]);
            
            $commandeId = $this->pdo->lastInsertId();
            
            // Insérer les détails de la commande
            foreach ($panier as $product_id => $item) {
                $prix = isset($item['prix']) ? $item['prix'] : 0;
                $nom = isset($item['nom']) ? $item['nom'] : 'Produit inconnu';
                
                // Si pas de prix dans la session, récupérer depuis la DB
                if ($prix == 0 || empty($nom)) {
                    $stmt = $this->pdo->prepare("SELECT nom, prix_original, prix_promotion FROM produits WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($produit) {
                        $prix = ($produit['prix_promotion'] && $produit['prix_promotion'] > 0) 
                            ? $produit['prix_promotion'] 
                            : $produit['prix_original'];
                        $nom = $produit['nom'];
                    }
                }
                
                if ($prix > 0) {
                    $sqlDetail = "INSERT INTO commande_details (
                        commande_id, produit_id, nom_produit, prix_unitaire, quantite
                    ) VALUES (?, ?, ?, ?, ?)";
                    
                    $stmtDetail = $this->pdo->prepare($sqlDetail);
                    $stmtDetail->execute([
                        $commandeId,
                        $product_id,
                        $nom,
                        $prix,
                        $item['quantite']
                    ]);
                    
                    // Mettre à jour le stock si la table produits existe
                    try {
                        $sqlStock = "UPDATE produits SET 
                            stock_quantite = GREATEST(0, stock_quantite - ?),
                            ventes_total = COALESCE(ventes_total, 0) + ?
                            WHERE id = ?";
                        
                        $stmtStock = $this->pdo->prepare($sqlStock);
                        $stmtStock->execute([
                            $item['quantite'],
                            $item['quantite'],
                            $product_id
                        ]);
                    } catch (Exception $e) {
                        // Si la mise à jour du stock échoue, continuer quand même
                        error_log("Erreur mise à jour stock: " . $e->getMessage());
                    }
                }
            }
            
            $this->pdo->commit();
            return $numeroCommande;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}

$gestionnaire = new GestionnaireCommande($pdo);

// Récupérer les informations du panier pour affichage (CORRECTION ICI)
$panierDetails = [];
$totalPanier = 0;

// Utiliser $_SESSION['panier'] au lieu de $_SESSION['cart']
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $product_id => $item) {
        // Récupérer les détails du produit depuis la base de données
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ? AND actif = 1");
        $stmt->execute([$product_id]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            $prix = ($produit['prix_promotion'] && $produit['prix_promotion'] > 0) 
                ? $produit['prix_promotion'] 
                : $produit['prix_original'];
        } else {
            // Si produit pas trouvé en DB, utiliser le prix de la session
            $prix = isset($item['prix']) ? $item['prix'] : 0;
            $produit = [
                'nom' => isset($item['nom']) ? $item['nom'] : 'Produit inconnu',
                'image' => isset($item['image']) ? $item['image'] : 'assets/images/default.jpg'
            ];
        }
        
        if ($prix > 0) {
            $panierDetails[] = [
                'produit' => $produit,
                'quantite' => $item['quantite'],
                'prix_unitaire' => $prix,
                'sous_total' => $prix * $item['quantite']
            ];
            
            $totalPanier += $prix * $item['quantite'];
        }
    }
}

$fraisLivraison = ($totalPanier > 50) ? 0 : 5.99;
$totalFinal = $totalPanier + $fraisLivraison;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement | Figurines</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .payment-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .payment-content {
            padding: 30px;
        }

        .cart-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .cart-item:last-child {
            border-bottom: none;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
        }

        .form-section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-section h5 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 15px;
            font-size: 1.1em;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }

        .alert-empty-cart {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            border: none;
            color: #2d3436;
            border-radius: 10px;
        }

        footer {
            background: linear-gradient(135deg, #2d3436, #636e72);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<header>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2d3436, #636e72);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2 class="mb-0">BOUTIQUE DE FIGURINE <span style="color: #667eea;">WEBSITE</span></h2>
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
                    <li class="nav-item active"><a class="nav-link" href="checkout.php">Commande</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- FORMULAIRE DE PAIEMENT -->
<div class="container">
    <div class="payment-container">
        <div class="payment-header">
            <h2>🎭 Finaliser votre commande</h2>
            <p class="mb-0">Sécurisé et rapide</p>
        </div>
        
        <div class="payment-content">
            <!-- Résumé du panier -->
            <?php if (!empty($panierDetails)): ?>
                <div class="cart-summary">
                    <h5>📦 Résumé de votre commande</h5>
                    <?php foreach ($panierDetails as $item): ?>
                        <div class="cart-item">
                            <div>
                                <strong><?php echo htmlspecialchars($item['produit']['nom']); ?></strong>
                                <br><small>Quantité: <?php echo $item['quantite']; ?></small>
                            </div>
                            <div>
                                <strong><?php echo number_format($item['sous_total'], 2); ?>€</strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-item">
                        <span>Sous-total:</span>
                        <span><?php echo number_format($totalPanier, 2); ?>€</span>
                    </div>
                    
                    <div class="cart-item">
                        <span>Frais de livraison:</span>
                        <span><?php echo ($fraisLivraison > 0) ? number_format($fraisLivraison, 2) . '€' : 'GRATUIT'; ?></span>
                    </div>
                    
                    <div class="cart-item">
                        <span>TOTAL:</span>
                        <span><?php echo number_format($totalFinal, 2); ?>€</span>
                    </div>
                    
                    <?php if ($totalPanier < 50): ?>
                        <small class="text-muted">💡 Livraison gratuite à partir de 50€</small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-empty-cart">
                    ⚠️ Votre panier est vide. <a href="products.php">Ajouter des produits</a> avant de commander.
                </div>
            <?php endif; ?>

            <form action="commande-validee.php" method="POST" id="checkoutForm">
                <!-- Informations personnelles -->
                <div class="form-section">
                    <h5>👤 Informations personnelles</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fullname">Nom complet *</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse complète *</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="city">Ville *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal">Code Postal *</label>
                                <input type="text" class="form-control" id="postal" name="postal" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Méthode de paiement -->
                <div class="form-section">
                    <h5>💳 Méthode de paiement</h5>
                    <div class="form-group">
                        <label for="card_type">Type de carte *</label>
                        <select class="form-control" id="card_type" name="card_type" required>
                            <option value="">-- Choisir --</option>
                            <option value="visa">💳 Visa</option>
                            <option value="mastercard">💳 MasterCard</option>
                            <option value="paypal">🅿️ PayPal</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_number">Numéro de carte *</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" 
                               placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry">Expiration (MM/AA) *</label>
                                <input type="text" class="form-control" id="expiry" name="expiry" 
                                       placeholder="12/25" maxlength="5" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                       placeholder="123" maxlength="4" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totaux cachés pour traitement -->
                <input type="hidden" name="sous_total" value="<?php echo $totalPanier; ?>">
                <input type="hidden" name="frais_livraison" value="<?php echo $fraisLivraison; ?>">
                <input type="hidden" name="total_commande" value="<?php echo $totalFinal; ?>">

                <?php if (!empty($panierDetails)): ?>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        🔒 Valider le paiement - <?php echo number_format($totalFinal, 2); ?>€
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-block btn-lg" disabled>
                        Panier vide - Impossible de commander
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>© 2025 Figurine.com - Paiement sécurisé 🔒</p>
    </div>
</footer>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
// Formatage automatique du numéro de carte
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Formatage de la date d'expiration
document.getElementById('expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Validation CVV
document.getElementById('cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
});

// Validation du formulaire
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
    const expiry = document.getElementById('expiry').value;
    const cvv = document.getElementById('cvv').value;
    
    if (cardNumber.length < 13 || cardNumber.length > 19) {
        alert('Numéro de carte invalide');
        e.preventDefault();
        return;
    }
    
    if (!/^\d{2}\/\d{2}$/.test(expiry)) {
        alert('Date d\'expiration invalide (MM/AA)');
        e.preventDefault();
        return;
    }
    
    if (cvv.length < 3 || cvv.length > 4) {
        alert('CVV invalide');
        e.preventDefault();
        return;
    }
});
</script>
</body>
</html>