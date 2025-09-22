<?php
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Vérification si l'email existe déjà
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([":email" => $email]);
        if ($stmt->rowCount() > 0) {
            $message = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);
                $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } catch (PDOException $e) {
                $message = "Erreur : " . $e->getMessage();
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
        }
        /* Ajout du style pour la barre de navigation */
        .navbar {
            background-color: #343a40; /* Couleur de fond */
        }
        .navbar .nav-link {
            color: #fff !important; /* Couleur du texte des liens */
        }
        .navbar .nav-link:hover {
            color: #d3d3d3 !important; /* Couleur du texte des liens au survol */
        }
        .navbar-brand {
            color: #fff !important; /* Couleur du texte de la marque */
        }
        .navbar-brand:hover {
            color: #d3d3d3 !important; /* Couleur du texte de la marque au survol */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php"><h2>Boutique de figurine <em>Website</em></h2></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="about-us.php">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Inscription</h2>
            <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>
            <form action="inscription.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            </form>
            <p class="mt-3 text-center">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>

    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="inner-content">
              <p>Contact © 2025 Company Name - Website by Anas Fekhar: <a href="https://manga-story.fr/maintenance/">E-commerceFigurine.com</a></p>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
