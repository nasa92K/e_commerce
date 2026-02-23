<?php
/**
 * Script une seule fois : ajoute la colonne avatar à la table users.
 * À exécuter via : http://localhost/E-commerceFigurine/install_avatar_column.php
 * Supprimez ce fichier après utilisation pour des raisons de sécurité.
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

$colonneDejaPresente = false;
try {
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");
    if ($stmt->rowCount() > 0) {
        $colonneDejaPresente = true;
    }
} catch (PDOException $e) {
    echo '<p style="color:red;">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

if ($colonneDejaPresente) {
    echo '<p style="color:green;">La colonne <strong>avatar</strong> existe déjà dans la table <strong>users</strong>. Aucune modification effectuée.</p>';
    echo '<p><a href="profile.php">Aller au profil</a></p>';
    exit;
}

try {
    $conn->exec('ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL');
    echo '<p style="color:green;">La colonne <strong>avatar</strong> a été ajoutée à la table <strong>users</strong>.</p>';
    echo '<p>Vous pouvez maintenant utiliser la photo de profil sur <a href="profile.php">profile.php</a>.</p>';
    echo '<p style="color:#666;">Recommandation : supprimez ce fichier (install_avatar_column.php) pour des raisons de sécurité.</p>';
} catch (PDOException $e) {
    echo '<p style="color:red;">Erreur lors de l\'ajout de la colonne : ' . htmlspecialchars($e->getMessage()) . '</p>';
}
