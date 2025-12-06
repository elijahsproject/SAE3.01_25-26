<?php
$connection = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$createTable = "CREATE TABLE IF NOT EXISTS moniteur (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    SERIAL VARCHAR(20) UNIQUE,
    MANUFACTURER VARCHAR(50),
    MODEL VARCHAR(100),   
    SIZE_INCH INT,
    RESOLUTION VARCHAR(20),
    CONNECTOR VARCHAR(20),
    ATTACHED_TO VARCHAR(100),
    CONSTRAINT fk_attached FOREIGN KEY (ATTACHED_TO) REFERENCES inventaire(MODEL)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);";

if (!mysqli_query($connection, $createTable)) {
    die('Erreur création table : ' . mysqli_error($connection));
}

echo "Table moniteur créée avec succès.";

mysqli_close($connection);
?>
