<?php

$connection = mysqli_connect("localhost", "root", "", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$table_sql = "
CREATE TABLE IF NOT EXISTS rebut_moniteur (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    SERIAL VARCHAR(20) UNIQUE,
    MANUFACTURER VARCHAR(50),
    MODEL VARCHAR(50),
    SIZE_INCH INT,
    RESOLUTION VARCHAR(20),
    CONNECTOR VARCHAR(20),
    ATTACHED_TO VARCHAR(50)
) ENGINE=InnoDB;
";

if (mysqli_query($connection, $table_sql)) {
    echo "Table rebut_moniteur créée avec succès.";
} else {
    echo "Erreur création table : " . mysqli_error($connection);
}

mysqli_close($connection);
?>
