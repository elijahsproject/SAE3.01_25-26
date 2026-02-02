<?php
$connection = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// Création de la table
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

$sql_alter = "ALTER TABLE user ADD role ENUM('technicien', 'sysadmin', 'adminweb') NOT NULL";

if (mysqli_query($connection, $sql_alter)) {
    echo "Colonne 'role' ajoutée avec succès.<br>";
} else {
    echo "Erreur lors de l'ajout de la colonne : " . mysqli_error($connection) . "<br>";
}

// Insertion des données
$sql_insert = "INSERT INTO user (login, password, role) VALUES
    ('tech1', '*tech1*', 'technicien'),
    ('sysadmin', 'sysadmin', 'sysadmin'),
    ('adminweb', 'adminweb', 'adminweb')";

if (mysqli_query($connection, $sql_insert)) {
    echo "Données insérées avec succès.<br>";
} else {
    echo "Erreur lors de l'insertion des données : " . mysqli_error($connection) . "<br>";
}

mysqli_close($connection);
?>
