<?php
$connection = mysqli_connect("localhost", "root", "", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$createTable = "
CREATE TABLE IF NOT EXISTS inventaire (
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
) ENGINE=InnoDB;
";

if (!mysqli_query($connection, $createTable)) {
    die("Erreur création table : " . mysqli_error($connection));
}

$csvFile = "inventory_devices.csv";

if (!file_exists($csvFile)) {
    die("Fichier CSV introuvable.");
}

$handle = fopen($csvFile, "r");

if ($handle === FALSE) {
    die("Impossible d'ouvrir le fichier CSV.");
}

$headers = fgetcsv($handle, 1000, ";");

while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {


    $data = array_map(function($value) use ($connection) {
        return mysqli_real_escape_string($connection, $value);
    }, $data);

    $sql = "
        INSERT INTO inventaire 
        (NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END)
        VALUES 
        (
            '$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]',
            '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]',
            '$data[10]', '$data[11]', '$data[12]', '$data[13]', '$data[14]',
            '$data[15]', '$data[16]'
        );
    ";

    if (!mysqli_query($connection, $sql)) {
        echo "Erreur insertion : " . mysqli_error($connection) . "<br>";
    }
}

fclose($handle);
echo "Importation terminée avec succès !";

mysqli_close($connection);
?>
