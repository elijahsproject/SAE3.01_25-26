<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="wrapper">
    <header>
        <h1>Plateforme de gestion du parc informatique</h1>

        <?php
        if (isset($_SESSION['login'])) {
            echo '<p class="user-info">Bienvenue ' .$_SESSION['login']. '</p>';
            echo '<a class="btn-deco" href="pages/logout.php">Déconnexion</a>';
        }
        else {
            echo '<a class="btn-connexion" href="pages/connexion.php">Connexion</a>';
        }
        ?>
    </header>

    <div class="main-container">
        <nav>
            <ul>
                <?php
                if (!isset($_SESSION['login'])) {
                    echo '<li><a href="accueil.php">Page principale</a></li>';
                }

                //client
                if (isset($_SESSION['login']) && $_SESSION['login'] === 'client') {
                    echo '<li><a href="accueil.php">Page principale</a></li>
                          <li><a href="pages/inventaire.php">Inventaire</a></li>';
                }

                // admin / tech / sysadmin
                if (isset($_SESSION['login']) &&
                        ($_SESSION['login'] === 'adminweb' ||
                                $_SESSION['login'] === 'sysadmin' ||
                                $_SESSION['login'] === 'tech1')) {

                    echo '<li><a href="accueil.php">Page principale</a></li>
                          <li><a href="pages/inventaire.php">Inventaire</a></li>
                          <li><a href="pages/gestion.php">Gestion</a></li>';
                }

                // Bouton déconnexion visible pour TOUT utilisateur connecté
                if (isset($_SESSION['login'])) {
                    echo '<li><a href="pages/logout.php">Déconnexion</a></li>';
                }
                ?>
            </ul>
        </nav>

        <div class="contenu_index">
            <h1>Bienvenue, sur votre gestion de parc IT simplifiée</h1>

            <div class="video-container">
                <video controls>
                    <source src="video/video_explication.mp4" type="video/mp4">
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
