<?php
session_start();
require_once "chacha20.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'adminweb') {
    header("Location: accueil.php");
    exit;
}

$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if (!$connecte) die("Erreur de connexion à la base");


$add_message = '';

if (isset($_POST['ajouter_bd'])) {

    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role !== 'adminweb') {

        $password_chiffre = chiffrer_chacha20($password);

        $stmt = mysqli_prepare($connecte,
                "INSERT INTO user (login,password,role) VALUES (?,?,?)"
        );

        mysqli_stmt_bind_param($stmt, "sss",
                $login,
                $password_chiffre,
                $role
        );

        if (mysqli_stmt_execute($stmt)) {
            $add_message = "<p style='color:green;'>Utilisateur ajouté (mot de passe chiffré).</p>";
        }

        mysqli_stmt_close($stmt);
    }
}



$update_message = '';

if (isset($_POST['mise_a_jour'])) {

    $id = intval($_POST['id']);
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (!empty($password)) {

        $password_chiffre = chiffrer_chacha20($password);

        $stmt = mysqli_prepare($connecte,
                "UPDATE user SET login=?, password=? WHERE id=?"
        );

        mysqli_stmt_bind_param($stmt,
                "ssi",
                $login,
                $password_chiffre,
                $id
        );

    } else {

        $stmt = mysqli_prepare($connecte,
                "UPDATE user SET login=? WHERE id=?"
        );

        mysqli_stmt_bind_param($stmt,
                "si",
                $login,
                $id
        );
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $update_message = "<p style='color:green;'>Utilisateur modifié.</p>";
}



$delete_message = '';

if (isset($_POST['supprimer'])) {

    $id = intval($_POST['suppr_id']);

    $chk = mysqli_query($connecte,
            "SELECT role FROM user WHERE id=$id"
    );

    $u = mysqli_fetch_assoc($chk);

    if ($u && $u['role'] !== 'adminweb') {

        mysqli_query($connecte,
                "DELETE FROM user WHERE id=$id"
        );

        $delete_message = "<p style='color:green;'>Utilisateur supprimé.</p>";

    } else {

        $delete_message = "<p style='color:red;'>Impossible de supprimer un compte adminweb.</p>";
    }
}

$per_page = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$cnt_stmt = mysqli_prepare($connecte, "SELECT COUNT(*) FROM user");
mysqli_stmt_execute($cnt_stmt);
mysqli_stmt_bind_result($cnt_stmt, $total_rows);
mysqli_stmt_fetch($cnt_stmt);
mysqli_stmt_close($cnt_stmt);

$total_pages = max(1, ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $per_page;

$data = mysqli_query($connecte,
        "SELECT id,login,role FROM user LIMIT $per_page OFFSET $offset"
);


function render_pagination_usr($page, $total_pages) {

    if ($total_pages <= 1) return '';

    $html = '<div class="pagination">';

    $html .= $page > 1
            ? '<a class="prev-next" href="utilisateur.php?page='.($page-1).'">‹ Préc.</a>'
            : '<span class="prev-next disabled">‹ Préc.</span>';

    $shown = array_unique(array_merge(
            [1,$total_pages],
            range(max(2,$page-2), min($total_pages-1,$page+2))
    ));

    sort($shown);

    $prev = null;

    foreach ($shown as $p) {

        if ($prev !== null && $p - $prev > 1)
            $html .= '<span class="ellipsis">…</span>';

        $html .= $p === $page
                ? '<span class="active-page">'.$p.'</span>'
                : '<a href="utilisateur.php?page='.$p.'">'.$p.'</a>';

        $prev = $p;
    }

    $html .= $page < $total_pages
            ? '<a class="prev-next" href="utilisateur.php?page='.($page+1).'">Suiv. ›</a>'
            : '<span class="prev-next disabled">Suiv. ›</span>';

    return $html . '</div>';
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
            /* ── Formulaire ajout ── */
            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter un utilisateur</h2>';
                echo '<form method="post"><table><tbody>';
                echo '<tr><th><label for="login">Login</label></th><td><input type="text" id="login" name="login" required></td></tr>';
                echo '<tr><th><label for="password">Mot de passe</label></th><td><input type="password" id="password" name="password" required></td></tr>';
                echo '<tr><th><label for="role">Rôle</label></th><td><select id="role" name="role">
                    <option value="technicien">Technicien</option>
                    <option value="sysadmin">Admin Système</option>
                </select></td></tr>';
                echo '<tr><td colspan="2"><button name="ajouter_bd">Ajouter</button> <a href="utilisateur.php" class="btn-reset">Annuler</a></td></tr>';
                echo '</tbody></table></form>';
            }

            /* ── Formulaire modification ── */
            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT id,login FROM user WHERE id=$id");
                $u = mysqli_fetch_assoc($res);
                if ($u) {
                    echo '<h2>Modifier utilisateur</h2>';
                    echo '<form method="post"><input type="hidden" name="id" value="'.$u['id'].'"><table><tbody>';
                    echo '<tr><th><label>Login</label></th><td><input type="text" name="login" value="'.htmlspecialchars($u['login']).'" required></td></tr>';
                    echo '<tr><th><label>Mot de passe</label></th><td><input type="password" name="password" placeholder="Laisser vide pour ne pas changer"></td></tr>';
                    echo '<tr><td colspan="2"><button name="mise_a_jour">Modifier</button> <a href="utilisateur.php" class="btn-reset">Annuler</a></td></tr>';
                    echo '</tbody></table></form>';
                }
            }

            echo $add_message . $update_message . $delete_message;
            ?>

            <h3>Liste des utilisateurs
                <small style="font-weight:normal;font-size:13px;color:#666;">
                    — <?= $total_rows ?> résultat(s), page <?= $page ?>/<?= $total_pages ?>
                </small>
            </h3>

            <table border="1">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($l = mysqli_fetch_assoc($data)): $isAdmin = ($l['role'] === 'adminweb'); ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td><?= htmlspecialchars($l['login']) ?></td>
                    <td><?= htmlspecialchars($l['role']) ?></td>
                    <td class="action-icons">
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="modif_id" value="<?= $l['id'] ?>">
                            <button type="submit" name="modifier" class="btn-icone">
                                <img src="../img/crayon.png" alt="Modifier" title="Modifier">
                            </button>
                        </form>
                        <?php if ($isAdmin): ?>
                            &nbsp;<span class="protege">Protégé</span>
                        <?php else: ?>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                                <input type="hidden" name="suppr_id" value="<?= $l['id'] ?>">
                                <button type="submit" name="supprimer" class="btn-icone">
                                    <img src="../img/poubelle.png" alt="Supprimer" title="Supprimer">
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?= render_pagination_usr($page, $total_pages) ?>

            <form method="post">
                <button type="submit" name="ajout" class="btn-ajouter-bas">+ Ajouter</button>
            </form>

        </div>
    </div>
</div>
</body>
</html>