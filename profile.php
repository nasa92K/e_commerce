<?php
session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$message = '';
$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

// --- Traitement upload avatar ---
define('AVATAR_MAX_SIZE', 2 * 1024 * 1024); // 2 Mo
$dossierWeb = 'uploads/avatars/';
$dossierSysteme = __DIR__ . '/' . $dossierWeb;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
    if (!is_dir($dossierSysteme)) {
        mkdir($dossierSysteme, 0777, true);
    }

    $file = $_FILES['avatar'];
    $name = isset($file['name']) ? $file['name'] : '';
    $ext = $name ? strtolower(pathinfo($name, PATHINFO_EXTENSION)) : '';
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $uploadOk = false;
    if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && in_array($ext, $allowed) && !empty($file['tmp_name'])) {
        if (isset($file['size']) && $file['size'] > AVATAR_MAX_SIZE) {
            $message = "<div class='alert alert-danger'>Fichier trop volumineux (max 2 Mo).</div>";
        } else {
            $nomFichier = 'user_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $dossierSysteme . $nomFichier)) {
                $cheminRelatif = $dossierWeb . $nomFichier;
                try {
                    $stmt = $conn->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
                    $stmt->execute([
                        ':avatar' => $cheminRelatif,
                        ':id' => $userId
                    ]);
                    $_SESSION['user_avatar'] = $cheminRelatif;
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?succes=1');
                    exit();
                } catch (PDOException $e) {
                    $message = '<div class="alert alert-danger">Erreur BDD : ' . htmlspecialchars($e->getMessage()) . '. Vérifiez que la colonne avatar existe (ALTER TABLE users ADD avatar VARCHAR(255) DEFAULT NULL).</div>';
                }
            } else {
                $message = "<div class='alert alert-danger'>Erreur serveur lors de l'enregistrement du fichier.</div>";
            }
        }
    } else {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $message = "<div class='alert alert-danger'>Aucun fichier reçu ou erreur d'upload.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Format invalide. Autorisés : jpg, jpeg, png, gif, webp (max 2 Mo).</div>";
        }
    }
}

// --- Chargement données utilisateur (même BDD que login) ---
$stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$profileUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!is_array($profileUser) || empty($profileUser)) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit();
}

$defaultAvatarPath = 'assets/img/default-avatar.png';
$avatarPath = (isset($profileUser['avatar']) && $profileUser['avatar'] !== '' && $profileUser['avatar'] !== null)
    ? trim((string) $profileUser['avatar'])
    : '';
if ($avatarPath !== '' && file_exists(__DIR__ . '/' . $avatarPath)) {
    $avatarUrl = $base_assets . $avatarPath;
} elseif (file_exists(__DIR__ . '/' . $defaultAvatarPath)) {
    $avatarUrl = $base_assets . $defaultAvatarPath;
} else {
    $avatarUrl = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}

$current_page = 'profile';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Profil | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        :root {
            --profil-bg: #121212;
            --profil-card: #1a1a1a;
            --profil-border: rgba(255, 255, 255, 0.08);
            --rouge-vif: #FF0000;
            --rouge-sombre: #8B0000;
            --rouge-degrade: linear-gradient(135deg, #FF0000 0%, #8B0000 100%);
        }
        body.profil-passion { background: var(--profil-bg); color: #e0e0e0; min-height: 100vh; }
        .profil-header-block {
            background: var(--profil-card);
            border-bottom: 2px solid var(--profil-border);
            padding: 2.5rem 0 2rem;
            position: relative;
        }
        .profil-avatar-wrap {
            width: 140px;
            height: 140px;
            margin: 0 auto 1rem;
            padding: 4px;
            background: var(--rouge-degrade);
            box-shadow: 0 4px 20px rgba(255, 0, 0, 0.25), 0 0 0 3px rgba(255, 0, 0, 0.2);
            border-radius: 50%;
        }
        .profil-avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 3px solid var(--profil-bg);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        }
        .profil-username {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        .profil-grade {
            background: var(--rouge-degrade);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.9rem;
            border-radius: 50px;
            display: inline-block;
            letter-spacing: 0.1em;
        }
        .profil-sidebar {
            background: var(--profil-card);
            border: 1px solid var(--profil-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .profil-sidebar .nav-link {
            color: #b0b0b0;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            transition: color 0.2s, background 0.2s;
        }
        .profil-sidebar .nav-link:hover,
        .profil-sidebar .nav-link.active { color: #fff; background: rgba(255, 0, 0, 0.15); }
        .profil-sidebar .nav-link i { width: 1.25rem; margin-right: 0.5rem; color: var(--rouge-vif); }
        .profil-card-main {
            background: var(--profil-card);
            border: 1px solid var(--profil-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .profil-card-main h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid transparent;
            border-image: var(--rouge-degrade) 1;
        }
        .profil-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--profil-border);
        }
        .profil-info-row:last-child { border-bottom: none; }
        .profil-info-row .label { color: #888; font-size: 0.9rem; }
        .profil-info-row .value { color: #fff; font-weight: 500; }
        .badge-profil { background: var(--rouge-degrade); color: #fff; font-size: 0.7rem; padding: 0.35rem 0.65rem; border-radius: 50px; }
        .profil-mini-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--profil-border);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .profil-mini-card:hover { border-color: rgba(255, 0, 0, 0.4); box-shadow: 0 4px 20px rgba(255, 0, 0, 0.1); }
        .profil-mini-card img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; }
        .profil-mini-card .title { color: #fff; font-weight: 600; font-size: 0.9rem; margin-top: 0.5rem; }
        .profil-mini-card .badge { margin-top: 0.35rem; }
        .btn-profil { background: var(--rouge-degrade); border: none; color: #fff; font-weight: 600; padding: 0.6rem 1.25rem; border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-profil:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 0, 0, 0.35); }
        .avatar-overlay-profil { position: absolute; inset: 4px; border-radius: 50%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: #fff; opacity: 0; transition: opacity 0.3s; cursor: pointer; font-size: 1.5rem; }
        .profil-avatar-wrap:hover .avatar-overlay-profil { opacity: 1; }
    </style>
</head>
<body class="theme-figurine profil-passion">
<?php include 'includes/header.php'; ?>

    <div class="profil-header-block theme-main" style="padding-top: 5.5rem;">
        <div class="container">
            <form id="avatarForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                <div class="profil-avatar-wrap position-relative d-inline-block">
                    <img src="<?php echo htmlspecialchars($avatarUrl); ?>?t=<?php echo time(); ?>" alt="Avatar" class="rounded-circle border border-danger border-3 shadow">
                    <label for="fileInput" class="avatar-overlay-profil position-absolute top-0 start-0 m-0"><i class="fa fa-camera"></i></label>
                    <input type="file" id="fileInput" name="avatar" class="d-none" onchange="document.getElementById('avatarForm').submit();" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
            </form>
            <h1 class="profil-username"><?php echo htmlspecialchars(isset($profileUser['username']) ? (string) $profileUser['username'] : ''); ?></h1>
            <span class="profil-grade">Collectionneur Élite</span>
            <?php if (isset($_GET['succes'])): ?>
                <div class="alert alert-success mt-3 mb-0">Photo mise à jour !</div>
            <?php endif; ?>
            <?php echo $message; ?>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Sidebar gauche -->
            <aside class="col-lg-4">
                <div class="profil-sidebar">
                    <h6 class="text-uppercase text-muted mb-3 small fw-bold">Mon compte</h6>
                    <div class="profil-info-row">
                        <span class="label"><i class="fa fa-envelope me-2 text-danger"></i>Email</span>
                        <span class="value"><?php echo htmlspecialchars(isset($profileUser['email']) ? (string) $profileUser['email'] : ''); ?></span>
                    </div>
                    <div class="profil-info-row">
                        <span class="label"><i class="fa fa-calendar me-2 text-danger"></i>Membre depuis</span>
                        <span class="value"><?php echo (isset($profileUser['created_at']) && $profileUser['created_at'] !== '' && $profileUser['created_at'] !== null) ? date('d/m/Y', strtotime($profileUser['created_at'])) : '-'; ?></span>
                    </div>
                    <div class="profil-info-row">
                        <span class="label"><i class="fa fa-check-circle me-2 text-danger"></i>Statut</span>
                        <span class="badge-profil">Compte vérifié</span>
                    </div>
                    <hr class="my-3 border-secondary">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#"><i class="fa fa-user"></i> Mon profil</a>
                        <a class="nav-link" href="checkout.php"><i class="fa fa-shopping-bag"></i> Mes commandes</a>
                        <a class="nav-link" href="#"><i class="fa fa-heart"></i> Favoris</a>
                        <a class="nav-link" href="#"><i class="fa fa-cog"></i> Paramètres</a>
                    </nav>
                    <a href="index.php" class="btn btn-profil w-100 mt-3">Retour à la boutique</a>
                </div>
            </aside>

            <!-- Contenu principal droite -->
            <main class="col-lg-8">
                <div class="profil-card-main">
                    <h5>Dernières figurines</h5>
                    <p class="text-muted small mb-3">Vos derniers achats ou articles en wishlist apparaîtront ici.</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <a href="products.php" class="text-decoration-none">
                                <div class="profil-mini-card">
                                    <img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" alt="Figurine">
                                    <div class="title">One Piece</div>
                                    <span class="badge badge-profil">Acheté</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="products.php" class="text-decoration-none">
                                <div class="profil-mini-card">
                                    <img src="imagefigurine/Naruto.jpg" alt="Figurine">
                                    <div class="title">Naruto</div>
                                    <span class="badge badge-profil">Wishlist</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4">
                            <a href="products.php" class="text-decoration-none">
                                <div class="profil-mini-card">
                                    <img src="<?php echo htmlspecialchars($base_assets); ?>imagefigurine/61Bwj6ERsoL.jpg" alt="Figurine">
                                    <div class="title">Ghost Face</div>
                                    <span class="badge badge-profil">En cours</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="products.php" class="btn btn-profil mt-3">Voir le catalogue</a>
                </div>

                <div class="profil-card-main">
                    <h5>Résumé des commandes</h5>
                    <div class="profil-info-row">
                        <span class="label">Commandes passées</span>
                        <span class="value">—</span>
                    </div>
                    <div class="profil-info-row">
                        <span class="label">En cours de livraison</span>
                        <span class="value">—</span>
                    </div>
                </div>
            </main>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
