<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rpiBD";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die($conn->connect_error);

$conn->query("CREATE TABLE IF NOT EXISTS inventory_monitors2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    col1 VARCHAR(255),
    col2 VARCHAR(255),
    col3 VARCHAR(255),
    col4 VARCHAR(255),
    col5 VARCHAR(255)
)");

$f = __DIR__ . "/inventory_monitors2.csv";
$h = fopen($f, "r");
$header = fgetcsv($h, 1000, ",");

while (($d = fgetcsv($h, 1000, ",")) !== FALSE) {
    for ($i=0;$i<count($d);$i++) $d[$i] = $conn->real_escape_string($d[$i]);
    $conn->query("INSERT INTO inventory_monitors2 (col1,col2,col3,col4,col5)
                  VALUES ('{$d[0]}','{$d[1]}','{$d[2]}','{$d[3]}','{$d[4]}')");
}

fclose($h);
$conn->close();
?>
