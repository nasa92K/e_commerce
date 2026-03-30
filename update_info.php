<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Afficher les erreurs pour debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$host = 'localhost';
$dbname = 'figurine_db'; // Mets ici le bon nom de ta BDD
$username_db = 'anas';
$password_db = 'anasdevelopper';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $new_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $user_id = $_SESSION["user_id"];

    if (!$new_email || !$new_username) {
        die("Email ou nom d'utilisateur invalide.");
    }

    // Initialisation de la nouvelle photo de profil
    $profile_pic_path = $_SESSION['profile_pic'] ?? 'uploads/default.jpg';

    // 1. Si une image prédéfinie est sélectionnée
    if (!empty($_POST['preset_pic']) && file_exists($_POST['preset_pic'])) {
        $profile_pic_path = $_POST['preset_pic'];
    }

    // 2. Sinon, si une image est uploadée
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = basename($_FILES['profile_pic']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid('pfp_', true) . "." . $file_ext;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($file_tmp, $destination)) {
                $profile_pic_path = $destination;
            }
        }
    }

    // Mettre à jour les infos dans la base
    try {
        $stmt = $pdo->prepare("UPDATE users SET email = :email, username = :username, profile_pic = :profile_pic WHERE id = :id");
        $stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
        $stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
        $stmt->bindParam(':profile_pic', $profile_pic_path, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();   

        // Mettre à jour la session
        $_SESSION["email"] = $new_email;
        $_SESSION["username"] = $new_username;
        $_SESSION["profile_pic"] = $profile_pic_path;

        // Redirection
        header("Location: profile.php?update=success");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour : " . $e->getMessage());
    }
} else {
    die("Méthode non autorisée.");
}
?>
