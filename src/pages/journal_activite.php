<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'sysadmin') {
    header("Location: accueil.php");
    exit;
}

// Connexion DB
$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if (!$connecte) {
    die("Erreur connexion DB");
}

// Récup logs DB (connexions réussies)
$sql = "SELECT * FROM logs_connexions ORDER BY id DESC";
$result = mysqli_query($connecte, $sql);

// Chargement JSON
$logs_connexions_json = json_decode(file_get_contents("../logs/logs_connexions.json"), true);
$logs_echecs_json = json_decode(file_get_contents("../logs/logs_echecs.json"), true);
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

            <h1>Journal d'activité (Sysadmin)</h1>

            <!-- ================= CONNEXIONS REUSSIES DB ================= -->
            <h2>Connexions réussies (Base de données)</h2>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>IP</th>
                    <th>Date Connexion</th>
                    <th>Date Déconnexion</th>
                    <th>Durée</th>
                </tr>

                <?php
                while ($ligne = mysqli_fetch_assoc($result)) {
                    echo "<tr>
        <td>{$ligne['id']}</td>
        <td>{$ligne['login']}</td>
        <td>{$ligne['ip']}</td>
        <td>{$ligne['date_connexion']}</td>
        <td>{$ligne['date_deconnexion']}</td>
        <td>{$ligne['duree_sec']} sec</td>
    </tr>";
                }
                ?>
            </table>

            <!-- ================= JSON CONNEXIONS ================= -->
            <h2>Connexions réussies (JSON)</h2>
            <table border="1">
                <tr>
                    <th>Login</th>
                    <th>IP</th>
                    <th>Date</th>
                </tr>

                <?php
                if (!empty($logs_connexions_json)) {
                    foreach ($logs_connexions_json as $log) {
                        echo "<tr>
            <td>{$log['login']}</td>
            <td>{$log['ip']}</td>
            <td>{$log['date_connexion']}</td>
        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucune donnée</td></tr>";
                }
                ?>
            </table>

            <!-- ================= ECHECS ================= -->
            <h2>Tentatives échouées (JSON)</h2>
            <table border="1">
                <tr>
                    <th>Login tenté</th>
                    <th>IP</th>
                    <th>Date</th>
                    <th>Raison</th>
                </tr>

                <?php
                if (!empty($logs_echecs_json)) {
                    foreach ($logs_echecs_json as $log) {
                        echo "<tr>
            <td>{$log['login_tente']}</td>
            <td>{$log['ip']}</td>
            <td>{$log['date_tentative']}</td>
            <td>{$log['raison']}</td>
        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucune tentative échée</td></tr>";
                }
                ?>
            </table>

            <!-- ================= SSH ================= -->
            <h2>Logs SSH (lecture simplifiée)</h2>
            <table border="1">
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>IP</th>
                    <th>Statut</th>
                </tr>

                <?php
                $logFile = "/var/log/auth.log";

                if (file_exists($logFile)) {
                    $lignes = file($logFile);

                    foreach ($lignes as $ligne) {

                        if (strpos($ligne, "sshd") !== false) {

                            // Succès
                            if (strpos($ligne, "Accepted") !== false) {
                                preg_match('/(\w+ \d+ \d+:\d+:\d+).*for (\w+) from ([\d\.]+)/', $ligne, $m);

                                if (!empty($m)) {
                                    echo "<tr>
                        <td>$m[1]</td>
                        <td>$m[2]</td>
                        <td>$m[3]</td>
                        <td style='color:green;'>SUCCES</td>
                    </tr>";
                                }
                            }

                            // Echec
                            if (strpos($ligne, "Failed") !== false) {
                                preg_match('/(\w+ \d+ \d+:\d+:\d+).*user (\w+) from ([\d\.]+)/', $ligne, $m);

                                if (!empty($m)) {
                                    echo "<tr>
                        <td>$m[1]</td>
                        <td>$m[2]</td>
                        <td>$m[3]</td>
                        <td style='color:red;'>ECHEC</td>
                    </tr>";
                                }
                            }
                        }
                    }

                } else {
                    echo "<tr><td colspan='4'>Fichier SSH introuvable</td></tr>";
                }
                ?>
            </table>

        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE</p>
    </footer>

</div>
</body>
</html>