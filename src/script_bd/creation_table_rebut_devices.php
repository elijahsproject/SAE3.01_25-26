<?php

$connection = mysqli_connect("localhost", "root", "", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$createTable = "
CREATE TABLE IF NOT EXISTS rebut_devices (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NAME VARCHAR(50),
    SERIAL VARCHAR(50),
    MANUFACTURER VARCHAR(50),
    MODEL VARCHAR(100),
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
);
";

if (mysqli_query($connection, $createTable)) {
    echo "Table rebut_moniteur créée avec succès.";
} else {
    echo "Erreur création table : " . mysqli_error($connection);
}

mysqli_close($connection);

?>