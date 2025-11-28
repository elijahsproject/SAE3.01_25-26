<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Ajouter</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <?php
    include("../pages/header.php");
    ?>

    <div class="main-container">
    <nav>
        <ul>
            <li><a href="../accueil.php">Page principale</a></li>
            <li><a href="inventaire.php">Inventaire</a></li>
            <li><a href="gestion.php">Gestion</a></li>
        </ul>
    </nav>


    <div class="contenu">
        <div class="boite_ajout">
            <h3>Ajouter</h3>
            <form method="post" action="">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" required="required">
                <label for="numserie">Numero de serie</label>
                <input type="text" id="numserie" name="numserie" placeholder="numserie" required="required">
                <button type="submit" >Valider</button>
            </form>
        </div>
        <table border="1">
            <thead>
            <tr>
                <th>
                    <button><a href="gestion.php">Ajout</a></button>
                </th>
                <th>NOM</th>
                <th>NB DE SERIE</th>
                <th>FABRICANT</th>
                <th>MODELE</th>
                <th>TYPE</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>STOCKAGE</th>
                <th>OS</th>
                <th>DOMAINE</th>
                <th>LOCATION</th>
                <th>BATIMENT</th>
                <th>SALLE</th>
                <th>ADRESSE MAC</th>
                <th>ETAT</th>
                <th>DATE D'ACHAT</th>
                <th>DATE DE GUARANTIE</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><button>Supprimer</button></td>
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
                <td>Exemple</td>
                <td>Exemple</td>
                <td>Exemple</td>
                <td><button><a href="gestion_modifier_ajout.php">Modifier</a></button></td>
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