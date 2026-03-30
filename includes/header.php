<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

$user_avatar = $base_assets . 'assets/img/default-avatar.png';
if (!file_exists(dirname(__DIR__) . '/assets/img/default-avatar.png')) {
    $user_avatar = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}

if (isset($_SESSION['user_id'])) {
    if (!empty($_SESSION['user_avatar']) && is_string($_SESSION['user_avatar']) && file_exists(dirname(__DIR__) . '/' . $_SESSION['user_avatar'])) {
        $user_avatar = $base_assets . $_SESSION['user_avatar'];
    } else {
        try {
            $host = "localhost";
            $db   = "figurine_db";
            $user = "anas";
            $pass = "anasdevelopper";
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT username, avatar, photo_profil FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($currentUser) {
                $_SESSION['username'] = $currentUser['username'];
                $avatarPath = $currentUser['avatar'] ?? $currentUser['photo_profil'] ?? '';
                if ($avatarPath !== '' && file_exists(dirname(__DIR__) . '/' . $avatarPath)) {
                    $user_avatar = $base_assets . $avatarPath;
                    $_SESSION['user_avatar'] = $avatarPath;
                } else {
                    $_SESSION['user_avatar'] = '';
                }
            }
        } catch (PDOException $e) {}
    }
}
if (!isset($current_page)) $current_page = '';
?>
<div id="preloader">
    <div class="jumper"><div></div><div></div><div></div></div>
</div>
<header>
    <nav class="navbar navbar-expand-lg navbar-light site-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php"><h2>Figurine <span>Store</span></h2></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page === 'products' ? 'active' : ''; ?>" href="products.php">Produits</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Plus</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="about-us.php">À propos</a></li>
                            <li><a class="dropdown-item <?php echo $current_page === 'blog' ? 'active' : ''; ?>" href="blog.php">Blog</a></li>
                            <li><a class="dropdown-item <?php echo $current_page === 'avis' ? 'active' : ''; ?>" href="avis.php">Avis</a></li>
                            <li><a class="dropdown-item <?php echo $current_page === 'terms' ? 'active' : ''; ?>" href="terms.php">Infos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page === 'checkout' ? 'active' : ''; ?>" href="checkout.php">Commande</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
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
                            <a href="Deconnexion.php" class="btn-logout" title="Déconnexion"><i class="fa fa-power-off"></i></a>
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
