<?php
require_once __DIR__ . '/config.php';

$message = '';

// Debug : décommenter pour vérifier la réception des données
// if ($_SERVER['REQUEST_METHOD'] === 'POST') { var_dump($_POST); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $message = "Veuillez remplir tous les champs.";
    } else {
        try {
            $stmt = $conn->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->rowCount() > 0) {
                $message = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);
                header('Location: login.php?inscrit=1');
                exit;
            }
        } catch (PDOException $e) {
            $message = 'Erreur base de données : ' . htmlspecialchars($e->getMessage());
        }
    }
}
$current_page = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription | Figurine Store</title>
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
            <h2 class="mb-4">Inscription</h2>
            <?php if ($message !== '') echo '<div class="alert alert-info">' . htmlspecialchars($message) . '</div>'; ?>
            <form action="inscription.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>
            <p class="mt-3 text-center mb-0">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
