<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "anas";
$mot_de_passe = "anasdevelopper";
$base_de_donnees = "figurine_db";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion']);
    exit;
}

// Récupération des données POST
$input = json_decode(file_get_contents('php://input'), true);
$produit_id = isset($input['produit_id']) ? (int)$input['produit_id'] : 0;
$quantite = isset($input['quantite']) ? (int)$input['quantite'] : 1;

if ($produit_id <= 0 || $quantite <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Vérification que le produit existe et est disponible
$sql = "SELECT * FROM produits WHERE id = ? AND actif = 1 AND stock_disponible = 1 AND stock_quantite >= ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$produit_id, $quantite]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    echo json_encode(['success' => false, 'message' => 'Produit non disponible']);
    exit;
}

// Initialisation du panier dans la session si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajout ou mise à jour de la quantité dans le panier
if (isset($_SESSION['panier'][$produit_id])) {
    $_SESSION['panier'][$produit_id]['quantite'] += $quantite;
} else {
    $_SESSION['panier'][$produit_id] = [
        'id' => $produit['id'],
        'nom' => $produit['nom'],
        'prix' => $produit['prix_promotion'] ?: $produit['prix_original'],
        'image' => $produit['image_url'],
        'quantite' => $quantite
    ];
}

// Calcul du total du panier
$total_panier = 0;
$nombre_articles = 0;
foreach ($_SESSION['panier'] as $item) {
    $total_panier += $item['prix'] * $item['quantite'];
    $nombre_articles += $item['quantite'];
}

echo json_encode([
    'success' => true, 
    'message' => 'Produit ajouté au panier',
    'panier_total' => number_format($total_panier, 2) . '€',
    'nombre_articles' => $nombre_articles
]);
?>