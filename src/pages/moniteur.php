<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Moniteurs</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">

    <?php include("header.php"); ?>

    <div class="main-container">

        <?php include("navbar.php"); ?>

        <div class="contenu">

            <div class="gestion-nav">
                <a href="gestion.php" class="gestion-btn">Unités centrales</a>
                <a href="moniteur.php" class="gestion-btn gestion-btn-active">Moniteurs</a>
            </div>

            <?php
            $connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
            if (!$connecte) { die("Erreur de connexion"); }

            if (isset($_GET['export_csv'])) {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=moniteurs_export.csv');
                $output = fopen('php://output', 'w');

                fputcsv($output, array('SERIAL', 'MANUFACTURER', 'MODEL', 'SIZE_INCH', 'RESOLUTION', 'CONNECTOR', 'ATTACHED_TO'));

                $query = "SELECT SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO FROM moniteur";
                $result = mysqli_query($connecte, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit();
            }

            if (isset($_POST['import_csv'])) {
                if ($_FILES['csvFile']['error'] == 0) {
                    $filename = $_FILES['csvFile']['tmp_name'];
                    $handle = fopen($filename, "r");

                    fgetcsv($handle);

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                        $sql = "INSERT INTO moniteur (SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($connecte, $sql);
                        mysqli_stmt_bind_param($stmt, "sssisss", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);
                        mysqli_stmt_execute($stmt);
                    }
                    fclose($handle);
                    echo "<p style='color:green;'>Importation CSV réussie !</p>";
                } else {
                    echo "<p style='color:red;'>Erreur lors du téléchargement du fichier.</p>";
                }
            }

            if (isset($_POST['modifier'])) {

                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM moniteur WHERE ID=$id");
                $moniteur = mysqli_fetch_assoc($res);

                if ($moniteur) {
                    echo '<h2>Modifier un moniteur</h2>';
                    echo '<form method="post" class="contenu_modifier">';
                    echo '<input type="hidden" name="id" value="'.$moniteur['ID'].'">';
                    echo '<table><tbody>';

                    echo '<tr><td>SERIAL</td><td><input type="text" name="SERIAL" value="'.$moniteur['SERIAL'].'"></td></tr>';
                    echo '<tr><td>MANUFACTURER</td><td><input type="text" name="MANUFACTURER" value="'.$moniteur['MANUFACTURER'].'"></td></tr>';
                    echo '<tr><td>MODEL</td><td><input type="text" name="MODEL" value="'.$moniteur['MODEL'].'"></td></tr>';
                    echo '<tr><td>SIZE_INCH</td><td><input type="number" name="SIZE_INCH" value="'.$moniteur['SIZE_INCH'].'"></td></tr>';
                    echo '<tr><td>RESOLUTION</td><td><input type="text" name="RESOLUTION" value="'.$moniteur['RESOLUTION'].'"></td></tr>';
                    echo '<tr><td>CONNECTOR</td><td><input type="text" name="CONNECTOR" value="'.$moniteur['CONNECTOR'].'"></td></tr>';

                    echo '<tr><td>ATTACHED_TO</td><td>';
                    echo '<select name="ATTACHED_TO">';
                    echo '<option value="">-- Non rattaché --</option>';

                    $sql_attached = "SELECT MODEL FROM inventaire ORDER BY MODEL ASC";
                    $res_attached = mysqli_query($connecte, $sql_attached);

                    while ($row = mysqli_fetch_assoc($res_attached)) {
                        $mdl = htmlspecialchars($row['MODEL']);
                        $selected = ($mdl == $moniteur['ATTACHED_TO']) ? 'selected' : '';
                        echo "<option value='$mdl' $selected>$mdl</option>";
                    }

                    echo '</select>';
                    echo '</td></tr>';

                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour" class="btn-valider">Modifier</button></td></tr>';
                    echo '</tbody></table></form>';
                }
            }

            if (isset($_POST['supprimer'])) {
                $id = intval($_POST['suppr_id']);
                mysqli_query($connecte, "DELETE FROM moniteur WHERE ID=$id");
                header("Location: moniteur.php");
                exit;
            }

            if (isset($_POST['ajout'])) {

                echo '<h2>Ajouter un moniteur</h2>';
                echo '<form method="post" class="contenu_modifier">';
                echo '<table><tbody>';

                echo '<tr><td>SERIAL</td><td><input type="text" name="SERIAL"></td></tr>';
                echo '<tr><td>MANUFACTURER</td><td><input type="text" name="MANUFACTURER"></td></tr>';
                echo '<tr><td>MODEL</td><td><input type="text" name="MODEL"></td></tr>';
                echo '<tr><td>SIZE_INCH</td><td><input type="number" name="SIZE_INCH"></td></tr>';
                echo '<tr><td>RESOLUTION</td><td><input type="text" name="RESOLUTION"></td></tr>';
                echo '<tr><td>CONNECTOR</td><td><input type="text" name="CONNECTOR"></td></tr>';

                echo '<tr><td>ATTACHED_TO</td><td>';
                echo '<select name="ATTACHED_TO">';
                echo '<option value="">-- Non rattaché --</option>';

                $res_attached = mysqli_query($connecte, "SELECT MODEL FROM inventaire ORDER BY MODEL ASC");
                while ($row = mysqli_fetch_assoc($res_attached)) {
                    $mdl = htmlspecialchars($row['MODEL']);
                    echo "<option value='$mdl'>$mdl</option>";
                }

                echo '</select>';
                echo '</td></tr>';

                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd" class="btn-valider">Ajouter</button></td></tr>';
                echo '<tr><td colspan="2"><button type="button" onclick="window.location.href=\'moniteur.php\'">Annuler</button></td></tr>';

                echo '</tbody></table>';
                echo '</form>';
            }


            if (isset($_POST['ajouter_bd'])) {
                $serial = $_POST['SERIAL'];
                $attached_to = $_POST['ATTACHED_TO'];


                $check_serial = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE SERIAL = ?");
                mysqli_stmt_bind_param($check_serial, "s", $serial);
                mysqli_stmt_execute($check_serial);
                mysqli_stmt_bind_result($check_serial, $count_serial);
                mysqli_stmt_fetch($check_serial);
                mysqli_stmt_close($check_serial);

                $count_attached = 0;
                if (!empty($attached_to)) {
                    $check_attached = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE ATTACHED_TO = ?");
                    mysqli_stmt_bind_param($check_attached, "s", $attached_to);
                    mysqli_stmt_execute($check_attached);
                    mysqli_stmt_bind_result($check_attached, $count_attached);
                    mysqli_stmt_fetch($check_attached);
                    mysqli_stmt_close($check_attached);
                }

                if ($count_serial > 0) {
                    echo "<p style='color:red;'>Erreur : ce SERIAL de moniteur existe déjà !</p>";
                } elseif ($count_attached > 0) {
                    echo "<p style='color:red;'>Erreur : cet ATTACHED_TO est déjà utilisé par un autre moniteur !</p>";
                } else {
                    $stmt = mysqli_prepare($connecte, "INSERT INTO moniteur (SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sssisss",
                            $serial, $_POST['MANUFACTURER'], $_POST['MODEL'],
                            $_POST['SIZE_INCH'], $_POST['RESOLUTION'], $_POST['CONNECTOR'],
                            $attached_to
                    );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    echo "<p style='color:green;'>Moniteur ajouté avec succès !</p>";
                }
            }

            if (isset($_POST['mise_a_jour'])) {
                $id = $_POST['id'];
                $serial = $_POST['SERIAL'];
                $attached_to = $_POST['ATTACHED_TO'];


                $check_serial = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE SERIAL = ? AND ID != ?");
                mysqli_stmt_bind_param($check_serial, "si", $serial, $id);
                mysqli_stmt_execute($check_serial);
                mysqli_stmt_bind_result($check_serial, $count_serial);
                mysqli_stmt_fetch($check_serial);
                mysqli_stmt_close($check_serial);


                $count_attached = 0;
                if (!empty($attached_to)) {
                    $check_attached = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE ATTACHED_TO = ? AND ID != ?");
                    mysqli_stmt_bind_param($check_attached, "si", $attached_to, $id);
                    mysqli_stmt_execute($check_attached);
                    mysqli_stmt_bind_result($check_attached, $count_attached);
                    mysqli_stmt_fetch($check_attached);
                    mysqli_stmt_close($check_attached);
                }

                if ($count_serial > 0) {
                    echo "<p style='color:red;'>Erreur : ce SERIAL est déjà utilisé par un autre moniteur !</p>";
                } elseif ($count_attached > 0) {
                    echo "<p style='color:red;'>Erreur : cet ATTACHED_TO est déjà utilisé par un autre moniteur !</p>";
                } else {
                    $stmt = mysqli_prepare($connecte, "UPDATE moniteur SET SERIAL=?, MANUFACTURER=?, MODEL=?, SIZE_INCH=?, RESOLUTION=?, CONNECTOR=?, ATTACHED_TO=? WHERE ID=?");
                    mysqli_stmt_bind_param($stmt, "sssisssi",
                            $serial, $_POST['MANUFACTURER'], $_POST['MODEL'],
                            $_POST['SIZE_INCH'], $_POST['RESOLUTION'], $_POST['CONNECTOR'],
                            $attached_to, $id
                    );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    echo "<p style='color:green;'>Moniteur mis à jour avec succès !</p>";
                }
            }

            ?>

            <h3>Liste des moniteurs</h3>

            <div class="csv_box">
                <form method="post" enctype="multipart/form-data" style="display:inline;">
                    <label for="csvFileInput" class="csv" style="cursor:pointer;">Importer CSV</label>
                    <input type="file" id="csvFileInput" name="csvFile" accept=".csv" style="display:none;" onchange="this.form.submit()">
                    <input type="hidden" name="import_csv" value="1">
                </form>

                <a href="moniteur.php?export_csv=1" class="csv2">Exporter CSV</a>
            </div>

            <?php
            $data = mysqli_query($connecte, "SELECT * FROM moniteur");

            echo '<table border="1">';
            echo '<thead><tr>';

            echo '<th>
        <form method="post">
            <input type="hidden" name="ajout">
            <button type="submit">Ajout</button>
        </form>
      </th>';

            echo '<th>SERIAL</th>';
            echo '<th>MANUFACTURER</th>';
            echo '<th>MODEL</th>';
            echo '<th>SIZE_INCH</th>';
            echo '<th>RESOLUTION</th>';
            echo '<th>CONNECTOR</th>';
            echo '<th>ATTACHED_TO</th>';
            echo '<th>Action</th>';
            echo '</tr></thead>';

            echo '<tbody>';

            while ($m = mysqli_fetch_assoc($data)) {
                $id = $m['ID'];
                echo '<tr>';
                echo '<td>';
                echo '<form method="post" onsubmit="return confirm(\'Confirmer la suppression ?\');">';
                echo '<input type="hidden" name="suppr_id" value="'.$id.'">';
                echo '<button name="supprimer">Supprimer</button>';
                echo '</form>';
                echo '</td>';
                echo '<td>'.htmlspecialchars($m['SERIAL']).'</td>';
                echo '<td>'.htmlspecialchars($m['MANUFACTURER']).'</td>';
                echo '<td>'.htmlspecialchars($m['MODEL']).'</td>';
                echo '<td>'.htmlspecialchars($m['SIZE_INCH']).'</td>';
                echo '<td>'.htmlspecialchars($m['RESOLUTION']).'</td>';
                echo '<td>'.htmlspecialchars($m['CONNECTOR']).'</td>';
                echo '<td>'.htmlspecialchars($m['ATTACHED_TO']).'</td>';
                echo '<td>';
                echo '<form method="post">';
                echo '<input type="hidden" name="modif_id" value="'.$id.'">';
                echo '<button name="modifier">Modifier</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            ?>

        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>

</div>
</body>
</html>
