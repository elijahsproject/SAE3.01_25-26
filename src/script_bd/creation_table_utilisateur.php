<?php
$connection = mysqli_connect("localhost", "root", "", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}


$sql_table = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if (mysqli_query($connection, $sql_table)) {
    echo "Table 'user' créée avec succès.<br>";
} else {
    echo "Erreur lors de la création de la table : " . mysqli_error($connection) . "<br>";
}


$sql_insert = "INSERT INTO user (login, password) VALUES
    ('tech1', '*tech1*'),
    ('sysadmin', 'sysadmin'),
    ('adminweb', 'adminweb')";

if (mysqli_query($connection, $sql_insert)) {
    echo "Données insérées avec succès.<br>";
} else {
    echo "Erreur lors de l'insertion des données : " . mysqli_error($connection) . "<br>";
}

// Fermeture de la connexion
mysqli_close($connection);
?>
