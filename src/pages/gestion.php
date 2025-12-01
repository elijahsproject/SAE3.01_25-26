<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Gestionnaire</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <?php include("header.php"); ?>

    <div class="main-container">
        <?php include("navbar.php"); ?>

        <div class="contenu">
            <?php
            $connecte = mysqli_connect("localhost", "root", "", "rpiBD");
            if (!$connecte) {
                die("Erreur de connexion");
            }
            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM inventaire WHERE id=$id");
                $resultat = mysqli_fetch_assoc($res);

                if ($resultat) {
                    echo '<h2>Modifier unité centrale</h2>';
                    echo '<form method="post" class="contenu_modifier">';
                    echo '<input type="hidden" name="id" value="'.$resultat['id'].'">';

                    echo '<table>';
                    echo '<tbody>';

                    echo '<tr><td>NOM</td><td><input type="text" name="nom" value="'.$resultat['nom'].'"></td></tr>';
                    echo '<tr><td>NB DE SERIE</td><td><input type="text" name="nb_serie" value="'.$resultat['nb_serie'].'"></td></tr>';
                    echo '<tr><td>FABRICANT</td><td><input type="text" name="fabricant" value="'.$resultat['fabricant'].'"></td></tr>';
                    echo '<tr><td>MODELE</td><td><input type="text" name="modele" value="'.$resultat['modele'].'"></td></tr>';
                    echo '<tr><td>TYPE</td><td><input type="text" name="type" value="'.$resultat['type'].'"></td></tr>';
                    echo '<tr><td>CPU</td><td><input type="text" name="cpu" value="'.$resultat['cpu'].'"></td></tr>';
                    echo '<tr><td>RAM</td><td><input type="number" name="ram" value="'.$resultat['ram'].'"></td></tr>';
                    echo '<tr><td>STOCKAGE</td><td><input type="text" name="disk" value="'.$resultat['disk'].'"></td></tr>';
                    echo '<tr><td>OS</td><td><input type="text" name="os" value="'.$resultat['os'].'"></td></tr>';
                    echo '<tr><td>DOMAINE</td><td><input type="text" name="domaine" value="'.$resultat['domaine'].'"></td></tr>';
                    echo '<tr><td>LOCATION</td><td><input type="text" name="location" value="'.$resultat['location'].'"></td></tr>';
                    echo '<tr><td>BATIMENT</td><td><input type="text" name="batiment" value="'.$resultat['batiment'].'"></td></tr>';
                    echo '<tr><td>SALLE</td><td><input type="text" name="salle" value="'.$resultat['salle'].'"></td></tr>';
                    echo '<tr><td>ETAT</td><td><input type="text" name="etat" value="'.$resultat['etat'].'"></td></tr>';


                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour" class="btn-valider">Modifier</button></td></tr>';
                    echo '<tr><td colspan="2"><button><a href="gestion.php">Annuler</a></button></td></tr>';

                    echo '</tbody>';
                    echo '</table>';

                    echo '</form>';
                }
            }
            // quand le deuxième bouton est appuyé
            if (isset($_POST['mise_a_jour'])) {
                $id = $_POST['id'];
                $nom = $_POST['nom'];
                $nb_serie = $_POST['nb_serie'];
                $fabricant = $_POST['fabricant'];
                $modele = $_POST['modele'];
                $type = $_POST['type'];
                $cpu = $_POST['cpu'];
                $ram = $_POST['ram'];
                $disk = $_POST['disk'];
                $os = $_POST['os'];
                $domaine = $_POST['domaine'];
                $location = $_POST['location'];
                $batiment = $_POST['batiment'];
                $salle = $_POST['salle'];
                $etat = $_POST['etat'];

                $sql = "UPDATE inventaire SET nom=?, nb_serie=?, fabricant=?, modele=?, type=?, cpu=?, ram=?, disk=?, os=?, domaine=?, location=?, batiment=?, salle=?, etat=? WHERE id=?";

                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param(
                        $stmt,
                        "ssssssisssssssi",
                        $nom, $nb_serie, $fabricant, $modele, $type, $cpu,
                        $ram, $disk, $os, $domaine, $location,
                        $batiment, $salle, $etat, $id
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                echo "Équipement mis à jour avec succès !";
            }

            ?>
            <h3>Liste de unité central : </h3>
            <div class="csv_box">
                <label for="csvFile" class="csv">Importer CSV</label>
                <input type="file" id="csvFile" accept=".csv" style="display: none;" />
                <a href="" class="csv2">Exporter en CSV</a>
            </div>

            <?php
                $data = mysqli_query($connecte, "SELECT * FROM inventaire");
                if (!$data) {
                    die("Erreur dans SELECT * ");
                }
            ?>

            <!-- Tableau des unités centrales -->
            <table border="1">
                <thead>
                <tr>
                    <th><button><a href="gestion_ajout.php">Ajout</a></button></th>
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
                    <th>ETAT</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($ligne = mysqli_fetch_assoc($data)) {
                    echo "<tr>";
                    echo "<td>";
                    echo "<form method='post' action='gestion.php'>";
                    echo "<input type='hidden' name='suppr_id' value='" . $ligne['id'] . "'>";
                    echo "<button type='submit' name='supprimer'>Supprimer</button>";
                    echo "</form>";
                    echo "</td>";

                    echo "<td>" . $ligne['nom'] . "</td>";
                    echo "<td>" . $ligne['nb_serie'] . "</td>";
                    echo "<td>" . $ligne['fabricant'] . "</td>";
                    echo "<td>" . $ligne['modele'] . "</td>";
                    echo "<td>" . $ligne['type'] . "</td>";
                    echo "<td>" . $ligne['cpu'] . "</td>";
                    echo "<td>" . $ligne['ram'] . "</td>";
                    echo "<td>" . $ligne['disk'] . "</td>";
                    echo "<td>" . $ligne['os'] . "</td>";
                    echo "<td>" . $ligne['domaine'] . "</td>";
                    echo "<td>" . $ligne['location'] . "</td>";
                    echo "<td>" . $ligne['batiment'] . "</td>";
                    echo "<td>" . $ligne['salle'] . "</td>";
                    echo "<td>" . $ligne['etat'] . "</td>";

                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='modif_id' value='" . $ligne['id'] . "'>";
                    echo "<button type='submit' name='modifier'>Modifier</button>";
                    echo "</form>";
                    echo "</td>";

                    echo "</tr>";
                }
                ?>




                </tbody>
            </table>

            <!-- Tableau de suppression unité centrale -->
            <h3>Liste de suppression (unité central) :</h3>
            <table border="1">
                <thead>
                <tr>
                    <th>Action</th>
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
                    <th>DATE DE GARANTIE</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><button>Mise en service</button></td>
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
                    <td>Date</td>
                </tr>
                </tbody>
            </table>

            <!-- Tableau Moniteurs -->
            <h3>Liste de moniteur :</h3>
            <div class="moniteur">
                <div class="csv_box">
                    <label for="csvFile1" class="csv">Importer CSV</label>
                    <input type="file" id="csvFile1" accept=".csv" style="display: none;" />
                    <a href="" class="csv2">Exporter en CSV</a>
                </div>
                <table border="1">
                    <thead>
                    <tr>
                        <th><button><a href="gestion_ajout.php">Ajout</a></button></th>
                        <th>SERIAL</th>
                        <th>MANUFACTURER</th>
                        <th>MODEL</th>
                        <th>SIZE_INCH</th>
                        <th>RESOLUTION</th>
                        <th>CONNECTOR</th>
                        <th>ATTACHED_TO</th>
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
                        <td><button><a href="gestion_modifier.php">Modifier</a></button></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tableau de suppression moniteur -->
            <h3>Liste de suppression (moniteur) :</h3>
            <table border="1">
                <thead>
                <tr>
                    <th>Action</th>
                    <th>SERIAL</th>
                    <th>MANUFACTURER</th>
                    <th>MODEL</th>
                    <th>SIZE_INCH</th>
                    <th>RESOLUTION</th>
                    <th>CONNECTOR</th>
                    <th>ATTACHED_TO</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><button>Mise en service</button></td>
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
