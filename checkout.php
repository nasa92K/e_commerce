<?php
session_start();

// --- 1. CONFIGURATION AVATAR & BDD ---
$host = "localhost";
$db   = "figurine_db";
$user = "anas";
$pass = "anasdevelopper";

$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';
$user_avatar = $base_assets . 'assets/img/default-avatar.png';
if (!file_exists(__DIR__ . '/assets/img/default-avatar.png')) {
    $user_avatar = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['user_id'])) {
        if (!empty($_SESSION['user_avatar']) && is_string($_SESSION['user_avatar']) && file_exists(__DIR__ . '/' . $_SESSION['user_avatar'])) {
            $user_avatar = $base_assets . $_SESSION['user_avatar'];
        } else {
            $stmt = $pdo->prepare("SELECT username, avatar, photo_profil FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if (is_array($currentUser)) {
                if (isset($currentUser['username'])) $_SESSION['username'] = $currentUser['username'];
                $avatarPath = $currentUser['avatar'] ?? $currentUser['photo_profil'] ?? '';
                if ($avatarPath !== '' && is_string($avatarPath) && file_exists(__DIR__ . '/' . $avatarPath)) {
                    $user_avatar = $base_assets . $avatarPath;
                    $_SESSION['user_avatar'] = $avatarPath;
                } else {
                    $_SESSION['user_avatar'] = '';
                }
            }
        }
    }
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// --- 2. LOGIQUE COMMANDE (Intacte) ---
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
            $sqlCommande = "INSERT INTO commandes (
                numero_commande, nom_complet, adresse, ville, code_postal, 
                methode_paiement, sous_total, frais_livraison, total_commande,
                statut_commande, statut_paiement, date_creation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'nouvelle', 'en_attente', NOW())";
            
            $stmt = $this->pdo->prepare($sqlCommande);
            $stmt->execute([
                $numeroCommande, $donneesClient['fullname'], $donneesClient['address'],
                $donneesClient['city'], $donneesClient['postal'], $donneesClient['card_type'],
                $sousTotal, $fraisLivraison, $total
            ]);
            
            $commandeId = $this->pdo->lastInsertId();
            
            foreach ($panier as $product_id => $item) {
                $prix = isset($item['prix']) ? $item['prix'] : 0;
                $nom = isset($item['nom']) ? $item['nom'] : 'Produit inconnu';
                
                if ($prix == 0 || empty($nom)) {
                    $stmt = $this->pdo->prepare("SELECT nom, prix_original, prix_promotion FROM produits WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($produit) {
                        $prix = ($produit['prix_promotion'] && $produit['prix_promotion'] > 0) 
                            ? $produit['prix_promotion'] : $produit['prix_original'];
                        $nom = $produit['nom'];
                    }
                }
                
                if ($prix > 0) {
                    $produitIdForDb = (is_numeric($product_id) && (int)$product_id > 0) ? (int)$product_id : 0;
                    $sqlDetail = "INSERT INTO commande_details (
                        commande_id, produit_id, nom_produit, prix_unitaire, quantite
                    ) VALUES (?, ?, ?, ?, ?)";
                    $stmtDetail = $this->pdo->prepare($sqlDetail);
                    $stmtDetail->execute([$commandeId, $produitIdForDb, $nom, $prix, $item['quantite']]);
                    
                    if ($produitIdForDb > 0) {
                        try {
                            $sqlStock = "UPDATE produits SET 
                                stock_quantite = GREATEST(0, stock_quantite - ?),
                                ventes_total = COALESCE(ventes_total, 0) + ?
                                WHERE id = ?";
                            $stmtStock = $this->pdo->prepare($sqlStock);
                            $stmtStock->execute([$item['quantite'], $item['quantite'], $produitIdForDb]);
                        } catch (Exception $e) {}
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
$panierDetails = [];
$totalPanier = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $product_id => $item) {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ? AND actif = 1");
        $stmt->execute([$product_id]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            $prix = ($produit['prix_promotion'] && $produit['prix_promotion'] > 0) 
                ? $produit['prix_promotion'] : $produit['prix_original'];
        } else {
            $prix = isset($item['prix']) ? $item['prix'] : 0;
            $produit = [
                'nom' => isset($item['nom']) ? $item['nom'] : 'Produit inconnu',
                'image' => isset($item['image']) ? $item['image'] : 'assets/images/default.jpg'
            ];
        }
        
        if ($prix > 0) {
            $panierDetails[] = [
                'product_id' => $product_id,
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

$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

$current_page = 'checkout';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande | Figurine Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        :root { --checkout-rouge: #D32F2F; --checkout-rouge-fonce: #B71C1C; }
        .checkout-page { padding-top: 5.5rem; min-height: 100vh; background: linear-gradient(180deg, #f8f9fa 0%, #eef0f2 100%); }
        .checkout-hero { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #fff; padding: 2rem 0; margin-bottom: 2rem; }
        .checkout-hero h1 { font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 800; margin: 0; }
        .checkout-hero p { margin: 0.5rem 0 0; opacity: 0.9; font-size: 1rem; }
        .checkout-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 1.5rem; }
        .checkout-card .card-header-custom { background: #fff; border-bottom: 2px solid var(--checkout-rouge); padding: 1rem 1.5rem; font-weight: 700; font-size: 1.1rem; color: #1a1a1a; display: flex; align-items: center; gap: 0.5rem; }
        .checkout-card .card-header-custom i { color: var(--checkout-rouge); }
        .checkout-card .card-body { padding: 1.5rem; }
        .checkout-item { display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid rgba(0,0,0,0.06); }
        .checkout-item:last-of-type { border-bottom: none; }
        .checkout-item-img { width: 64px; height: 64px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0; }
        .checkout-item-name { font-weight: 600; color: #1a1a1a; }
        .checkout-item-qty { font-size: 0.9rem; color: #6c757d; }
        .checkout-item-price { font-weight: 700; color: var(--checkout-rouge); margin-left: auto; }
        .checkout-item-remove { flex-shrink: 0; }
        .checkout-item-remove .btn-remove-item { padding: 0.35rem 0.6rem; font-size: 0.8rem; color: #c62828; border-color: #c62828; }
        .checkout-item-remove .btn-remove-item:hover { background: #c62828; color: #fff; }
        .checkout-totals { padding: 1rem 0; }
        .checkout-totals .row-total { display: flex; justify-content: space-between; padding: 0.5rem 0; color: #555; }
        .checkout-totals .row-total.final { font-size: 1.25rem; font-weight: 800; color: var(--checkout-rouge); border-top: 2px solid rgba(0,0,0,0.08); margin-top: 0.75rem; padding-top: 1rem; }
        .step-badge { width: 32px; height: 32px; border-radius: 50%; background: var(--checkout-rouge); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; }
        .form-label { font-weight: 600; color: #333; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); padding: 0.65rem 1rem; }
        .form-control:focus, .form-select:focus { border-color: var(--checkout-rouge); box-shadow: 0 0 0 0.2rem rgba(211,47,47,0.15); }
        .input-group-text { border-radius: 10px 0 0 10px; background: #f8f9fa; border-color: rgba(0,0,0,0.12); }
        .btn-commander { background: linear-gradient(135deg, var(--checkout-rouge), var(--checkout-rouge-fonce)); color: #fff; border: none; font-weight: 700; font-size: 1.1rem; padding: 1rem 1.5rem; border-radius: 12px; width: 100%; transition: transform 0.2s, box-shadow 0.2s; text-transform: uppercase; letter-spacing: 0.05em; }
        .btn-commander:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(211,47,47,0.35); }
        .btn-commander:disabled { opacity: 0.7; transform: none; }
        .empty-cart-box { text-align: center; padding: 3rem 2rem; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 2px dashed rgba(0,0,0,0.1); }
        .empty-cart-box .display-4 { color: #ccc; margin-bottom: 1rem; }
        .empty-cart-box .btn { border-radius: 10px; font-weight: 600; padding: 0.75rem 1.5rem; }
        .livraison-gratuite { background: rgba(211,47,47,0.08); color: var(--checkout-rouge); font-size: 0.85rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 8px; margin-top: 0.5rem; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

<main class="checkout-page">
    <section class="checkout-hero">
        <div class="container">
            <h1><i class="fa fa-shopping-bag me-2"></i>Finaliser la commande</h1>
            <p>Livraison sécurisée — Paiement en toute confiance</p>
        </div>
    </section>

    <div class="container pb-5">
        <?php if (empty($panierDetails)): ?>
            <div class="empty-cart-box">
                <div class="display-4"><i class="fa fa-shopping-cart"></i></div>
                <h4 class="mb-2">Votre panier est vide</h4>
                <p class="text-muted mb-4">Ajoutez des figurines avant de passer commande.</p>
                <a href="products.php" class="btn btn-danger">Voir la boutique</a>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-5 col-xl-4 order-lg-2">
                <div class="checkout-card sticky-top" style="top: 6rem;">
                    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span><i class="fa fa-receipt"></i> Résumé de la commande</span>
                        <a href="panier.php" class="btn btn-outline-danger btn-sm">Modifier le panier</a>
                    </div>
                    <div class="card-body">
                        <?php foreach ($panierDetails as $item): 
                            $img = $item['produit']['image_url'] ?? $item['produit']['image'] ?? '';
                            $imgSrc = ($img !== '' && strpos($img, 'http') !== 0) ? $base_assets . $img : ($img ?: $base_assets . 'assets/img/default-avatar.png');
                            $pid = isset($item['product_id']) ? $item['product_id'] : '';
                        ?>
                        <div class="checkout-item">
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="" class="checkout-item-img">
                            <div class="flex-grow-1 min-w-0">
                                <div class="checkout-item-name"><?php echo htmlspecialchars($item['produit']['nom']); ?></div>
                                <div class="checkout-item-qty">Qté : <?php echo (int)$item['quantite']; ?></div>
                            </div>
                            <div class="checkout-item-price"><?php echo number_format($item['sous_total'], 2); ?> €</div>
                            <div class="checkout-item-remove">
                                <a href="panier.php?action=remove&id=<?php echo urlencode($pid); ?>&retour=checkout" class="btn btn-outline-danger btn-sm btn-remove-item" onclick="return confirm('Retirer cet article du panier ?');" title="Retirer du panier"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="checkout-totals">
                            <div class="row-total"><span>Sous-total</span><span><?php echo number_format($totalPanier, 2); ?> €</span></div>
                            <div class="row-total"><span>Livraison</span><span><?php echo $fraisLivraison > 0 ? number_format($fraisLivraison, 2) . ' €' : 'Offerte'; ?></span></div>
                            <?php if ($fraisLivraison == 0): ?><div class="livraison-gratuite"><i class="fa fa-gift me-1"></i> Offerte à partir de 50 € d'achat</div><?php endif; ?>
                            <div class="row-total final"><span>Total</span><span><?php echo number_format($totalFinal, 2); ?> €</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-xl-8 order-lg-1">
                <form action="commande-validee.php" method="POST" id="checkoutForm">
                    <div class="checkout-card mb-4">
                        <div class="card-header-custom">
                            <span class="step-badge">1</span> Adresse de livraison
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fullname" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Jean Dupont" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="jean@email.com">
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="123 Rue de la Figurine" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="Paris" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="postal" class="form-label">Code postal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postal" name="postal" placeholder="75000" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-card mb-4">
                        <div class="card-header-custom">
                            <span class="step-badge">2</span> Paiement
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="card_type" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                                <select class="form-select" id="card_type" name="card_type" required>
                                    <option value="">Choisir...</option>
                                    <option value="visa">Carte Bancaire / Visa</option>
                                    <option value="mastercard">MasterCard</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Numéro de carte <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-credit-card text-muted"></i></span>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="expiry" class="form-label">Expiration (MM/AA) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/AA" maxlength="5" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="sous_total" value="<?php echo htmlspecialchars($totalPanier); ?>">
                    <input type="hidden" name="frais_livraison" value="<?php echo htmlspecialchars($fraisLivraison); ?>">
                    <input type="hidden" name="total_commande" value="<?php echo htmlspecialchars($totalFinal); ?>">

                    <div class="checkout-card">
                        <div class="card-body text-center py-4">
                            <button type="submit" class="btn btn-commander btn-lg">
                                <i class="fa fa-lock me-2"></i>Payer <?php echo number_format($totalFinal, 2); ?> €
                            </button>
                            <p class="small text-muted mt-3 mb-0"><i class="fa fa-shield-alt"></i> Paiement sécurisé SSL</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
<script>
(function() {
    var cardNumber = document.getElementById('card_number');
    var expiry = document.getElementById('expiry');
    var cvv = document.getElementById('cvv');
    var form = document.getElementById('checkoutForm');
    if (!form) return;
    if (cardNumber) cardNumber.addEventListener('input', function(e) {
        var v = e.target.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
        e.target.value = v.match(/.{1,4}/g) ? v.match(/.{1,4}/g).join(' ') : v;
    });
    if (expiry) expiry.addEventListener('input', function(e) {
        var v = e.target.value.replace(/\D/g, '');
        if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2, 4);
        e.target.value = v;
    });
    if (cvv) cvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    form.addEventListener('submit', function(e) {
        if (!cardNumber) return;
        var num = cardNumber.value.replace(/\s/g, '');
        if (num.length < 13 || num.length > 19) {
            e.preventDefault();
            alert('Numéro de carte invalide.');
        }
    });
})();
</script>
</body>
</html>