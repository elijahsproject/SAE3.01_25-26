<?php
echo "<nav><ul>";

echo '<li><a href="accueil.php">Page principale</a></li>';
echo '<li><a href="inventaire.php">Inventaire</a></li>';
echo '<li><a href="statistiques.php">Statistiques</a></li>';

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'technicien') {
        echo '<li><a href="gestion.php">Gestion</a></li>';
    }

    if ($_SESSION['role'] === 'sysadmin') {
        echo '<li><a href="journal_activite.php">Journal d\'activité</a></li>';
    }

    if ($_SESSION['role'] === 'adminweb') {
        echo '<li><a href="gestion.php">Gestion</a></li>';
        echo '<li><a href="utilisateur.php">Utilisateurs</a></li>';
    }
}

echo "</ul></nav>";
?>
