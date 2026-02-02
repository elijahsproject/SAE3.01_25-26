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
            <div class="gestion-nav">
                <span class="gestion-btn gestion-btn-active" aria-current="page">
                    Unités centrales
                </span>
                <a href="moniteur.php" class="gestion-btn">Moniteurs</a>
            </div>
            <?php

            $connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
            if (!$connecte) {
                die("Erreur de connexion");
            }


            if (isset($_GET['export_csv'])) {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=inventaire_export.csv');
                $output = fopen('php://output', 'w');

                // En-têtes
                fputcsv($output, array('NAME', 'SERIAL', 'MANUFACTURER', 'MODEL', 'TYPE', 'CPU', 'RAM_MB', 'DISK_GB', 'OS', 'DOMAIN', 'LOCATION', 'BUILDING', 'ROOM', 'MACADDR', 'PURCHASE_DATE', 'WARRANTY_END'));

                $query = "SELECT NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END FROM inventaire";
                $result = mysqli_query($connecte, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit();
            }

            if (isset($_POST['import_csv'])) {
                if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
                    $filename = $_FILES['csvFile']['tmp_name'];
                    $handle = fopen($filename, "r");
                    fgetcsv($handle);
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $sql = "INSERT INTO inventaire (NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                        $stmt = mysqli_prepare($connecte, $sql);
                        mysqli_stmt_bind_param($stmt, "ssssssiiissssssss",
                                $data[0], $data[1], $data[2], $data[3],
                                $data[4], $data[5], $data[6], $data[7],
                                $data[8], $data[9], $data[10], $data[11],
                                $data[12], $data[13], $data[14], $data[15]
                        );
                        mysqli_stmt_execute($stmt);
                    }
                    fclose($handle);
                    echo "<p style='color:green;'>Importation Inventaire réussie !</p>";
                }
            }

            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM inventaire WHERE ID=$id");
                $resultat = mysqli_fetch_assoc($res);

                if ($resultat) {
                    echo '<h2>Modifier unité centrale</h2>';
                    echo '<form method="post" class="contenu_modifier">';
                    echo '<input type="hidden" name="id" value="'.$resultat['ID'].'">';

                    echo '<table><tbody>';
                    echo '<tr><td>NOM</td><td><input type="text" name="NAME" value="'.$resultat['NAME'].'"></td></tr>';
                    echo '<tr><td>NUMÉRO DE SÉRIE</td><td><input type="text" name="SERIAL" value="'.$resultat['SERIAL'].'"></td></tr>';

                    echo '<tr><td>MANUFACTURER</td><td><select name="MANUFACTURER">';
                    $result_man = mysqli_query($connecte, "SELECT DISTINCT MANUFACTURER FROM inventaire ORDER BY MANUFACTURER ASC");
                    while ($man = mysqli_fetch_assoc($result_man)) {
                        $selected = (isset($resultat) && $resultat['MANUFACTURER'] == $man['MANUFACTURER']) ? 'selected' : '';
                        echo '<option value="'.htmlspecialchars($man['MANUFACTURER']).'" '.$selected.'>'.htmlspecialchars($man['MANUFACTURER']).'</option>';
                    }
                    if (isset($_SESSION['options_suppl']['MANUFACTURER'])) {
                        foreach ($_SESSION['options_suppl']['MANUFACTURER'] as $man_supp) {
                            $selected = (isset($resultat) && $resultat['MANUFACTURER'] == $man_supp) ? 'selected' : '';
                            echo '<option value="'.htmlspecialchars($man_supp).'" '.$selected.'>'.htmlspecialchars($man_supp).'</option>';
                        }
                    }

                    echo '<option value="">--Autre--</option>';
                    echo '</select></td></tr>';

                    echo '<tr><td>MODÈLE</td><td><input type="text" name="MODEL" value="'.$resultat['MODEL'].'"></td></tr>';
                    echo '<tr><td>TYPE</td><td><input type="text" name="TYPE" value="'.$resultat['TYPE'].'"></td></tr>';
                    echo '<tr><td>CPU</td><td><input type="text" name="CPU" value="'.$resultat['CPU'].'"></td></tr>';
                    echo '<tr><td>RAM (MB)</td><td><input type="number" name="RAM_MB" value="'.$resultat['RAM_MB'].'"></td></tr>';
                    echo '<tr><td>STOCKAGE (GB)</td><td><input type="number" name="DISK_GB" value="'.$resultat['DISK_GB'].'"></td></tr>';

                    echo '<tr><td>OS</td><td><select name="OS">';

                    $result_os = mysqli_query($connecte, "SELECT DISTINCT OS FROM inventaire ORDER BY OS ASC");
                    while ($os = mysqli_fetch_assoc($result_os)) {
                        $selected = (isset($resultat) && $resultat['OS'] == $os['OS']) ? 'selected' : '';
                        echo '<option value="'.htmlspecialchars($os['OS']).'" '.$selected.'>'.htmlspecialchars($os['OS']).'</option>';
                    }


                    if (isset($_SESSION['options_suppl']['OS'])) {
                        foreach ($_SESSION['options_suppl']['OS'] as $os_supp) {
                            $selected = (isset($resultat) && $resultat['OS'] == $os_supp) ? 'selected' : '';
                            echo '<option value="'.htmlspecialchars($os_supp).'" '.$selected.'>'.htmlspecialchars($os_supp).'</option>';
                        }
                    }

                    echo '<option value="">--Autre--</option>';
                    echo '</select></td></tr>';


                    echo '<tr><td>DOMAINE</td><td><input type="text" name="DOMAIN" value="'.$resultat['DOMAIN'].'"></td></tr>';
                    echo '<tr><td>LOCATION</td><td><input type="text" name="LOCATION" value="'.$resultat['LOCATION'].'"></td></tr>';
                    echo '<tr><td>BÂTIMENT</td><td><input type="text" name="BUILDING" value="'.$resultat['BUILDING'].'"></td></tr>';
                    echo '<tr><td>SALLE</td><td><input type="text" name="ROOM" value="'.$resultat['ROOM'].'"></td></tr>';
                    echo '<tr><td>ADRESSE MAC</td><td><input type="text" name="MACADDR" value="'.$resultat['MACADDR'].'"></td></tr>';

                    echo '<tr><td>DATE D\'ACHAT</td><td><input type="date" name="PURCHASE_DATE" value="'.$resultat['PURCHASE_DATE'].'"></td></tr>';
                    echo '<tr><td>FIN DE GARANTIE</td><td><input type="date" name="WARRANTY_END" value="'.$resultat['WARRANTY_END'].'"></td></tr>';

                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour" class="btn-valider">Modifier</button></td></tr>';
                    echo '</tbody></table></form>';
                }
            }

            if (isset($_POST['supprimer'])) {
                $id = intval($_POST['suppr_id']);

                $res = mysqli_query($connecte, "SELECT * FROM inventaire WHERE ID=$id");

                if ($res && mysqli_num_rows($res) > 0) {
                    $equipement = mysqli_fetch_assoc($res);
                    $sql_rebut = "
            INSERT INTO rebut_devices
            (NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

                    $stmt = mysqli_prepare($connecte, $sql_rebut);
                    mysqli_stmt_bind_param(
                            $stmt,
                            "ssssssiissssssss",
                            $equipement['NAME'],
                            $equipement['SERIAL'],
                            $equipement['MANUFACTURER'],
                            $equipement['MODEL'],
                            $equipement['TYPE'],
                            $equipement['CPU'],
                            $equipement['RAM_MB'],
                            $equipement['DISK_GB'],
                            $equipement['OS'],
                            $equipement['DOMAIN'],
                            $equipement['LOCATION'],
                            $equipement['BUILDING'],
                            $equipement['ROOM'],
                            $equipement['MACADDR'],
                            $equipement['PURCHASE_DATE'],
                            $equipement['WARRANTY_END']
                    );

                    if (mysqli_stmt_execute($stmt)) {

                        $model = mysqli_real_escape_string($connecte, $equipement['MODEL']);
                        mysqli_query($connecte, "DELETE FROM moniteur WHERE MODEL='$model'");

                        mysqli_query($connecte, "DELETE FROM inventaire WHERE ID=$id");

                        header('Location: gestion.php');
                        exit;
                    } else {
                        echo "<p style='color:red;'>Erreur lors de l'ajout au rebut.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Équipement introuvable.</p>";
                }
            }



            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter unité centrale</h2>';
                echo '<form method="post" class="contenu_modifier">';
                echo '<table><tbody>';

                echo '<tr><td>NAME</td><td><input type="text" name="NAME" value=""></td></tr>';
                echo '<tr><td>SERIAL</td><td><input type="text" name="SERIAL" value=""></td></tr>';

                echo '<tr><td>MANUFACTURER</td><td><select name="MANUFACTURER">';

                $result_man = mysqli_query($connecte, "SELECT DISTINCT MANUFACTURER FROM inventaire ORDER BY MANUFACTURER ASC");
                while ($man = mysqli_fetch_assoc($result_man)) {
                    $selected = (isset($resultat) && $resultat['MANUFACTURER'] == $man['MANUFACTURER']) ? 'selected' : '';
                    echo '<option value="'.htmlspecialchars($man['MANUFACTURER']).'" '.$selected.'>'.htmlspecialchars($man['MANUFACTURER']).'</option>';
                }

                if (isset($_SESSION['options_suppl']['MANUFACTURER'])) {
                    foreach ($_SESSION['options_suppl']['MANUFACTURER'] as $man_supp) {
                        $selected = (isset($resultat) && $resultat['MANUFACTURER'] == $man_supp) ? 'selected' : '';
                        echo '<option value="'.htmlspecialchars($man_supp).'" '.$selected.'>'.htmlspecialchars($man_supp).'</option>';
                    }
                }

                echo '<option value="">--Autre--</option>';
                echo '</select></td></tr>';
                echo '<tr><td>MODEL</td><td><input type="text" name="MODEL" value=""></td></tr>';
                echo '<tr><td>TYPE</td><td><input type="text" name="TYPE" value=""></td></tr>';
                echo '<tr><td>CPU</td><td><input type="text" name="CPU" value=""></td></tr>';
                echo '<tr><td>RAM_MB</td><td><input type="number" name="RAM_MB" value=""></td></tr>';
                echo '<tr><td>DISK_GB</td><td><input type="number" name="DISK_GB" value=""></td></tr>';

                echo '<tr><td>OS</td><td><select name="OS">';

                $result_os = mysqli_query($connecte, "SELECT DISTINCT OS FROM inventaire ORDER BY OS ASC");
                while ($os = mysqli_fetch_assoc($result_os)) {
                    $selected = (isset($resultat) && $resultat['OS'] == $os['OS']) ? 'selected' : '';
                    echo '<option value="'.htmlspecialchars($os['OS']).'" '.$selected.'>'.htmlspecialchars($os['OS']).'</option>';
                }

                if (isset($_SESSION['options_suppl']['OS'])) {
                    foreach ($_SESSION['options_suppl']['OS'] as $os_supp) {
                        $selected = (isset($resultat) && $resultat['OS'] == $os_supp) ? 'selected' : '';
                        echo '<option value="'.htmlspecialchars($os_supp).'" '.$selected.'>'.htmlspecialchars($os_supp).'</option>';
                    }
                }

                echo '<option value="">--Autre--</option>';
                echo '</select></td></tr>';


                echo '<tr><td>DOMAIN</td><td><input type="text" name="DOMAIN" value=""></td></tr>';
                echo '<tr><td>LOCATION</td><td><input type="text" name="LOCATION" value=""></td></tr>';
                echo '<tr><td>BUILDING</td><td><input type="text" name="BUILDING" value=""></td></tr>';
                echo '<tr><td>ROOM</td><td><input type="text" name="ROOM" value=""></td></tr>';
                echo '<tr><td>MACADDR</td><td><input type="text" name="MACADDR" value=""></td></tr>';
                echo '<tr><td>PURCHASE_DATE</td><td><input type="date" name="PURCHASE_DATE" value=""></td></tr>';
                echo '<tr><td>WARRANTY_END</td><td><input type="date" name="WARRANTY_END" value=""></td></tr>';

                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd" class="btn-valider">Ajouter</button></td></tr>';
                echo '<tr><td colspan="2"><button type="button" onclick="window.location.href=\'gestion.php\'">Annuler</button></td></tr>';

                echo '</tbody></table>';
                echo '</form>';
            }


            $message = "";
            if (isset($_POST['ajouter_bd'])) {
                // Vérifier si le SERIAL existe déjà
                $check_sql = "SELECT COUNT(*) as count FROM inventaire WHERE SERIAL = ?";
                $check_stmt = mysqli_prepare($connecte, $check_sql);
                mysqli_stmt_bind_param($check_stmt, "s", $_POST['SERIAL']);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_bind_result($check_stmt, $count);
                mysqli_stmt_fetch($check_stmt);
                mysqli_stmt_close($check_stmt);

                if ($count > 0) {
                    $message = "<p style='color:red;'>Erreur : ce numéro de série existe déjà !</p>";
                } else {
                    // insertion
                    $sql = "INSERT INTO inventaire (NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($connecte, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssssiissssssss",
                            $_POST['NAME'], $_POST['SERIAL'], $_POST['MANUFACTURER'], $_POST['MODEL'],
                            $_POST['TYPE'], $_POST['CPU'], $_POST['RAM_MB'], $_POST['DISK_GB'],
                            $_POST['OS'], $_POST['DOMAIN'], $_POST['LOCATION'], $_POST['BUILDING'],
                            $_POST['ROOM'], $_POST['MACADDR'], $_POST['PURCHASE_DATE'], $_POST['WARRANTY_END']
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<p style='color:green;'>Équipement ajouté avec succès !</p>";
                    } else {
                        $message = "<p style='color:red;'>Erreur lors de l'ajout : ".mysqli_error($connecte)."</p>";
                    }
                }
            }
            echo $message;





            if (isset($_POST['mise_a_jour'])) {

                $sql = "UPDATE inventaire SET NAME=?, SERIAL=?, MANUFACTURER=?, MODEL=?, TYPE=?, CPU=?, RAM_MB=?, DISK_GB=?, OS=?, DOMAIN=?, LOCATION=?, BUILDING=?, ROOM=?, MACADDR=?, PURCHASE_DATE=?, WARRANTY_END=? WHERE ID=?";

                $stmt = mysqli_prepare($connecte, $sql);

                mysqli_stmt_bind_param($stmt, "ssssssiiisssssssi",
                        $_POST['NAME'], $_POST['SERIAL'], $_POST['MANUFACTURER'], $_POST['MODEL'],
                        $_POST['TYPE'], $_POST['CPU'], $_POST['RAM_MB'], $_POST['DISK_GB'],
                        $_POST['OS'], $_POST['DOMAIN'], $_POST['LOCATION'], $_POST['BUILDING'],
                        $_POST['ROOM'], $_POST['MACADDR'], $_POST['PURCHASE_DATE'],
                        $_POST['WARRANTY_END'], $_POST['id']
                );

                mysqli_stmt_execute($stmt);
                echo "<p style='color:green;'>Équipement mis à jour avec succès !</p>";
            }

            ?>

            <h3>Liste de unité central : </h3>
            <div class="csv_box">
                <form method="post" enctype="multipart/form-data" style="display:inline;">
                    <label for="csvFileInputGestion" class="csv" style="cursor:pointer;">Importer CSV</label>
                    <input type="file" id="csvFileInputGestion" name="csvFile" accept=".csv" style="display: none;" onchange="this.form.submit()" />
                    <input type="hidden" name="import_csv" value="1">
                </form>
                <a href="gestion.php?export_csv=1" class="csv2">Exporter en CSV</a>
            </div>

            <?php
            $data = mysqli_query($connecte, "SELECT * FROM inventaire");
            if (!$data) {
                die("Erreur dans SELECT * : " . mysqli_error($connecte));
            }

            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>
        <form method="post">
            <input type="hidden" name="ajout" value="ajout">
            <button type="submit">Ajout</button>
        </form>
      </th>';
            echo '<th>NAME</th>';
            echo '<th>SERIAL</th>';
            echo '<th>MANUFACTURER</th>';
            echo '<th>MODEL</th>';
            echo '<th>TYPE</th>';
            echo '<th>CPU</th>';
            echo '<th>RAM_MB</th>';
            echo '<th>DISK_GB</th>';
            echo '<th>OS</th>';
            echo '<th>DOMAIN</th>';
            echo '<th>LOCATION</th>';
            echo '<th>BUILDING</th>';
            echo '<th>ROOM</th>';
            echo '<th>MACADDR</th>';
            echo '<th>PURCHASE_DATE</th>';
            echo '<th>WARRANTY_END</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';


            while ($ligne = mysqli_fetch_assoc($data)) {

                $id = $ligne['ID'];

                echo '<tr>';

                echo '<td>';
                echo '<form method="post" onsubmit="return confirm(\'Confirmer la suppression ?\');">';
                echo '<input type="hidden" name="suppr_id" value="' . htmlspecialchars($id) . '">';
                echo '<button type="submit" name="supprimer">Supprimer</button>';
                echo '</form>';
                echo '</td>';

                echo '<td>' . htmlspecialchars($ligne['NAME']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['SERIAL']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['MANUFACTURER']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['MODEL']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['TYPE']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['CPU']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['RAM_MB']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['DISK_GB']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['OS']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['DOMAIN']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['LOCATION']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['BUILDING']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['ROOM']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['MACADDR']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['PURCHASE_DATE']) . '</td>';
                echo '<td>' . htmlspecialchars($ligne['WARRANTY_END']) . '</td>';


                echo '<td>';
                echo '<form method="post">';
                echo '<input type="hidden" name="modif_id" value="' . htmlspecialchars($id) . '">';
                echo '<button type="submit" name="modifier">Modifier</button>';
                echo '</form>';
                echo '</td>';

                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            ?>
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