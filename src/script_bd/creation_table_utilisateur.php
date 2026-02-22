<?php


require_once "../pages/chacha20.php";


$connection = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connection) {
    die("Connexion échouée : " . mysqli_connect_error());
}


$sql_table = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role ENUM('technicien', 'sysadmin', 'adminweb') NOT NULL
)";

if (mysqli_query($connection, $sql_table)) {
    echo "Table 'user' prête.<br>";
} else {
    die("Erreur création table : " . mysqli_error($connection));
}


$sql_insert = "INSERT INTO user (login, password, role) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($connection, $sql_insert);

if (!$stmt) {
    die("Erreur préparation requête : " . mysqli_error($connection));
}

$utilisateurs = [
    ["tech1", "*tech1*", "technicien"],
    ["sysadmin", "sysadmin", "sysadmin"],
    ["adminweb", "adminweb", "adminweb"]
];

foreach ($utilisateurs as $u) {

    $login = $u[0];
    $motDePasseClair = $u[1];
    $role = $u[2];

    $motDePasseChiffre = chiffrer_chacha20($motDePasseClair);

    mysqli_stmt_bind_param($stmt, "sss", $login, $motDePasseChiffre, $role);
    mysqli_stmt_execute($stmt);

    echo "Utilisateur $login inséré avec mot de passe chiffré.<br>";
}

mysqli_stmt_close($stmt);
mysqli_close($connection);

echo "<br>Initialisation terminée.";
?>