<?php
$connection = mysqli_connect("localhost", "root", "");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$sql = "CREATE DATABASE rpiBD";
if (mysqli_query($connection, $sql)) {
    echo "Base de données rpiBD créée avec succès.";
} else {
    echo "Erreur : " . mysqli_error($connection);
}

mysqli_close($connection);
?>
