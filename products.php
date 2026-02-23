<?php
session_start();
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base_de_donnees = "figurine_db";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $pdo = null;
}

$produits = [];
if ($pdo) {
    $sql = "SELECT * FROM produits WHERE actif = 1 ORDER BY date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Figurines affichées si la BDD est vide ou en complément (liste statique achetable)
$figurines_catalogue = [
    [
        'id' => null,
        'nom' => 'Elden Ring : Malenia',
        'image_url' => 'imagefigurine/malenia.jpg',
        'prix_original' => 199.99,
        'prix_promotion' => 189.99,
        'description' => 'Figurine officielle Malenia, Blade of Miquella — Elden Ring. Détails premium, édition limitée.',
        'page_details' => 'products.php',
        'stock_quantite' => 5,
        'badge' => 'Boss / Or',
    ],
    [
        'id' => null,
        'nom' => 'Kratos (God of War Ragnarök)',
        'image_url' => 'imagefigurine/worldbox-onetoys-announces-kratos-god-of-war-ragnarok-figure-v0-j76h3zspyvy91.webp',
        'prix_original' => 169.00,
        'prix_promotion' => 159.00,
        'description' => 'Kratos Worldbox OneToys — God of War Ragnarök. Pose dynamique, finitions métal et cuir.',
        'page_details' => 'products.php',
        'stock_quantite' => 3,
        'badge' => 'Action',
    ],
    [
        'id' => null,
        'nom' => 'Link (Tears of the Kingdom)',
        'image_url' => 'imagefigurine/link.jpg',
        'prix_original' => 139.99,
        'prix_promotion' => 129.99,
        'description' => 'Link Zelda Tears of the Kingdom — figurine collector avec base thématique.',
        'page_details' => 'products.php',
        'stock_quantite' => 8,
        'badge' => 'RPG',
    ],
    [
        'id' => null,
        'nom' => 'Cyberpunk 2077 : Johnny Silverhand',
        'image_url' => 'imagefigurine/Exclu1.webp',
        'prix_original' => 149.00,
        'prix_promotion' => 139.00,
        'description' => 'Johnny Silverhand — Cyberpunk 2077. Exclu boutique, édition détaillée avec accessoires.',
        'page_details' => 'products.php',
        'stock_quantite' => 4,
        'badge' => 'Cyber',
    ],
    [
        'id' => null,
        'nom' => 'Goku Ultra Instinct',
        'image_url' => 'imagefigurine/product_figures_dragon-ball_blood-of-saiyans_goku_ultra-instinct.webp',
        'prix_original' => 109.99,
        'prix_promotion' => 99.99,
        'description' => 'Dragon Ball Super — Goku Ultra Instinct. Blood of Saiyans, pose signature et effet lumineux.',
        'page_details' => 'products.php',
        'stock_quantite' => 6,
        'badge' => 'Combat',
    ],
    [
        'id' => null,
        'nom' => 'Guts (Berserker Armor)',
        'image_url' => 'imagefigurine/guts.jpg',
        'prix_original' => 189.00,
        'prix_promotion' => 179.00,
        'description' => 'Berserk — Guts en armure du Berserker. Sculpture détaillée, ambiance Dark Fantasy.',
        'page_details' => 'products.php',
        'stock_quantite' => 2,
        'badge' => 'Dark Fantasy',
    ],
    [
        'id' => null,
        'nom' => 'POP Ghost Face',
        'image_url' => 'imagefigurine/61Bwj6ERsoL.jpg',
        'prix_original' => 19.99,
        'prix_promotion' => 14.99,
        'description' => 'Funko POP — Ghost Face, icône du film Scream. Idéal pour les fans d\'horreur.',
        'page_details' => 'product-details-ghostface.php',
        'stock_quantite' => 12,
        'badge' => 'Horreur',
    ],
    [
        'id' => null,
        'nom' => 'POP Leon S. Kennedy',
        'image_url' => 'imagefigurine/POPleonkennedy.jpg',
        'prix_original' => 19.99,
        'prix_promotion' => 14.99,
        'description' => 'Funko POP — Leon Kennedy, Resident Evil. Détails fidèles au jeu.',
        'page_details' => 'product-details-leonkennedy.php',
        'stock_quantite' => 10,
        'badge' => 'Sci-Fi',
    ],
    [
        'id' => null,
        'nom' => 'POP Haerin (NewJeans)',
        'image_url' => 'imagefigurine/POPhaerin.jpg',
        'prix_original' => 24.99,
        'prix_promotion' => 19.99,
        'description' => 'Funko POP — Kang Haerin du groupe K-Pop NewJeans. Édition collector.',
        'page_details' => 'product-details-haerin.php',
        'stock_quantite' => 7,
        'badge' => 'K-Pop',
    ],
    [
        'id' => null,
        'nom' => 'Figurine Squid Game (Garde)',
        'image_url' => 'imagefigurine/POPsquidgame.jpg',
        'prix_original' => 24.99,
        'prix_promotion' => 19.99,
        'description' => 'Garde Squid Game — réplique du masque et tenue de la série. Qualité premium.',
        'page_details' => 'product-details-squidgame.php',
        'stock_quantite' => 9,
        'badge' => 'Série',
    ],
    [
        'id' => null,
        'nom' => 'Figurine Mercredi Addams',
        'image_url' => 'imagefigurine/POPwednesday.jpg',
        'prix_original' => 19.99,
        'prix_promotion' => 14.99,
        'description' => 'Mercredi Addams — univers gothique. Parfaite pour les fans de la série.',
        'page_details' => 'product-details-mercrediaddams.php',
        'stock_quantite' => 11,
        'badge' => 'Fantasy',
    ],
    [
        'id' => null,
        'nom' => 'Figurine LeBron James',
        'image_url' => 'imagefigurine/POPlebronjames.jpg',
        'prix_original' => 19.99,
        'prix_promotion' => 14.99,
        'description' => 'LeBron James — légende NBA en figurine. Pour les passionnés de basket.',
        'page_details' => 'product-details-lebronjames.php',
        'stock_quantite' => 15,
        'badge' => 'Sport',
    ],
];

// Afficher le catalogue statique si pas de produits en BDD
$liste_affichage = !empty($produits) ? $produits : $figurines_catalogue;
$current_page = 'products';

// Base URL pour les assets (images) — permet un chargement correct en sous-dossier ou à la racine
$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Boutique Figurines | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .products-page { padding-top: 5.5rem; min-height: 100vh; background: linear-gradient(180deg, #f8f9fa 0%, #eef0f2 100%); }
        .products-hero { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: #fff; padding: 3rem 0; margin-bottom: 2.5rem; position: relative; overflow: hidden; }
        .products-hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h40v40H0V0zm20 20v-2h-2v2h-2v2h2v2h2v-2h2v-2h-2z' fill='%23ffffff' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E"); opacity: 0.8; }
        .products-hero .container { position: relative; z-index: 1; }
        .products-hero h1 { font-weight: 800; font-size: clamp(1.75rem, 4vw, 2.25rem); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem; }
        .products-hero .lead { color: rgba(255,255,255,0.85); font-size: 1rem; }
        .products-hero .accent { color: #D32F2F; }
        .products-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 2rem; }
        .products-toolbar .result-count { color: #5c5c5c; font-size: 0.95rem; }
        .product-card { background: #fff; border-radius: 14px; overflow: hidden; height: 100%; border: 1px solid rgba(0,0,0,0.06); transition: transform 0.25s ease, box-shadow 0.25s ease; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 14px 32px rgba(0,0,0,0.1), 0 0 0 1px rgba(211,47,47,0.12); }
        .product-card .img-wrap { position: relative; aspect-ratio: 1; background: #f0f0f0; overflow: hidden; }
        .product-card .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s ease; }
        .product-card:hover .img-wrap img { transform: scale(1.05); }
        .product-card .badge-prod { position: absolute; top: 10px; left: 10px; font-size: 0.65rem; font-weight: 700; padding: 0.3rem 0.6rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.04em; background: #D32F2F; color: #fff; z-index: 2; }
        .product-card .badge-stock { position: absolute; top: 10px; right: 10px; font-size: 0.65rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: 6px; background: rgba(0,0,0,0.6); color: #fff; z-index: 2; }
        .product-card .card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .product-card .card-title { font-size: 1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; line-height: 1.3; }
        .product-card .card-title a { color: inherit; text-decoration: none; }
        .product-card .card-title a:hover { color: #D32F2F; }
        .product-card .card-text { font-size: 0.85rem; color: #5c5c5c; margin-bottom: 1rem; line-height: 1.45; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-card .price-wrap { margin-bottom: 1rem; }
        .product-card .price-current { font-size: 1.2rem; font-weight: 800; color: #D32F2F; }
        .product-card .price-old { font-size: 0.9rem; color: #999; text-decoration: line-through; margin-left: 0.35rem; }
        .product-card .btn-group-cart { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; }
        .product-card .btn-detail { background: #fff; color: #1a1a1a; border: 1px solid rgba(0,0,0,0.15); font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0.9rem; border-radius: 8px; text-decoration: none; transition: all 0.2s; }
        .product-card .btn-detail:hover { background: #f5f5f5; color: #D32F2F; border-color: #D32F2F; }
        .product-card .btn-cart { background: #D32F2F; color: #fff; border: none; font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0.9rem; border-radius: 8px; transition: all 0.2s; cursor: pointer; }
        .product-card .btn-cart:hover { background: #B71C1C; color: #fff; transform: scale(1.02); }
        .product-card .btn-cart:disabled { background: #adb5bd; cursor: not-allowed; transform: none; }
        .toast-panier { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

<main class="products-page">
    <section class="products-hero">
        <div class="container">
            <h1>Boutique <span class="accent">Figurines</span></h1>
            <p class="lead mb-0">Anime, jeux vidéo, POP — trouvez la pièce qui complète votre collection.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="products-toolbar">
            <span class="result-count"><?php echo count($liste_affichage); ?> figurine(s) disponible(s)</span>
        </div>

        <div class="row g-4">
            <?php foreach ($liste_affichage as $p): 
                $fromDb = isset($p['id']) && $p['id'] !== null;
                $nom = $p['nom'] ?? '';
                $img = $p['image_url'] ?? '';
                $prix_orig = (float)($p['prix_original'] ?? 0);
                $prix_promo = isset($p['prix_promotion']) && $p['prix_promotion'] > 0 && $p['prix_promotion'] < $prix_orig ? (float)$p['prix_promotion'] : null;
                $prix_affiché = $prix_promo !== null ? $prix_promo : $prix_orig;
                $desc = $p['description'] ?? '';
                $page_details = $p['page_details'] ?? 'products.php';
                $stock = (int)($p['stock_quantite'] ?? 0);
                $badge = $p['badge'] ?? '';
                $en_stock = $fromDb ? (isset($p['stock_disponible']) && $p['stock_disponible'] && $stock > 0) : ($stock > 0);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card card h-100">
                    <div class="img-wrap">
                        <?php if ($badge): ?><span class="badge-prod"><?php echo htmlspecialchars($badge); ?></span><?php endif; ?>
                        <span class="badge-stock"><?php echo $en_stock ? 'En stock' : 'Rupture'; ?></span>
                        <a href="<?php echo htmlspecialchars($page_details); ?>">
                            <img src="<?php echo htmlspecialchars($base_assets . $img); ?>" alt="<?php echo htmlspecialchars($nom); ?>" loading="lazy">
                        </a>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><a href="<?php echo htmlspecialchars($page_details); ?>"><?php echo htmlspecialchars($nom); ?></a></h3>
                        <p class="card-text"><?php echo htmlspecialchars($desc); ?></p>
                        <div class="price-wrap">
                            <span class="price-current"><?php echo number_format($prix_affiché, 2); ?> €</span>
                            <?php if ($prix_promo !== null): ?><span class="price-old"><?php echo number_format($prix_orig, 2); ?> €</span><?php endif; ?>
                        </div>
                        <div class="btn-group-cart">
                            <a href="<?php echo htmlspecialchars($page_details); ?>" class="btn-detail">Voir</a>
                            <?php if ($fromDb && $en_stock): ?>
                                <button type="button" class="btn-cart" onclick="ajouterAuPanier(<?php echo (int)$p['id']; ?>)">Ajouter au panier</button>
                            <?php elseif (!$fromDb && $en_stock): ?>
                                <form method="post" action="panier.php" class="d-inline">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($nom); ?>">
                                    <input type="hidden" name="product_price" value="<?php echo number_format($prix_affiché, 2, '.', ''); ?>">
                                    <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($img); ?>">
                                    <input type="hidden" name="product_quantity" value="1">
                                    <button type="submit" class="btn-cart">Ajouter au panier</button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="btn-cart" disabled>Rupture de stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<div id="toast-panier" class="toast align-items-center text-white bg-success border-0 toast-panier" role="alert" aria-live="polite" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">Produit ajouté au panier.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function ajouterAuPanier(produitId) {
    fetch('ajouter_panier.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produit_id: produitId, quantite: 1 })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var toast = document.getElementById('toast-panier');
            if (toast) {
                var bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            } else {
                alert('Produit ajouté au panier !');
            }
        } else {
            alert('Erreur : ' + (data.message || 'impossible d\'ajouter au panier'));
        }
    })
    .catch(function() {
        alert('Erreur lors de l\'ajout au panier');
    });
}
</script>
</body>
</html>
