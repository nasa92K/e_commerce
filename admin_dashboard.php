<?php
session_start();

// 1. Sécurité et Accès : Vérification stricte du rôle et de l'email de l'administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['email']) || $_SESSION['email'] !== 'anasfigurine@gmail.com') {
    // Redirection immédiate si l'utilisateur n'est pas le véritable admin
    header("Location: index.php");
    exit();
}

// 2. Connexion PDO sécurisée à la base de données
$host = "localhost";
$db   = "figurine_db";
$user = "anas";
$pass = "anasdevelopper";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Activation des exceptions pour détecter les erreurs SQL plus facilement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$message = "";
$view = $_GET['view'] ?? 'produits';

// Dossier pour l'upload des images de figurines
$uploadDir = 'assets/images/';
if (!is_dir($uploadDir)) {
    // Création du dossier s'il n'existe pas
    @mkdir($uploadDir, 0777, true);
}

// 3. Traitement des actions du CRUD (Produits)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // -- AJOUTER UNE FIGURINE --
    if ($action === 'add') {
        $nom = trim($_POST['nom'] ?? '');
        $prix_original = (float) ($_POST['prix_original'] ?? 0);
        $prix_promotion = $_POST['prix_promotion'] !== '' ? (float) $_POST['prix_promotion'] : null;
        $stock_quantite = (int) ($_POST['stock_quantite'] ?? 0);
        
        $image_url = '';

        // Gestion du sélecteur de fichier pour l'image
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $fileName = basename($_FILES['image_file']['name']);
            // Générer un nom unique pour éviter d'écraser des fichiers existants
            $uniqueFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $fileName);
            $destination = $uploadDir . $uniqueFileName;
            
            if (move_uploaded_file($tmpName, $destination)) {
                $image_url = $destination;
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors du téléchargement de l'image.</div>";
            }
        }

        if ($nom !== '' && $prix_original > 0) {
            $sql = "INSERT INTO produits (nom, prix_original, prix_promotion, stock_quantite, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nom, $prix_original, $prix_promotion, $stock_quantite, $image_url])) {
                $message = "<div class='alert alert-success d-flex align-items-center'><i class='fa-solid fa-circle-check me-2'></i> Produit ajouté avec succès !</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de l'ajout du produit dans la base de données.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>Le nom et le prix original sont obligatoires.</div>";
        }
    }

    // -- MODIFIER UNE FIGURINE --
    if ($action === 'edit') {
        $id = (int) $_POST['id'];
        $prix_original = (float) str_replace(',', '.', $_POST['prix_original']);
        $prix_promotion = $_POST['prix_promotion'] !== '' ? (float) str_replace(',', '.', $_POST['prix_promotion']) : null;
        $stock_quantite = (int) $_POST['stock_quantite'];

        $sql = "UPDATE produits SET prix_original = ?, prix_promotion = ?, stock_quantite = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$prix_original, $prix_promotion, $stock_quantite, $id])) {
            $message = "<div class='alert alert-success d-flex align-items-center'><i class='fa-solid fa-circle-check me-2'></i> Produit mis à jour avec succès !</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour du produit.</div>";
        }
    }

    // -- SUPPRIMER UNE FIGURINE --
    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        
        // Supprimer l'image associée du serveur si elle existe
        $stmt = $pdo->prepare("SELECT image_url FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if ($prod && !empty($prod['image_url']) && file_exists($prod['image_url'])) {
            unlink($prod['image_url']);
        }

        // Suppression définitive du produit de la table
        $sql = "DELETE FROM produits WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id])) {
            $message = "<div class='alert alert-success d-flex align-items-center'><i class='fa-solid fa-trash-can me-2'></i> Produit supprimé définitivement.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la suppression.</div>";
        }
    }
}

// 4. Récupération des données selon la vue sélectionnée
if ($view === 'produits') {
    try {
        // Tentative de récupération des ventes par produit via la table 'ligne_commande'
        // Si elle n'existe pas, interception par le 'catch' et fallback normal.
        $stmt = $pdo->query("
            SELECT p.*, COALESCE(SUM(lc.quantite), 0) as ventes 
            FROM produits p 
            LEFT JOIN ligne_commande lc ON p.id = lc.produit_id 
            GROUP BY p.id 
            ORDER BY p.id DESC
        ");
        $items = $stmt->fetchAll();
    } catch(PDOException $e) {
        try {
            // Fallback : La table commande_details ou ligne_commande n'existe peut-être pas encore
            $stmt = $pdo->query("SELECT *, 0 AS ventes FROM produits ORDER BY id DESC");
            $items = $stmt->fetchAll();
        } catch(PDOException $e) {
            $items = [];
            $message = "<div class='alert alert-warning'>La table 'produits' n'existe pas ou est inaccessible. (" . htmlspecialchars($e->getMessage()) . ")</div>";
        }
    }
} elseif ($view === 'commandes') {
    try {
        $stmt = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC, id DESC");
        $items = $stmt->fetchAll();
    } catch(PDOException $e) {
        $items = [];
        $message = "<div class='alert alert-warning'>La table 'commandes' n'existe pas.</div>";
    }
} elseif ($view === 'utilisateurs') {
    try {
        $stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC");
        $items = $stmt->fetchAll();
    } catch(PDOException $e) {
        $items = [];
        $message = "<div class='alert alert-warning'>La table 'utilisateurs' n'existe pas.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Figurine Shop</title>
    <!-- Framework Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Design Moderne et Dark Mode Cohérent */
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background-color: #000000;
            border-right: 1px solid #2d2d2d;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #2d2d2d;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 2.5rem;
            width: calc(100% - 260px);
            background-color: #121212;
        }

        /* Nav-links Sidebar */
        .nav-link {
            color: #9e9e9e;
            padding: 0.9rem 1.5rem;
            margin: 0.3rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #ffffff;
            background-color: #212529;
            box-shadow: inset 3px 0 0 #dc3545; /* Ligne rouge à gauche inspirée de Navbar */
        }
        
        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }

        /* Composants personnalisés */
        .card {
            background-color: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        
        .card-header {
            background-color: #1e1e1e;
            border-bottom: 1px solid #2d2d2d;
            padding: 1.25rem 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }

        /* Tables Bootstrap adaptées au Dark Mode */
        .table-dark {
            --bs-table-bg: transparent;
            color: #e0e0e0;
        }
        
        .table-dark thead th {
            background-color: #141414;
            border-bottom-color: #2d2d2d;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            font-weight: 600;
            color: #9e9e9e;
            padding: 1rem;
        }

        .table-dark tbody td {
            border-color: #2d2d2d;
            vertical-align: middle;
            padding: 1rem;
        }

        .table-dark tbody tr:hover {
            background-color: rgba(255,255,255,0.03);
        }

        /* Images miniatures */
        .img-thumbnail-custom {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #2b2b2b;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Formulaires au format sombre */
        .input-group-text, .form-control, .form-select {
            background-color: #2b2b2b;
            border: 1px solid #3d3d3d;
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background-color: #333;
            border-color: #dc3545;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .form-control::file-selector-button {
            background-color: #3d3d3d;
            color: white;
            border: none;
            padding: 0.375rem 0.75rem;
            margin-inline-end: 1rem;
            transition: background-color 0.2s;
        }
        .form-control::file-selector-button:hover {
            background-color: #4d4d4d;
        }

    </style>
</head>
<body>

    <!-- Sidebar de Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="text-white m-0 tracking-wide font-weight-bold">
                <i class="fa-solid fa-user-shield me-2 text-danger"></i>ADMIN
            </h4>
        </div>
        
        <ul class="nav flex-column mb-auto mt-3">
            <li class="nav-item">
                <a href="?view=produits" class="nav-link <?= $view === 'produits' ? 'active' : '' ?>">
                    <i class="fa-solid fa-box-open"></i> Figurine & Stocks
                </a>
            </li>
            <li class="nav-item">
                <a href="?view=commandes" class="nav-link <?= $view === 'commandes' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Ventes & Suivi
                </a>
            </li>
            <li class="nav-item">
                <a href="?view=utilisateurs" class="nav-link <?= $view === 'utilisateurs' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Utilisateurs
                </a>
            </li>
        </ul>
        
        <div class="px-4 pb-4 mt-auto">
            <div class="p-3 bg-dark rounded text-center border border-secondary border-opacity-25 mb-3">
                <i class="fa-solid fa-circle-user fs-2 text-danger mb-2"></i>
                <div class="text-white fw-bold"><?= htmlspecialchars($_SESSION['prenom'] ?? 'Administrateur') ?></div>
                <small class="text-muted text-break"><?= htmlspecialchars($_SESSION['email']) ?></small>
            </div>
            <a href="index.php" class="btn btn-outline-danger w-100">
                <i class="fa-solid fa-arrow-left me-2"></i>Retour Boutique
            </a>
        </div>
    </div>

    <!-- Contenu Principal de la Page -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-dark">
            <h2 class="h3 fw-bold m-0 text-white">
                <?php
                if ($view === 'produits') echo 'Gestion du Catalogue Produits';
                elseif ($view === 'commandes') echo 'Suivi des Commandes et Ventes';
                elseif ($view === 'utilisateurs') echo 'Base de données des Clients';
                ?>
            </h2>
        </div>

        <!-- Zone pour les messages Flash (Succès / Erreurs) -->
        <?= $message ?>

        <?php if ($view === 'produits'): ?>
            <!-- Section 1 : Formulaire d'Ajout -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-white"><i class="fa-solid fa-circle-plus me-2 text-danger"></i>Ajouter une nouvelle Figurine</h5>
                </div>
                <div class="card-body">
                    <form action="admin_dashboard.php?view=produits" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                            <label class="form-label text-muted small fw-bold">Nom complet (Modèle)</label>
                            <input type="text" name="nom" class="form-control" required placeholder="Ex: Pop! Zoro Enma">
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label text-muted small fw-bold">Prix Boutique (€)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="prix_original" class="form-control" required placeholder="19.99">
                                <span class="input-group-text"><i class="fa-solid fa-euro-sign"></i></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label text-muted small fw-bold">Prix Promo (€)</label>
                            <input type="number" step="0.01" name="prix_promotion" class="form-control" placeholder="Optionnel">
                        </div>
                        <div class="col-lg-1 col-md-3">
                            <label class="form-label text-muted small fw-bold">Stock init.</label>
                            <input type="number" name="stock_quantite" class="form-control" value="10" required>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-muted small fw-bold">Upload de l'image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/png, image/jpeg, image/webp">
                        </div>
                        <div class="col-lg-1 col-md-3 text-end">
                            <button type="submit" class="btn btn-danger w-100 h-100" title="Ajouter le produit">
                                <i class="fa-solid fa-plus fs-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section 2 : Tableau interactif (Modification & Suppression) -->
            <div class="card">
                <div class="card-body p-0 table-responsive border-0">
                    <table class="table table-dark mb-0 align-middle table-borderless">
                        <thead>
                            <tr class="border-bottom border-dark">
                                <th class="ps-4">Aperçu</th>
                                <th>Informations Produit</th>
                                <th>Prix Normal</th>
                                <th>Prix en Promo</th>
                                <th>Qté Stock</th>
                                <th>Ventes Globales</th>
                                <th class="text-end pe-4">Actions Rapides</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $p): ?>
                                <!-- Formulaires cachés pour l'édition/suppression -->
                                <form id="form-edit-<?= $p['id'] ?>" action="admin_dashboard.php?view=produits" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                </form>
                                <form id="form-delete-<?= $p['id'] ?>" action="admin_dashboard.php?view=produits" method="POST" onsubmit="return confirm('Attention ! Souhaitez-vous vraiment supprimer définitivement cette figurine ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                </form>

                                <tr class="border-bottom border-dark">
                                    <td class="ps-4">
                                        <?php if(!empty($p['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Figurine" class="img-thumbnail-custom">
                                        <?php else: ?>
                                            <div class="img-thumbnail-custom d-flex align-items-center justify-content-center text-muted fs-4">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-6"><?= htmlspecialchars($p['nom'] ?? '') ?></div>
                                        <small class="text-muted d-block">ID: #<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></small>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fa-solid fa-euro-sign"></i></span>
                                            <input type="number" step="0.01" class="form-control fw-bold" name="prix_original" value="<?= htmlspecialchars($p['prix_original'] ?? '') ?>" form="form-edit-<?= $p['id'] ?>">
                                        </div>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fa-solid fa-tag text-warning"></i></span>
                                            <input type="number" step="0.01" class="form-control" name="prix_promotion" value="<?= htmlspecialchars($p['prix_promotion'] ?? '') ?>" form="form-edit-<?= $p['id'] ?>">
                                        </div>
                                    </td>
                                    <td style="min-width: 110px;">
                                        <input type="number" class="form-control form-control-sm text-center fw-bold <?= ($p['stock_quantite'] < 5) ? 'text-danger' : 'text-success' ?>" name="stock_quantite" value="<?= htmlspecialchars($p['stock_quantite'] ?? '0') ?>" form="form-edit-<?= $p['id'] ?>">
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary px-3 py-2 text-light rounded-pill">
                                            <i class="fa-solid fa-chart-line me-1 text-primary"></i> <?= htmlspecialchars($p['ventes'] ?? '0') ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4" style="white-space: nowrap;">
                                        <button type="submit" form="form-edit-<?= $p['id'] ?>" class="btn btn-outline-success btn-sm me-1 rounded-3" title="Sauvegarder les modifications">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                        </button>
                                        <button type="submit" form="form-delete-<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm rounded-3" title="Supprimer la figurine">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-box-open fs-1 mb-3 d-block opacity-50"></i>
                                        Votre catalogue est vide. Ajoutez votre première figurine !
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($view === 'commandes'): ?>
            <!-- Tableau de suivi des commandes -->
            <div class="card border-0 overflow-hidden">
                <div class="card-body p-0 table-responsive border-0">
                    <table class="table table-dark mb-0 align-middle table-borderless table-hover">
                        <thead>
                            <tr class="border-bottom border-dark bg-dark">
                                <th class="ps-4">Référence</th>
                                <th>Client</th>
                                <th>Date de commande</th>
                                <th>Montant Total</th>
                                <th class="pe-4">Statut Actuel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $c): ?>
                                <?php 
                                    // Génération du format de commande CMD-YYYY-XXXX
                                    $annee = date('Y', strtotime($c['date_commande'] ?? date('Y-m-d')));
                                    $num_cmd = 'CMD-' . $annee . '-' . str_pad($c['id'] ?? 0, 4, '0', STR_PAD_LEFT);
                                    
                                    // Détermination du nom client
                                    $nom_client = !empty($c['nom']) ? $c['nom'] : (!empty($c['nom_client']) ? $c['nom_client'] : (!empty($c['email']) ? $c['email'] : 'Utilisateur ID #'.$c['user_id']));
                                    
                                    // Détermination du montant
                                    $montant_brut = (float) ($c['montant_total'] ?? $c['total'] ?? 0);
                                    $montant = $montant_brut > 0 ? number_format($montant_brut, 2, ',', ' ') . ' €' : '<span class="text-muted">A calculer</span>';
                                    
                                    // Gestion visuelle du statut
                                    $statut = strtolower($c['statut'] ?? 'en attente');
                                    $badgeClass = 'bg-secondary text-light';
                                    $iconClass = 'fa-clock';
                                    
                                    if ($statut === 'livrée' || $statut === 'expédiée' || $statut === 'validée' || strpos($statut, 'termin') !== false) {
                                        $badgeClass = 'bg-success text-white';
                                        $iconClass = 'fa-check-circle';
                                    } elseif ($statut === 'en cours' || $statut === 'en attente') {
                                        $badgeClass = 'bg-warning text-dark';
                                        $iconClass = 'fa-spinner fa-spin';
                                    } elseif ($statut === 'annulée') {
                                        $badgeClass = 'bg-danger text-white';
                                        $iconClass = 'fa-times-circle';
                                    }
                                ?>
                                <tr class="border-bottom border-dark">
                                    <td class="ps-4">
                                        <div class="fw-bold font-monospace text-light"><i class="fa-solid fa-barcode text-muted me-2"></i><?= $num_cmd ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-dark rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 35px; height: 35px;">
                                                <i class="fa-solid fa-user text-muted fs-6"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold"><?= htmlspecialchars($nom_client) ?></span>
                                                <small class="d-block text-muted"><?= htmlspecialchars($c['email'] ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fa-regular fa-calendar me-2 text-muted"></i>
                                        <?= date('d/m/Y à H:i', strtotime($c['date_commande'] ?? 'now')) ?>
                                    </td>
                                    <td class="fs-5 fw-bold text-success">
                                        <?= $montant ?>
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill fs-6 fw-normal shadow-sm">
                                            <i class="fa-solid <?= $iconClass ?> me-1"></i> <?= ucfirst(htmlspecialchars($statut)) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-receipt fs-1 mb-3 d-block opacity-50"></i>
                                        Historique des ventes vide pour le moment.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($view === 'utilisateurs'): ?>
            <div class="card border-0">
                <div class="card-body p-0 table-responsive">
                    <table class="table table-dark mb-0 align-middle table-hover text-center text-md-start">
                        <thead>
                            <tr class="border-bottom border-dark">
                                <th class="ps-4">ID User</th>
                                <th>Identité du client</th>
                                <th>Email Contact</th>
                                <th>Privilèges</th>
                                <th class="pe-4 text-end">Membre depuis le</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $u): ?>
                                <tr class="border-bottom border-dark">
                                    <td class="ps-4 text-muted font-monospace fw-bold">#<?= str_pad($u['id'] ?? 0, 3, '0', STR_PAD_LEFT) ?></td>
                                    <td class="fw-bold text-light">
                                        <?= htmlspecialchars(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? 'Anonyme')) ?>
                                    </td>
                                    <td><a href="mailto:<?= htmlspecialchars($u['email'] ?? '') ?>" class="text-info text-decoration-none"><?= htmlspecialchars($u['email'] ?? '') ?></a></td>
                                    <td>
                                        <?php if(($u['role'] ?? '') === 'admin'): ?>
                                            <span class="badge bg-danger shadow-sm px-3 py-2 text-uppercase"><i class="fa-solid fa-crown me-2"></i>Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary shadow-sm px-3 py-2"><i class="fa-solid fa-user me-2"></i>Client</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end text-muted">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= isset($u['date_inscription']) ? date('d/m/Y', strtotime($u['date_inscription'])) : 'Date Inconnue' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-users-slash fs-1 mb-3 d-block opacity-50"></i>
                                        Aucun compte utilisateur enregistré.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap 5 Bundle JS (inclut Popper.js pour les tooltips, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
