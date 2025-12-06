<?php
$connection = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}
$createTable = "CREATE TABLE IF NOT EXISTS inventaire (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NAME VARCHAR(50),
    SERIAL VARCHAR(50),
    MANUFACTURER VARCHAR(50),
    MODEL VARCHAR(100) UNIQUE,  
    TYPE VARCHAR(50),
    CPU VARCHAR(100),
    RAM_MB INT,
    DISK_GB INT,
    OS VARCHAR(100),
    DOMAIN VARCHAR(100),
    LOCATION VARCHAR(100),
    BUILDING VARCHAR(100),
    ROOM VARCHAR(50),
    MACADDR VARCHAR(20),
    PURCHASE_DATE DATE,
    WARRANTY_END DATE
);";

if (mysqli_query($connection, $createTable)) {
    echo "Table inventaire créée avec succès.<br>";
} else {
    echo "Erreur lors de la création de la table : " . mysqli_error($connection);
}

// Fermeture de la connexion
mysqli_close($connection);
?>
