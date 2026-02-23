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

// Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Vérifier si le panier n'est pas vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

// Variables pour l'affichage
$commandeReussie = false;
$erreur = '';
$numeroCommande = '';
$totalCommande = 0;

// Classe pour gérer les commandes (même que dans checkout.php)
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
            if (isset($item['prix'])) {
                $total += $item['prix'] * $item['quantite'];
            } else {
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
        $fraisLivraison = ($sousTotal > 50) ? 0 : 5.99;
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
                    $produitIdForDb = (is_numeric($product_id) && (int)$product_id > 0) ? (int)$product_id : 0;
                    $sqlDetail = "INSERT INTO details_commande (
                        commande_id, produit_id, nom_produit, prix_unitaire, quantite
                    ) VALUES (?, ?, ?, ?, ?)";
                    
                    $stmtDetail = $this->pdo->prepare($sqlDetail);
                    $stmtDetail->execute([
                        $commandeId,
                        $produitIdForDb,
                        $nom,
                        $prix,
                        $item['quantite']
                    ]);
                    
                    if ($produitIdForDb > 0) {
                        try {
                            $sqlStock = "UPDATE produits SET 
                                stock_quantite = GREATEST(0, stock_quantite - ?),
                                ventes_total = COALESCE(ventes_total, 0) + ?
                                WHERE id = ?";
                            $stmtStock = $this->pdo->prepare($sqlStock);
                            $stmtStock->execute([
                                $item['quantite'],
                                $item['quantite'],
                                $produitIdForDb
                            ]);
                        } catch (Exception $e) {
                            error_log("Erreur mise à jour stock: " . $e->getMessage());
                        }
                    }
                }
            }
            
            $this->pdo->commit();
            return ['numero' => $numeroCommande, 'id' => $commandeId, 'total' => $total];
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}

// Traitement de la commande
try {
    $gestionnaire = new GestionnaireCommande($pdo);
    
    // Récupérer les données du formulaire
    $donneesClient = [
        'fullname' => $_POST['fullname'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal' => $_POST['postal'] ?? '',
        'card_type' => $_POST['card_type'] ?? ''
    ];
    
    // Valider les données obligatoires
    if (empty($donneesClient['fullname']) || empty($donneesClient['address']) || 
        empty($donneesClient['city']) || empty($donneesClient['postal']) || 
        empty($donneesClient['card_type'])) {
        throw new Exception("Veuillez remplir tous les champs obligatoires.");
    }
    
    // Créer la commande
    $resultatCommande = $gestionnaire->creerCommande($donneesClient, $_SESSION['panier']);
    
    // Vider le panier après commande réussie
    unset($_SESSION['panier']);
    
    $numeroCommande = $resultatCommande['numero'];
    $totalCommande = $resultatCommande['total'];
    $commandeReussie = true;
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
    $commandeReussie = false;
}
?>

<?php $current_page = ''; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande validée | Figurine Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .confirmation-container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid rgba(0,0,0,0.08); }
        .confirmation-header { background: linear-gradient(135deg, #D32F2F, #B71C1C); color: #fff; padding: 2rem; text-align: center; }
        .error-header { background: linear-gradient(135deg, #c62828, #b71c1c); }
        .confirmation-content { padding: 2rem; text-align: center; }
        .order-number { background: #f5f5f5; padding: 1.25rem; border-radius: 12px; margin: 1.25rem 0; border-left: 4px solid #D32F2F; }
        .error-message { background: #fff5f5; border: 1px solid #ffcdd2; color: #c62828; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .btn-custom { background: #D32F2F; border: none; color: #fff; padding: 0.6rem 1.5rem; border-radius: 8px; text-decoration: none; display: inline-block; margin: 0.5rem; font-weight: 600; transition: 0.3s; }
        .btn-custom:hover { background: #B71C1C; color: #fff; text-decoration: none; transform: translateY(-2px); }
        .success-icon { font-size: 3rem; color: #D32F2F; margin-bottom: 1rem; }
        .error-icon { font-size: 3rem; color: #c62828; margin-bottom: 1rem; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>
<div class="container theme-main py-5" style="padding-top: 6rem !important;">
    <div class="confirmation-container">
        <?php if ($commandeReussie): ?>
            <!-- Commande réussie -->
            <div class="confirmation-header">
                <h2>✅ Commande Confirmée !</h2>
                <p class="mb-0">Merci pour votre achat</p>
            </div>
            
            <div class="confirmation-content">
                <div class="success-icon">🎉</div>
                
                <h3>Félicitations !</h3>
                <p>Votre commande a été traitée avec succès.</p>
                
                <div class="order-number">
                    <h5>Numéro de commande :</h5>
                    <h3 class="text-danger"><?php echo htmlspecialchars($numeroCommande); ?></h3>
                    <p class="mb-0">Montant total : <strong><?php echo number_format($totalCommande, 2); ?>€</strong></p>
                </div>
                
                <div class="mt-4">
                    <p>📧 Un email de confirmation vous sera envoyé sous peu.</p>
                    <p>📦 Votre commande sera expédiée dans les 24-48h.</p>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn-custom">🏠 Retour à l'accueil</a>
                    <a href="products.php" class="btn-custom">🛍️ Continuer mes achats</a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Erreur -->
            <div class="confirmation-header error-header">
                <h2>❌ Erreur de commande</h2>
                <p class="mb-0">Un problème est survenu</p>
            </div>
            
            <div class="confirmation-content">
                <div class="error-icon">⚠️</div>
                
                <h3>Oups ! Une erreur s'est produite</h3>
                
                <div class="error-message">
                    <strong>Erreur :</strong> <?php echo htmlspecialchars($erreur); ?>
                </div>
                
                <p>Nous nous excusons pour la gêne occasionnée. Veuillez réessayer ou nous contacter si le problème persiste.</p>
                
                <div class="mt-4">
                    <a href="checkout.php" class="btn-custom">🔄 Réessayer</a>
                    <a href="panier.php" class="btn-custom">🛒 Retour au panier</a>
                    <a href="index.php" class="btn-custom">🏠 Accueil</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>