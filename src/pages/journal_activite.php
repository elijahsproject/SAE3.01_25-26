<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: accueil.php");
    exit;
}

if ($_SESSION['role'] !== 'sysadmin') {
    header("Location: accueil.php");
    exit;
}


$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if (!$connecte) {
    die("Erreur connexion DB");
}

$sql = "SELECT * FROM logs_connexions ORDER BY id DESC";
$result = mysqli_query($connecte, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal d'activité</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">

    <?php include("../pages/header.php"); ?>
    <div class="main-container">
        <?php include("navbar.php"); ?>

        <div class="contenu">

            <h1>Journal d'activité</h1>

            <table border="1" cellpadding="6" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Date Connexion</th>
                    <th>Date Déconnexion</th>
                    <th>Durée (sec)</th>
                    <th>Statut</th>
                </tr>

                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($ligne = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$ligne['id']."</td>";
                        echo "<td>".$ligne['login']."</td>";
                        echo "<td>".$ligne['ip']."</td>";
                        echo "<td>".$ligne['user_agent']."</td>";
                        echo "<td>".$ligne['date_connexion']."</td>";
                        echo "<td>".$ligne['date_deconnexion']."</td>";
                        echo "<td>".$ligne['duree_sec']."</td>";
                        echo "<td>".$ligne['statut']."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Aucune activité enregistrée</td></tr>";
                }
                ?>
            </table>

        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>
</body>
</html>
