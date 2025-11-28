<?php
echo "
<header>
    <h1>Plateforme de gestion du parc informatique</h1>" ;
if (isset($_SESSION['login'])){
    $nom = $_SESSION['login'];
    echo"<h2> $nom </h2>
<a href='logout.php'>Deconnexion</a>";
    }
else {
    echo '<a class="btn-connexion" href="../pages/connexion.php">Connexion</a>';
}
echo "
</header>
";
?>
