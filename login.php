<?php
session_start();

// 1. Connexion à la base de données
$host = "localhost";
$db   = "figurine_db";
$user = "anas";
$pass = "anasdevelopper";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$message = "";

if (isset($_GET['inscrit']) && (int)$_GET['inscrit'] === 1) {
    $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // 2. Recherche de l'utilisateur
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 3. Vérification du mot de passe
        // Note: password_verify fonctionne avec password_hash()
        // Si le mdp stocké est en clair, on peut tester une comparaison simple en fallback temporaire
        $isValid = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $isValid = true;
            } elseif ($user['password'] === $password) {
                // FALLBACK : le mot de passe est en clair, mettons-le à jour avec un hash !
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$newHash, $user['id']]);
                $isValid = true;
            }
        }

        if ($isValid) {
            // CONNEXION RÉUSSIE
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = ($user['email'] === 'anasfigurine@gmail.com') ? 'admin' : 'client';
            
            // Attribution de la photo de profil, avec une image par défaut
            $photo = $user['photo_profil'] ?? $user['avatar'] ?? null;
            $_SESSION['photo_profil'] = $photo ?: 'imagefigurine/Portrait Pro - Bureau.png';
            
            // Compatibilité avec l'ancien code :
            $_SESSION['user_avatar'] = $_SESSION['photo_profil'];
            
            header("Location: index.php");
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
$current_page = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .form-container { max-width: 400px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 2px solid #D32F2F; color: #1a1a1a; }
        .form-container h2 { color: #1a1a1a !important; }
        .form-container .form-label { color: #333 !important; }
        .form-container p { color: #444 !important; }
        .form-container .btn-primary { background: #D32F2F; border-color: #D32F2F; color: #fff; }
        .form-container .btn-primary:hover { background: #B71C1C; border-color: #B71C1C; color: #fff; }
        .form-container a { color: #D32F2F; font-weight: 600; }
        .form-container .form-control { background: #fff; color: #1a1a1a; border-color: #ced4da; }
        .form-container .form-control:focus { border-color: #D32F2F; color: #1a1a1a; }
        .form-container .alert { color: inherit; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>
    <div class="container theme-main py-5" style="padding-top: 6rem !important;">
        <div class="form-container">
            <h2 class="mb-4">Connexion</h2>
            <?php if ($message !== '') echo '<div class="alert ' . (isset($_GET['inscrit']) ? 'alert-success' : 'alert-danger') . '">' . htmlspecialchars($message) . '</div>'; ?>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
            <p class="mt-3 text-center mb-0">Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous ici</a></p>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
