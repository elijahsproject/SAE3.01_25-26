<?php
$connection = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}


$sql = "
CREATE TABLE IF NOT EXISTS logs_connexions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50),
    ip VARCHAR(50),
    user_agent TEXT,
    date_connexion DATETIME,
    date_deconnexion DATETIME,
    duree_sec INT,
    statut VARCHAR(20)
);
";

if (mysqli_query($connection, $sql)) {
    echo "Table 'logs_connexions' créée ou déjà existante.";
} else {
    echo "Erreur lors de la création de la table : " . mysqli_error($connection);
}

mysqli_close($connection);
?>
