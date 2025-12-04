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

            /* ───────────────────────────────────────────── */
            /*                CONNEXION BD                   */
            /* ───────────────────────────────────────────── */

            $connecte = mysqli_connect("localhost", "root", "", "rpiBD");
            if (!$connecte) { die("Erreur de connexion"); }

            /* ───────────────────────────────────────────── */
            /*         TRAITEMENT FORMULAIRE MODIFIER        */
            /* ───────────────────────────────────────────── */

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
                    echo '<tr><td>ATTACHED_TO</td><td><input type="text" name="ATTACHED_TO" value="'.$moniteur['ATTACHED_TO'].'"></td></tr>';

                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour" class="btn-valider">Modifier</button></td></tr>';

                    echo '</tbody></table></form>';
                }
            }

            /* ───────────────────────────────────────────── */
            /*              TRAITEMENT SUPPRESSION           */
            /* ───────────────────────────────────────────── */

            if (isset($_POST['supprimer'])) {
                $id = intval($_POST['suppr_id']);
                mysqli_query($connecte, "DELETE FROM moniteur WHERE ID=$id");
                header("Location: moniteur.php");
                exit;
            }

            /* ───────────────────────────────────────────── */
            /*              FORMULAIRE AJOUT                 */
            /* ───────────────────────────────────────────── */

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
                echo '<tr><td>ATTACHED_TO</td><td><input type="text" name="ATTACHED_TO"></td></tr>';

                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd" class="btn-valider">Ajouter</button></td></tr>';
                echo '<tr><td colspan="2"><button type="button" onclick="window.location.href=\'moniteur.php\'">Annuler</button></td></tr>';

                echo '</tbody></table>';
                echo '</form>';
            }

            /* ───────────────────────────────────────────── */
            /*            ENREGISTREMENT EN BASE             */
            /* ───────────────────────────────────────────── */

            if (isset($_POST['ajouter_bd'])) {

                $sql = "INSERT INTO moniteur (SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param($stmt, "sssisss",
                    $_POST['SERIAL'], $_POST['MANUFACTURER'], $_POST['MODEL'],
                    $_POST['SIZE_INCH'], $_POST['RESOLUTION'], $_POST['CONNECTOR'],
                    $_POST['ATTACHED_TO']
                );

                if (mysqli_stmt_execute($stmt))
                    echo "<p style='color:green;'>Moniteur ajouté avec succès !</p>";
                else
                    echo "<p style='color:red;'>Erreur : ".mysqli_error($connecte)."</p>";
            }

            /* ───────────────────────────────────────────── */
            /*                 MISE À JOUR                    */
            /* ───────────────────────────────────────────── */

            if (isset($_POST['mise_a_jour'])) {

                $sql = "UPDATE moniteur SET SERIAL=?, MANUFACTURER=?, MODEL=?, SIZE_INCH=?, RESOLUTION=?, CONNECTOR=?, ATTACHED_TO=? WHERE ID=?";

                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param($stmt, "sssisssi",
                    $_POST['SERIAL'], $_POST['MANUFACTURER'], $_POST['MODEL'],
                    $_POST['SIZE_INCH'], $_POST['RESOLUTION'], $_POST['CONNECTOR'],
                    $_POST['ATTACHED_TO'], $_POST['id']
                );

                mysqli_stmt_execute($stmt);
                echo "<p style='color:green;'>Moniteur mis à jour avec succès !</p>";
            }

            ?>

            <h3>Liste des moniteurs</h3>

            <div class="csv_box">
                <label for="csvFile" class="csv">Importer CSV</label>
                <input type="file" id="csvFile" accept=".csv" style="display:none;">
                <a href="#" class="csv2">Exporter CSV</a>
            </div>

            <?php
            /* ───────────────────────────────────────────── */
            /*      AFFICHAGE DU TABLEAU DES MONITEURS       */
            /* ───────────────────────────────────────────── */

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
