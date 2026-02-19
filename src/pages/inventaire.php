<?php
session_start();

$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if (!$connecte) die("Erreur de connexion");

/* ══ PAGINATION ══ */
$per_page = 20;
$page     = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$cnt_stmt = mysqli_prepare($connecte, "SELECT COUNT(*) FROM inventaire");
mysqli_stmt_execute($cnt_stmt);
mysqli_stmt_bind_result($cnt_stmt, $total_rows);
mysqli_stmt_fetch($cnt_stmt);
mysqli_stmt_close($cnt_stmt);

$total_pages = max(1, ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset = ($page - 1) * $per_page;

// Colonnes (hors id)
$colonnes = mysqli_query($connecte, "SHOW COLUMNS FROM inventaire");
$col_names = [];
while ($col = mysqli_fetch_assoc($colonnes)) {
    if (strtolower($col['Field']) !== 'id') $col_names[] = $col['Field'];
}

$data = mysqli_query($connecte, "SELECT * FROM inventaire LIMIT $per_page OFFSET $offset");
if (!$data) die("Erreur dans SELECT * : " . mysqli_error($connecte));

function render_pagination_inv($page, $total_pages) {
    if ($total_pages <= 1) return '';
    $html = '<div class="pagination">';
    $html .= $page > 1
        ? '<a class="prev-next" href="inventaire.php?page='.($page-1).'">‹ Préc.</a>'
        : '<span class="prev-next disabled">‹ Préc.</span>';
    $shown = array_unique(array_merge([1,$total_pages], range(max(2,$page-2), min($total_pages-1,$page+2))));
    sort($shown);
    $prev = null;
    foreach ($shown as $p) {
        if ($prev !== null && $p - $prev > 1) $html .= '<span class="ellipsis">…</span>';
        $html .= $p === $page
            ? '<span class="active-page">'.$p.'</span>'
            : '<a href="inventaire.php?page='.$p.'">'.$p.'</a>';
        $prev = $p;
    }
    $html .= $page < $total_pages
        ? '<a class="prev-next" href="inventaire.php?page='.($page+1).'">Suiv. ›</a>'
        : '<span class="prev-next disabled">Suiv. ›</span>';
    return $html . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Inventaire</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <?php include("../pages/header.php"); ?>
    <div class="main-container">
        <?php include("navbar.php"); ?>
        <div class="contenu">

            <h3>Inventaire
                <small style="font-weight:normal;font-size:13px;color:#666;">
                    — <?= $total_rows ?> machine(s), page <?= $page ?>/<?= $total_pages ?>
                </small>
            </h3>

            <table border="1" cellpadding="5">
                <thead>
                <tr>
                    <?php foreach ($col_names as $name): ?>
                        <th><?= htmlspecialchars($name) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php while ($ligne = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <?php foreach ($ligne as $cle => $valeur): ?>
                        <?php if (strtolower($cle) !== 'id'): ?>
                            <td><?= htmlspecialchars($valeur) ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?= render_pagination_inv($page, $total_pages) ?>

        </div>
    </div>
    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>
</body>
</html>