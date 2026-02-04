<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: accueil.php");
    exit;
}

if ($_SESSION['role'] !== 'adminweb') {
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

            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter un utilisateur</h2>';
                echo '<form method="post">';
                echo '<table>';

                echo '<tr>
                        <td><label for="login">Login</label></td>
                        <td><input type="text" id="login" name="login" required></td>
                      </tr>';

                echo '<tr>
                        <td><label for="password">Password</label></td>
                        <td><input type="password" id="password" name="password" required></td>
                      </tr>';

                echo '<tr>
                        <td><label for="role">Groupe</label></td>
                        <td>
                            <select id="role" name="role" required>
                                <option value="technicien">Technicien</option>
                                <option value="sysadmin">Admin Système</option>
                            </select>
                        </td>
                      </tr>';

                echo '<tr>
                        <td colspan="2">
                            <button type="submit" name="ajouter_bd">Ajouter</button>
                        </td>
                      </tr>';

                echo '<tr>
                        <td colspan="2">
                            <form action="utilisateur.php" method="get">
                                <button type="submit">Annuler</button>
                            </form>
                        </td>
                      </tr>';

                echo '</table>';
                echo '</form>';
            }

            if (isset($_POST['ajouter_bd'])) {
                $login = $_POST['login'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                if ($role === 'adminweb') {
                    echo "<p style='color:red;'>Création d'un admin web interdite.</p>";
                    exit;
                }

                $sql = "INSERT INTO user (login, password, role) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($connecte, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $login, $password, $role);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<p style='color:green;'>Utilisateur ajouté avec succès !</p>";
                } else {
                    echo "<p style='color:red;'>Erreur lors de l'ajout</p>";
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
                    echo '<input type="hidden" name="id" value="'.$utilisateur['id'].'">';
                    echo '<table>';

                    echo '<tr>
                            <td><label for="login_modif">Login</label></td>
                            <td><input type="text" id="login_modif" name="login" value="'.$utilisateur['login'].'" required></td>
                          </tr>';

                    echo '<tr>
                            <td><label for="password_modif">Password</label></td>
                            <td><input type="text" id="password_modif" name="password" value="'.$utilisateur['password'].'" required></td>
                          </tr>';

                    echo '<tr>
                            <td colspan="2">
                                <button type="submit" name="mise_a_jour">Modifier</button>
                            </td>
                          </tr>';

                    echo '<tr>
                            <td colspan="2">
                                <form action="utilisateur.php" method="get">
                                    <button type="submit">Annuler</button>
                                </form>

                            </td>
                          </tr>';

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
                $res = mysqli_query($connecte, "SELECT login FROM user WHERE id=$id");
                $utilisateur = mysqli_fetch_assoc($res);

                if ($utilisateur['login'] !== 'adminweb') {
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
                    if ($ligne['login'] !== 'adminweb') {
                        echo '<form method="post">
                                <input type="hidden" name="suppr_id" value="'.$ligne['id'].'">
                                <button type="submit" name="supprimer">Supprimer</button>
                              </form>';
                    } else {
                        echo "Protégé";
                    }
                    echo "</td>";

                    echo "<td>".$ligne['id']."</td>";
                    echo "<td>".$ligne['login']."</td>";
                    echo "<td>".$ligne['password']."</td>";

                    echo "<td>";
                    if ($ligne['login'] !== 'adminweb') {
                        echo '<form method="post">
                                <input type="hidden" name="modif_id" value="'.$ligne['id'].'">
                                <button type="submit" name="modifier">Modifier</button>
                              </form>';
                    } else {
                        echo "Protégé";
                    }
                    echo "</td>";

                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>

            <h2>Ajouter un OS ou un Manufacturer</h2>
            <form method="post">
                <label for="type_ajout">Choisir le type</label>
                <select id="type_ajout" name="type_ajout" required>
                    <option value="OS">OS</option>
                    <option value="MANUFACTURER">Manufacturer</option>
                </select>

                <label for="nouvelle_valeur">Nouvelle valeur</label>
                <input type="text" id="nouvelle_valeur" name="nouvelle_valeur" required>

                <button type="submit" name="ajouter_option">Ajouter</button>
            </form>

            <?php
            if (!isset($_SESSION['options_suppl'])) {
                $_SESSION['options_suppl'] = [
                        'OS' => [],
                        'MANUFACTURER' => []
                ];
            }

            if (isset($_POST['ajouter_option'])) {
                $type = $_POST['type_ajout'];
                $valeur = trim($_POST['nouvelle_valeur']);

                if ($valeur !== "") {
                    $check_sql = "SELECT COUNT(*) FROM inventaire WHERE $type = ?";
                    $stmt = mysqli_prepare($connecte, $check_sql);
                    mysqli_stmt_bind_param($stmt, "s", $valeur);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $count);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);

                    if ($count == 0 && !in_array($valeur, $_SESSION['options_suppl'][$type])) {
                        $_SESSION['options_suppl'][$type][] = $valeur;
                        echo "<p style='color:green;'>Ajout réussi</p>";
                    } else {
                        echo "<p style='color:red;'>Valeur déjà existante</p>";
                    }
                }
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>
</body>
</html>
