<?php
$host = "localhost";  // Hôte (généralement localhost)
$dbname = "figurine_db"; // Même base que le reste du site (table users dans users.sql)
$username = "anas"; // Nom d'utilisateur MySQL (par défaut root)
$password = "anasdevelopper"; // Mot de passe MySQL (laisser vide sur XAMPP)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}
?>
