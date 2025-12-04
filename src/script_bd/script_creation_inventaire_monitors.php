<?php
$connection = mysqli_connect("localhost", "root", "", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$createTable = "
CREATE TABLE IF NOT EXISTS moniteur (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    SERIAL VARCHAR(20) UNIQUE,
    MANUFACTURER VARCHAR(50),
    MODEL VARCHAR(50),
    SIZE_INCH INT,
    RESOLUTION VARCHAR(20),
    CONNECTOR VARCHAR(20),
    ATTACHED_TO VARCHAR(50)
);
";

if (!mysqli_query($connection, $createTable)) {
    die('Erreur création table : ' . mysqli_error($connection));
}

echo "Table moniteur prête.<br>";

$csvFile = "inventory_monitors2.csv";

if (!file_exists($csvFile)) {
    die("Fichier CSV introuvable : $csvFile");
}

if (($handle = fopen($csvFile, "r")) !== false) {

    $header = fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {

        $serial       = mysqli_real_escape_string($connection, $data[0]);
        $manufacturer = mysqli_real_escape_string($connection, $data[1]);
        $model        = mysqli_real_escape_string($connection, $data[2]);
        $size_inch    = intval($data[3]);
        $resolution   = mysqli_real_escape_string($connection, $data[4]);
        $connector    = mysqli_real_escape_string($connection, $data[5]);
        $attached_to  = mysqli_real_escape_string($connection, $data[6]);

        $sql = "
            INSERT INTO moniteur (SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO)
            VALUES ('$serial', '$manufacturer', '$model', $size_inch, '$resolution', '$connector', '$attached_to')
            ON DUPLICATE KEY UPDATE
                MANUFACTURER = VALUES(MANUFACTURER),
                MODEL = VALUES(MODEL),
                SIZE_INCH = VALUES(SIZE_INCH),
                RESOLUTION = VALUES(RESOLUTION),
                CONNECTOR = VALUES(CONNECTOR),
                ATTACHED_TO = VALUES(ATTACHED_TO);
        ";

        if (!mysqli_query($connection, $sql)) {
            echo "Erreur insertion ($serial) : " . mysqli_error($connection) . "<br>";
        }
    }

    fclose($handle);
}

echo "<br>Importation terminée.";

mysqli_close($connection);

?>
