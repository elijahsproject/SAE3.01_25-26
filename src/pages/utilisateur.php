<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'adminweb') {
    header("Location: accueil.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion utilisateurs</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <?php include("../pages/header.php"); ?>

    <div class="main-container">
        <?php include("navbar.php"); ?>

        <div class="contenu">
            <?php
            $connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
            if (!$connecte) {
                die("Erreur de connexion à la base");
            }

            /* ================= AJOUT UTILISATEUR ================= */
            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter un utilisateur</h2>';
                echo '<form method="post"><table>';

                echo '<tr>
                        <td><label>Login</label></td>
                        <td><input type="text" name="login" required></td>
                      </tr>';

                echo '<tr>
                        <td><label>Mot de passe</label></td>
                        <td><input type="password" name="password" required></td>
                      </tr>';

                echo '<tr>
                        <td><label>Groupe</label></td>
                        <td>
                            <select name="role" required>
                                <option value="technicien">Technicien</option>
                                <option value="sysadmin">Admin Système</option>
                            </select>
                        </td>
                      </tr>';

                echo '<tr><td colspan="2"><button name="ajouter_bd">Ajouter</button></td></tr>';
                echo '</table></form>';
            }

            if (isset($_POST['ajouter_bd'])) {
                $login = $_POST['login'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                if ($role !== 'adminweb') {
                    $sql = "INSERT INTO user (login, password, role) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($connecte, $sql);
                    mysqli_stmt_bind_param($stmt, "sss", $login, $password, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    echo "<p style='color:green;'>Utilisateur ajouté</p>";
                }
            }

            /* ================= MODIFICATION ================= */
            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT id, login FROM user WHERE id=$id");
                $u = mysqli_fetch_assoc($res);

                echo '<h2>Modifier utilisateur</h2>';
                echo '<form method="post"><table>';
                echo '<input type="hidden" name="id" value="'.$u['id'].'">';

                echo '<tr>
                        <td><label>Login</label></td>
                        <td><input type="text" name="login" value="'.$u['login'].'" required></td>
                      </tr>';

                echo '<tr>
                        <td><label>Mot de passe</label></td>
                        <td><input type="password" name="password" placeholder="Nouveau mdp"></td>
                      </tr>';

                echo '<tr><td colspan="2"><button name="mise_a_jour">Modifier</button></td></tr>';
                echo '</table></form>';
            }

            if (isset($_POST['mise_a_jour'])) {
                $id = intval($_POST['id']);
                $login = $_POST['login'];
                $password = $_POST['password'];

                if (!empty($password)) {
                    $sql = "UPDATE user SET login=?, password=? WHERE id=?";
                    $stmt = mysqli_prepare($connecte, $sql);
                    mysqli_stmt_bind_param($stmt, "ssi", $login, $password, $id);
                } else {
                    $sql = "UPDATE user SET login=? WHERE id=?";
                    $stmt = mysqli_prepare($connecte, $sql);
                    mysqli_stmt_bind_param($stmt, "si", $login, $id);
                }
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                echo "<p style='color:green;'>Utilisateur modifié</p>";
            }

            /* ================= SUPPRESSION ================= */
            if (isset($_POST['supprimer'])) {
                $id = intval($_POST['suppr_id']);
                mysqli_query($connecte, "DELETE FROM user WHERE id=$id");
                echo "<p style='color:green;'>Utilisateur supprimé</p>";
            }
            ?>

            <h3>Liste des utilisateurs</h3>
            <table border="1">
                <thead>
                <tr>
                    <th><form method="post"><button name="ajout">Ajouter</button></form></th>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query($connecte, "SELECT id, login FROM user");
                while ($l = mysqli_fetch_assoc($data)) {
                    echo "<tr>";
                    echo "<td>
                            <form method='post'>
                                <input type='hidden' name='suppr_id' value='{$l['id']}'>
                                <button name='supprimer'>Supprimer</button>
                            </form>
                          </td>";
                    echo "<td>{$l['id']}</td>";
                    echo "<td>{$l['login']}</td>";
                    echo "<td>
                            <form method='post'>
                                <input type='hidden' name='modif_id' value='{$l['id']}'>
                                <button name='modifier'>Modifier</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
</body>
</html>
