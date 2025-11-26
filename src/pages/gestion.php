<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Gestionnaire</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <header>
        <h1>Plateforme de gestion du parc informatique</h1>
        <h2>Technicien</h2>
        <a href="../accueil.php">Deconnexion</a>
    </header>

    <div class="main-container">
        <nav>
            <ul>
                <li><a href="../accueil.php">Page principale</a></li>
                <li><a href="inventaire.php">Inventaire</a></li>
                <li><a href="gestion.html">Gestion</a></li>
            </ul>
        </nav>


        <div class="contenu">
            <h3>Liste de unité central : </h3>
            <div class="csv_box">
                <label for="csvFile" class="csv">Importer CSV</label>
                <input type="file" id="csvFile" accept=".csv" style="display: none;" />
                <a href="" class="csv2">Exporter en CSV</a>
            </div>

            <table border="1">
                <thead>
                <tr>
                    <th>
                        <button><a href="gestion_ajout.php">Ajout</a></button>
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
                    <td><button><a href="gestion_modifier.php">Modifier</a></button></td>
                </tr>
                </tbody>
            </table>



            <h3>Liste de supression(unité central) : </h3>
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
                    <th>DATE DE GUARANTIE</th>
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
            </br>
            </br>
            </br>
            <h3>Liste de moniteur : </h3>
                <div class="moniteur">
                        <div class="csv_box">
                            <label for="csvFile1" class="csv">Importer CSV</label>
                            <input type="file" id="csvFile1" accept=".csv" style="display: none;" />
                            <a href="" class="csv2">Exporter en CSV</a>
                        </div>
                    <table border="1">
                        <thead>
                        <tr>
                            <th>
                                <button><a href="gestion_ajout.php">Ajout</a></button>
                            </th>
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
            <h3>Liste de supression(moniteur) : </h3>
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