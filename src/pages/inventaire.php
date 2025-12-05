<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Inventaire</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>



<div class="wrapper">
    <?php
    include("../pages/header.php");
    ?>
    <div class="main-container">
        <?php
        include("navbar.php");
        ?>

        <div class="contenu">
            <?php
            $connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

            if (!$connecte) {
                die("Erreur de connexion");
            }

            echo "connecte avec succes";

            // Récupération des colonnes
            $colonnes = mysqli_query($connecte, "SHOW COLUMNS FROM inventaire");

            if (!$colonnes) {
                die("Erreur dans la requête SHOW COLUMNS");
            }

            // Récupération des données
            $data = mysqli_query($connecte, "SELECT * FROM inventaire");

            if (!$data) {
                die("Erreur dans SELECT * ");
            }

            echo "<table border='1' cellpadding='5'>";
            echo "<thead><tr>";

            // Affiche les noms des colonnes
            while ($col = mysqli_fetch_assoc($colonnes)) {
                if ($col['Field'] != 'id') {
                    echo "<th>" . $col['Field'] . "</th>";
                }
            }

            echo "</tr></thead>";
            echo "<tbody>";

            // Affiche les données
            while ($ligne = mysqli_fetch_assoc($data)) {
                echo "<tr>";
                foreach ($ligne as $cle => $valeur) {
                    //permet de filtrer les collones crées sans devoir faire une longue requète sql
                    if ($cle != 'id') {
                        echo "<td>" . $valeur . "</td>";
                    }
                }
                echo "</tr>";
            }

            echo "</tbody></table>";
            ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>




</body>
</html>