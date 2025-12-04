<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
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
            $connecte = mysqli_connect("localhost", "root", "", "rpiBD");
            if (!$connecte) {
                die("Erreur de connexion à la base");
            }

            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter un technicien</h2>';
                echo '<form method="post">';
                echo '<table>';
                echo '<tr><td>Login</td><td><input type="text" name="login"></td></tr>';
                echo '<tr><td>Password</td><td><input type="text" name="password"></td></tr>';
                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd">Ajouter</button></td></tr>';
                echo '<tr><td colspan="2"><button><a href="utilisateur.php">Annuler</a></button></td></tr>';
                echo '</table>';
                echo '</form>';
            }

            // --- Confirmation ajout ---
            if (isset($_POST['ajouter_bd'])) {
                $login = $_POST['login'];
                $password = $_POST['password'];

                $sql = "INSERT INTO user (login, password) VALUES (?, ?)";
                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $login, $password);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<p style='color:green;'>Technicien ajouté avec succès !</p>";
                } else {
                    echo "<p style='color:red;'>Erreur lors de l'ajout : " . mysqli_error($connecte) . "</p>";
                }
                mysqli_stmt_close($stmt);
            }

            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM user WHERE id=$id");
                $utilisateur = mysqli_fetch_assoc($res);

                if ($utilisateur) {
                    echo '<h2>Modifier utilisateur</h2>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="id" value="' . $utilisateur['id'] . '">';
                    echo '<table>';
                    echo '<tr><td>Login</td><td><input type="text" name="login" value="' . $utilisateur['login'] . '"></td></tr>';
                    echo '<tr><td>Password</td><td><input type="text" name="password" value="' . $utilisateur['password'] . '"></td></tr>';
                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour">Modifier</button></td></tr>';
                    echo '<tr><td colspan="2"><button><a href="utilisateur.php">Annuler</a></button></td></tr>';
                    echo '</table>';
                    echo '</form>';
                }
            }

            if (isset($_POST['mise_a_jour'])) {
                $id = intval($_POST['id']);
                $login = $_POST['login'];
                $password = $_POST['password'];

                $sql = "UPDATE user SET login=?, password=? WHERE id=?";
                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param($stmt, "ssi", $login, $password, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                echo "<p style='color:green;'>Utilisateur modifié avec succès !</p>";
            }

            if (isset($_POST['supprimer'])) {
                $id = intval($_POST['suppr_id']);
                // Verif login pour ne pas supprimer adminweb
                $res = mysqli_query($connecte, "SELECT login FROM user WHERE id=$id");
                $utilisateur = mysqli_fetch_assoc($res);
                if ($utilisateur['login'] != 'adminweb') {
                    mysqli_query($connecte, "DELETE FROM user WHERE id=$id");
                    echo "<p style='color:green;'>Utilisateur supprimé avec succès !</p>";
                } else {
                    echo "<p style='color:red;'>Impossible de supprimer adminweb !</p>";
                }
            }
            ?>

            <h3>Liste des utilisateurs</h3>
            <table border="1">
                <thead>
                <tr>
                    <th>
                        <form method="post">
                            <button type="submit" name="ajout">Ajouter</button>
                        </form>
                    </th>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query($connecte, "SELECT * FROM user");
                while ($ligne = mysqli_fetch_assoc($data)) {
                    echo "<tr>";

                    echo "<td>";
                    if ($ligne['login'] != 'adminweb') {
                        echo '<form method="post">';
                        echo '<input type="hidden" name="suppr_id" value="' . $ligne['id'] . '">';
                        echo '<button type="submit" name="supprimer">Supprimer</button>';
                        echo '</form>';
                    } else {
                        echo "Protégé";
                    }
                    echo "</td>";

                    echo "<td>" . $ligne['id'] . "</td>";
                    echo "<td>" . $ligne['login'] . "</td>";
                    echo "<td>" . $ligne['password'] . "</td>";

                    echo "<td>";
                    if ($ligne['login'] != 'adminweb') {
                        echo '<form method="post">';
                        echo '<input type="hidden" name="modif_id" value="' . $ligne['id'] . '">';
                        echo '<button type="submit" name="modifier">Modifier</button>';
                        echo '</form>';
                    } else {
                        echo "Protégé";
                    }
                    echo "</td>";

                    echo "</tr>";
                }
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
