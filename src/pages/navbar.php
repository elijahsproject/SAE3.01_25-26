<?php
echo "<nav> <ul>
                ";


    echo '<li><a href="accueil.php">Page principale</a></li>
                          <li><a href="inventaire.php">Inventaire</a></li>';

// admin / tech / sysadmin
if (isset($_SESSION['login']) &&
    ($_SESSION['login'] === 'adminweb' ||
        $_SESSION['login'] === 'tech1')) {

    echo '
                          <li><a href="gestion.php">Gestion</a></li>';
}

echo "</ul>
        </nav>";

