<?php
session_start();

$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

$user_avatar = $base_assets . 'assets/img/default-avatar.png';
if (!file_exists(__DIR__ . '/assets/img/default-avatar.png')) {
    $user_avatar = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}

if (isset($_SESSION['user_id'])) {
    if (!empty($_SESSION['user_avatar']) && is_string($_SESSION['user_avatar']) && file_exists(__DIR__ . '/' . $_SESSION['user_avatar'])) {
        $user_avatar = $base_assets . $_SESSION['user_avatar'];
    } else {
        try {
            $host = "localhost";
            $db   = "figurine_db";
            $user = "root";
            $pass = "";
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT username, avatar, photo_profil FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($currentUser) {
                $_SESSION['username'] = $currentUser['username'];
                $avatarPath = $currentUser['avatar'] ?? $currentUser['photo_profil'] ?? '';
                if ($avatarPath !== '' && file_exists(__DIR__ . '/' . $avatarPath)) {
                    $user_avatar = $base_assets . $avatarPath;
                    $_SESSION['user_avatar'] = $avatarPath;
                } else {
                    $_SESSION['user_avatar'] = '';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['user_avatar'] = '';
        }
    }
}

$canOrder = isset($_SESSION['cart']) && !empty($_SESSION['cart']);
if (!isset($pdo)) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=figurine_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $pdo = null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Figurine Store | Accueil</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <style>
        /* Section Univers Figurines - fond distinct */
        .section-univers { background: linear-gradient(180deg, #e8e8e8 0%, #dedede 100%); padding: 3.5rem 0 3rem; position: relative; }
        .section-univers::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); opacity: 0.6; pointer-events: none; }
        .section-univers .section-title { font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 800; color: #1a1a1a; text-align: center; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.02em; }
        .section-univers .section-subtitle { text-align: center; color: #5c5c5c; margin-bottom: 2.5rem; font-size: 1rem; }
        .card-univers { background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; overflow: hidden; height: 100%; transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; }
        .card-univers:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,0,0,0.12), 0 0 0 1px rgba(211,47,47,0.15); }
        .card-univers.card-vedette .card-univers-img-wrap { aspect-ratio: 1; }
        .card-univers-img-wrap { position: relative; overflow: hidden; aspect-ratio: 1; background: #f0f0f0; }
        .card-univers-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .card-univers:hover .card-univers-img-wrap img { transform: scale(1.06); }
        .card-univers .card-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .card-univers .card-title-univers { font-size: 0.95rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.35rem; line-height: 1.25; }
        .card-univers .card-title-univers a { color: inherit; text-decoration: none; }
        .card-univers .card-title-univers a:hover { color: #D32F2F; }
        .badge-theme { font-size: 0.65rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem; display: inline-block; }
        .badge-anime { background: linear-gradient(135deg, #E91E63, #9C27B0); color: #fff; }
        .badge-scifi { background: linear-gradient(135deg, #2196F3, #03A9F4); color: #fff; }
        .badge-fantasy { background: linear-gradient(135deg, #4CAF50, #8BC34A); color: #fff; }
        .badge-superheros { background: linear-gradient(135deg, #FF5722, #FF9800); color: #fff; }
        .badge-horreur { background: linear-gradient(135deg, #37474F, #546E7A); color: #fff; }
        .badge-sport { background: linear-gradient(135deg, #00BCD4, #0097A7); color: #fff; }
        .badge-boss { background: linear-gradient(135deg, #D4AF37, #F4E4A6); color: #1a1a1a; }
        .badge-action { background: linear-gradient(135deg, #8B0000, #B22222); color: #fff; }
        .badge-rpg { background: linear-gradient(135deg, #228B22, #32CD32); color: #fff; }
        .badge-cyber { background: linear-gradient(135deg, #00CED1, #00BFFF); color: #0a0a0a; }
        .badge-combat { background: linear-gradient(135deg, #1E90FF, #4169E1); color: #fff; }
        .badge-darkfantasy { background: linear-gradient(135deg, #1a1a1a, #2d2d2d); color: #e0e0e0; }
        .card-univers .price-univers { font-size: 1rem; font-weight: 700; color: #D32F2F; margin-bottom: 0.5rem; }
        .card-univers .btn-univers { background: #D32F2F; color: #fff; border: none; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.9rem; border-radius: 6px; text-decoration: none; display: inline-block; transition: background 0.2s, transform 0.2s; margin-top: auto; }
        .card-univers .btn-univers:hover { background: #B71C1C; color: #fff; transform: scale(1.02); }
        .cta-collection { display: inline-block; background: #D32F2F; color: #fff; font-weight: 700; font-size: 1.1rem; padding: 0.85rem 2.5rem; border-radius: 8px; text-decoration: none; transition: background 0.3s, transform 0.3s, box-shadow 0.3s; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.5rem; }
        .cta-collection:hover { background: #B71C1C; color: #fff; transform: translateY(-3px); box-shadow: 0 8px 24px rgba(211,47,47,0.4); }
    </style>
</head>
<body class="body-home">

<div id="preloader">
    <div class="jumper"><div></div><div></div><div></div></div>
</div>

<header>
    <nav class="navbar navbar-expand-lg navbar-light home-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2>Figurine <span>Store</span></h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Plus</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about-us.php">À propos</a></li>
                            <li><a class="dropdown-item" href="avis.php">Avis</a></li>
                            <li><a class="dropdown-item" href="terms.php">Infos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="checkout.php">Commande</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION["user_id"])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_SESSION['email']) && $_SESSION['email'] === 'anasfigurine@gmail.com'): ?>
                        <li class="nav-item me-2 d-flex align-items-center">
                            <a class="nav-link text-danger fw-bold border border-danger rounded px-2 py-1" href="admin_dashboard.php"><i class="fa fa-user-shield me-1"></i>Admin</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a href="profile.php" class="user-profile-box" title="Mon Profil">
                                <img src="<?php echo htmlspecialchars($user_avatar); ?>?t=<?php echo time(); ?>" class="profile-avatar rounded-circle" alt="Avatar" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #D32F2F;">
                                <span class="profile-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="Deconnexion.php" class="btn-logout" title="Déconnexion">
                                <i class="fa fa-power-off"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link btn-inscription" href="inscription.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Hero Banner : dégradé noir → rouge translucide -->
<section class="hero-figurine">
    <div class="hero-content">
        <p class="hero-subtitle">Trouvez ce qui vous convient</p>
        <h1 class="hero-title">Vente de figurines</h1>
        <p class="hero-desc">Large choix de figurines, différentes catégories et quantités limitées. Livraison rapide.</p>
        <a href="products.php" class="btn btn-cta">Voir les produits</a>
    </div>
</section>

<!-- Figurines en promotion -->
<section class="latest-products py-5">
    <div class="container">
        <div class="section-heading-home">
            <h2>Figurines en promotion</h2>
            <a href="products.php">Voir plus <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-figurine h-100">
                    <div class="card-img-wrap">
                        <span class="badge badge-figurine badge-promo position-absolute top-0 start-0 m-2">Promo</span>
                        <img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" class="card-img-top" alt="Figurine Luffy">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><a href="products.php">Figurine Luffy</a></h3>
                        <div class="card-price">
                            <small>€199.00</small> €169.99
                        </div>
                        <p class="card-text">Figurine Luffy, personnage du manga One Piece — édition limitée.</p>
                        <a href="products.php" class="btn btn-cta btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-figurine h-100">
                    <div class="card-img-wrap">
                        <span class="badge badge-figurine badge-limited position-absolute top-0 start-0 m-2">Limitée</span>
                        <img src="imagefigurine/Naruto.jpg" class="card-img-top" alt="Figurine Naruto">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><a href="products.php">Figurine Naruto</a></h3>
                        <div class="card-price">
                            <small>€99.00</small> €79.00
                        </div>
                        <p class="card-text">Figurine du personnage Naruto, manga éponyme — qualité premium.</p>
                        <a href="products.php" class="btn btn-cta btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="products.php" class="text-decoration-none text-dark d-block h-100" style="cursor:pointer;">
                    <div class="card card-figurine h-100">
                        <div class="card-img-wrap">
                            <span class="badge badge-figurine badge-stock position-absolute top-0 start-0 m-2">En stock</span>
                            <img src="imagefigurine/efe1d1166698125edc87d0a964e980fbbf1a23ed.jpg" class="card-img-top" alt="Plus de figurines">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Plus de figurines</h3>
                            <div class="card-price">À partir de €49.99</div>
                            <p class="card-text">Découvrez toute la collection sur la page Produits.</p>
                            <span class="btn btn-cta btn-sm">Voir tout</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php if ($canOrder): ?>
        <div class="checkout-cta-home">
            <a href="checkout.php" class="btn btn-cta">Aller à la page Commande</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Découvrez notre Univers de Figurines -->
<section class="section-univers">
    <div class="container">
        <h2 class="section-title">La Collection Ultime</h2>
        <p class="section-subtitle">Découvrez notre univers de figurines — Anime, Sci-Fi, Fantasy, Super-Héros et plus.</p>
        <div class="row g-3 g-lg-4">
            <!-- Carte vedette (plus grande) -->
            <div class="col-sm-6 col-lg-4">
                <div class="card card-univers card-vedette h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" alt="Luffy Gear 5">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-anime">Anime</span>
                        <h3 class="card-title-univers"><a href="products.php">Luffy Gear 5</a></h3>
                        <div class="price-univers">€169.99</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/Naruto.jpg" alt="Naruto">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-anime">Anime</span>
                        <h3 class="card-title-univers"><a href="products.php">Naruto</a></h3>
                        <div class="price-univers">€79.00</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="<?php echo htmlspecialchars($base_assets); ?>imagefigurine/61Bwj6ERsoL.jpg" alt="Ghost Face">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-horreur">Horreur</span>
                        <h3 class="card-title-univers"><a href="product-details-ghostface.php">Ghost Face</a></h3>
                        <div class="price-univers">€14.99</div>
                        <a href="product-details-ghostface.php" class="btn-univers">Détails</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPsquidgame.jpg" alt="Squid Game">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-scifi">Sci-Fi</span>
                        <h3 class="card-title-univers"><a href="product-details-squidgame.php">Squid Game</a></h3>
                        <div class="price-univers">€19.99</div>
                        <a href="product-details-squidgame.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPwednesday.jpg" alt="Mercredi Addams">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-fantasy">Fantasy</span>
                        <h3 class="card-title-univers"><a href="product-details-mercrediaddams.php">Mercredi Addams</a></h3>
                        <div class="price-univers">€14.99</div>
                        <a href="product-details-mercrediaddams.php" class="btn-univers">Détails</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPlebronjames.jpg" alt="LeBron James">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-sport">Sport</span>
                        <h3 class="card-title-univers"><a href="product-details-lebronjames.php">LeBron James</a></h3>
                        <div class="price-univers">€14.99</div>
                        <a href="product-details-lebronjames.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPhaerin.jpg" alt="Haerin">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-anime">Anime</span>
                        <h3 class="card-title-univers"><a href="product-details-haerin.php">Haerin NewJeans</a></h3>
                        <div class="price-univers">€19.99</div>
                        <a href="product-details-haerin.php" class="btn-univers">Détails</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPleonkennedy.jpg" alt="Leon Kennedy">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-scifi">Sci-Fi</span>
                        <h3 class="card-title-univers"><a href="product-details-leonkennedy.php">Leon Kennedy</a></h3>
                        <div class="price-univers">€14.99</div>
                        <a href="product-details-leonkennedy.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/malenia.jpg" alt="Malenia - Elden Ring">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-boss">Boss / Or</span>
                        <h3 class="card-title-univers"><a href="products.php">Elden Ring : Malenia</a></h3>
                        <div class="price-univers">€189.99</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/worldbox-onetoys-announces-kratos-god-of-war-ragnarok-figure-v0-j76h3zspyvy91.webp" alt="Kratos - God of War Ragnarök">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-action">Action / Rouge Sang</span>
                        <h3 class="card-title-univers"><a href="products.php">Kratos (God of War Ragnarök)</a></h3>
                        <div class="price-univers">€159.00</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/link.jpg" alt="Link - Tears of the Kingdom">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-rpg">RPG / Vert</span>
                        <h3 class="card-title-univers"><a href="products.php">Link (Tears of the Kingdom)</a></h3>
                        <div class="price-univers">€129.99</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/Exclu1.webp" alt="Johnny Silverhand - Cyberpunk 2077">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-cyber">Cyber / Cyan</span>
                        <h3 class="card-title-univers"><a href="products.php">Cyberpunk 2077 : Johnny Silverhand</a></h3>
                        <div class="price-univers">€139.00</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/product_figures_dragon-ball_blood-of-saiyans_goku_ultra-instinct.webp" alt="Goku Ultra Instinct - Dragon Ball Super">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-combat">Combat / Bleu</span>
                        <h3 class="card-title-univers"><a href="products.php">Goku Ultra Instinct</a></h3>
                        <div class="price-univers">€99.99</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/guts.jpg" alt="Guts - Berserker Armor">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-darkfantasy">Dark Fantasy / Noir</span>
                        <h3 class="card-title-univers"><a href="products.php">Guts (Berserker Armor)</a></h3>
                        <div class="price-univers">€179.00</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/POPhaerin.jpg" alt="K-Pop">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-anime">Anime</span>
                        <h3 class="card-title-univers"><a href="products.php">K-Pop Star</a></h3>
                        <div class="price-univers">€29.99</div>
                        <a href="products.php" class="btn-univers">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-univers h-100">
                    <div class="card-univers-img-wrap">
                        <img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" alt="Collector">
                    </div>
                    <div class="card-body">
                        <span class="badge-theme badge-superheros">Super-Héros</span>
                        <h3 class="card-title-univers"><a href="products.php">Collector Rare</a></h3>
                        <div class="price-univers">€129.00</div>
                        <a href="products.php" class="btn-univers">Détails</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 pt-2">
            <a href="products.php" class="cta-collection">Voir toute la collection</a>
        </div>
    </div>
</section>

<footer class="footer-home">
    <div class="container">
        <div class="inner-content">
            <p>Contact © 2025 Figurine Store — Design by Anas Fekhar: <a href="#">E-commerceFigurine.com</a></p>
        </div>
    </div>
</footer>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
