<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Accueil</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="wrapper">

        <?php

        include_once("header.php");


        ?>

    <div class="main-container">
        <?php
        include("navbar.php");
        ?>

        <div class="contenu_index">
            <h1>Bienvenue, sur votre gestion de parc IT simplifiée</h1>

            <div class="video-container">
                <video controls>
                    <source src="../video/video_explication.mp4" type="video/mp4">
                </video>
            </div>

            <p>...</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>

</body>
</html>