<?php
echo "<nav> <ul>";


echo '<li><a href="accueil.php">Page principale</a></li><li><a href="inventaire.php">Inventaire</a></li>';

if (isset($_SESSION['login'])
    && $_SESSION['login'] != 'sysadmin'
    && $_SESSION['login'] != 'adminweb') {

    echo '<li><a href="gestion.php">Gestion</a></li>';
}

if (isset($_SESSION['login']) && ($_SESSION['login'] === 'adminweb')) {

    echo '<li><a href="gestion.php">Gestion</a></li>';
    echo '<li><a href="utilisateur.php">Utilisateur</a></li>';
}

echo "</ul>
        </nav>";

