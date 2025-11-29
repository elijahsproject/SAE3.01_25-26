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
            <h3>Liste de unité central : </h3>
            <div class="csv_box">
                <label for="csvFile" class="csv">Importer CSV</label>
                <input type="file" id="csvFile" accept=".csv" style="display: none;" />
                <a href="" class="csv2">Exporter en CSV</a>
            </div>

            <?php
            $connecte = mysqli_connect("localhost", "root", "", "rpiBD");
            if (!$connecte) {
                die("Erreur de connexion");
            }

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
                <?php while ($ligne = mysqli_fetch_assoc($data)) : ?>
                    <tr>
                        <td>
                            <form method='post' action='gestion.php'>
                                <input type='hidden' name='suppr_id' value='<?= $ligne['id'] ?>'>
                                <button type='submit' name='supprimer'>Supprimer</button>
                            </form>
                        </td>
                        <td><?= $ligne['nom'] ?></td>
                        <td><?= $ligne['nb_serie'] ?></td>
                        <td><?= $ligne['fabricant'] ?></td>
                        <td><?= $ligne['modele'] ?></td>
                        <td><?= $ligne['type'] ?></td>
                        <td><?= $ligne['cpu'] ?></td>
                        <td><?= $ligne['ram'] ?></td>
                        <td><?= $ligne['disk'] ?></td>
                        <td><?= $ligne['os'] ?></td>
                        <td><?= $ligne['domaine'] ?></td>
                        <td><?= $ligne['location'] ?></td>
                        <td><?= $ligne['batiment'] ?></td>
                        <td><?= $ligne['salle'] ?></td>
                        <td><?= $ligne['etat'] ?></td>
                        <td>
                            <form method='post' action='gestion_modifier.php'>
                                <input type='hidden' name='modif_id' value='<?= $ligne['id'] ?>'>
                                <button type='submit' name='modifier'>Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
