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

            <table border="1">
                <thead>
                <tr>
                    <th>NOM</th>
                    <th>NB DE SERIE</th>
                    <th>FABRICANT</th>
                    <th>MODELE</th>
                    <th>TYPE</th>
                    <th>CPU</th>
                    <th>RAM</th>
                    <th>DISK</th>
                    <th>OS</th>
                    <th>DOMAINE</th>
                    <th>LOCATION</th>
                    <th>BATIMENT</th>
                    <th>SALLE</th>
                    <th>ETAT</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                    <td>Exemple</td>
                </tr>
                </tbody>
                </table>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>




</body>
</html>