<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rpiBD";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur connexion MySQL : " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(255),
    ip_address VARCHAR(255),
    mac_address VARCHAR(255),
    location VARCHAR(255)
)";
if (!$conn->query($sql)) {
    die("Erreur création table : " . $conn->error);
}
$csvFile = __DIR__ . "/inventory_devices.csv";

if (!file_exists($csvFile)) {
    die("Fichier CSV introuvable.");
}

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $device = $conn->real_escape_string($data[0]);
        $ip     = $conn->real_escape_string($data[1]);
        $mac    = $conn->real_escape_string($data[2]);
        $loc    = $conn->real_escape_string($data[3]);

        $insert = "INSERT INTO inventory (device_name, ip_address, mac_address, location)
                   VALUES ('$device', '$ip', '$mac', '$loc')";

        if (!$conn->query($insert)) {
            echo "Erreur lors de l'insertion : " . $conn->error . "<br>";
        }
    }

    fclose($handle);
    echo "Import terminé avec succès.";
}
else {
    echo "Impossible d'ouvrir le fichier CSV.";
}

$conn->close();
?>
