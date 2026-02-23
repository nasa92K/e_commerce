-- Exécuter cette requête sur la base où se trouve la table users (bdd_cpx ou figurine_db)
-- pour activer la photo de profil sur profile.php

ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;
