<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: accueil.php");
    exit;
}
if ($_SESSION['role'] !== 'technicien' && $_SESSION['role'] !== 'adminweb') {
    header("Location: accueil.php");
    exit;
}

$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if (!$connecte) die("Erreur de connexion");

/* ══ EXPORT CSV ══ */
if (isset($_GET['export_csv'])) {
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=moniteurs_export.csv');
    header('Pragma: no-cache');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['SERIAL','MANUFACTURER','MODEL','SIZE_INCH','RESOLUTION','CONNECTOR','ATTACHED_TO']);
    $r = mysqli_query($connecte, "SELECT SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO FROM moniteur");
    while ($row = mysqli_fetch_assoc($r)) fputcsv($out, $row);
    fclose($out);
    exit();
}

/* ══ IMPORT CSV ══ */
$import_message = '';
if (isset($_POST['import_csv']) && isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $sep   = isset($_POST['separateur'])  ? $_POST['separateur']                : ',';
    $start = isset($_POST['ligne_debut']) ? max(1, intval($_POST['ligne_debut'])) : 2;
    $handle = fopen($_FILES['csvFile']['tmp_name'], "r");
    $cur = 0; $imported = 0;
    while (($d = fgetcsv($handle, 2000, $sep)) !== FALSE) {
        $cur++;
        if ($cur < $start) continue;
        if (count($d) < 7) continue;
        $stmt = mysqli_prepare($connecte, "INSERT INTO moniteur (SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO) VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssisss", $d[0],$d[1],$d[2],$d[3],$d[4],$d[5],$d[6]);
        if (mysqli_stmt_execute($stmt)) $imported++;
        mysqli_stmt_close($stmt);
    }
    fclose($handle);
    $import_message = "<p style='color:green;'>Importation réussie : $imported ligne(s) insérée(s).</p>";
}

/* ══ SUPPRESSION ══ */
if (isset($_POST['supprimer'])) {
    $id = intval($_POST['suppr_id']);
    $res = mysqli_query($connecte, "SELECT * FROM moniteur WHERE ID=$id");
    if ($res && mysqli_num_rows($res) > 0) {
        $m = mysqli_fetch_assoc($res);
        $stmt = mysqli_prepare($connecte, "INSERT INTO rebut_moniteur (SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO) VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssisss", $m['SERIAL'],$m['MANUFACTURER'],$m['MODEL'],$m['SIZE_INCH'],$m['RESOLUTION'],$m['CONNECTOR'],$m['ATTACHED_TO']);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_query($connecte, "DELETE FROM moniteur WHERE ID=$id");
            header("Location: moniteur.php"); exit;
        }
    }
}

/* ══ MISE À JOUR ══ */
$update_message = '';
if (isset($_POST['mise_a_jour'])) {
    $id = $_POST['id']; $serial = $_POST['SERIAL']; $attached_to = $_POST['ATTACHED_TO'];
    $chk = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE SERIAL=? AND ID!=?");
    mysqli_stmt_bind_param($chk, "si", $serial, $id);
    mysqli_stmt_execute($chk); mysqli_stmt_bind_result($chk, $cs); mysqli_stmt_fetch($chk); mysqli_stmt_close($chk);
    $ca = 0;
    if (!empty($attached_to)) {
        $chk2 = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE ATTACHED_TO=? AND ID!=?");
        mysqli_stmt_bind_param($chk2, "si", $attached_to, $id);
        mysqli_stmt_execute($chk2); mysqli_stmt_bind_result($chk2, $ca); mysqli_stmt_fetch($chk2); mysqli_stmt_close($chk2);
    }
    if ($cs > 0)     $update_message = "<p style='color:red;'>Ce SERIAL est déjà utilisé !</p>";
    elseif ($ca > 0) $update_message = "<p style='color:red;'>Cet ATTACHED_TO est déjà utilisé !</p>";
    else {
        $stmt = mysqli_prepare($connecte, "UPDATE moniteur SET SERIAL=?,MANUFACTURER=?,MODEL=?,SIZE_INCH=?,RESOLUTION=?,CONNECTOR=?,ATTACHED_TO=? WHERE ID=?");
        mysqli_stmt_bind_param($stmt, "sssisssi", $serial,$_POST['MANUFACTURER'],$_POST['MODEL'],$_POST['SIZE_INCH'],$_POST['RESOLUTION'],$_POST['CONNECTOR'],$attached_to,$id);
        mysqli_stmt_execute($stmt);
        $update_message = "<p style='color:green;'>Moniteur mis à jour.</p>";
    }
}

/* ══ AJOUT ══ */
$add_message = '';
if (isset($_POST['ajouter_bd'])) {
    $serial = $_POST['SERIAL']; $attached_to = $_POST['ATTACHED_TO'];
    $chk = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE SERIAL=?");
    mysqli_stmt_bind_param($chk, "s", $serial);
    mysqli_stmt_execute($chk); mysqli_stmt_bind_result($chk, $cs); mysqli_stmt_fetch($chk); mysqli_stmt_close($chk);
    $ca = 0;
    if (!empty($attached_to)) {
        $chk2 = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur WHERE ATTACHED_TO=?");
        mysqli_stmt_bind_param($chk2, "s", $attached_to);
        mysqli_stmt_execute($chk2); mysqli_stmt_bind_result($chk2, $ca); mysqli_stmt_fetch($chk2); mysqli_stmt_close($chk2);
    }
    if ($cs > 0)     $add_message = "<p style='color:red;'>Ce SERIAL existe déjà !</p>";
    elseif ($ca > 0) $add_message = "<p style='color:red;'>Cet ATTACHED_TO est déjà utilisé !</p>";
    else {
        $stmt = mysqli_prepare($connecte, "INSERT INTO moniteur (SERIAL,MANUFACTURER,MODEL,SIZE_INCH,RESOLUTION,CONNECTOR,ATTACHED_TO) VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssisss", $serial,$_POST['MANUFACTURER'],$_POST['MODEL'],$_POST['SIZE_INCH'],$_POST['RESOLUTION'],$_POST['CONNECTOR'],$attached_to);
        $add_message = mysqli_stmt_execute($stmt) ? "<p style='color:green;'>Moniteur ajouté !</p>" : "<p style='color:red;'>Erreur : ".mysqli_error($connecte)."</p>";
    }
}

/* ══ FILTRES, TRI & PAGINATION ══ */
$per_page = 20;
$page     = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$allowed_sort = ['SERIAL','MANUFACTURER','MODEL','SIZE_INCH','RESOLUTION','CONNECTOR','ATTACHED_TO'];
$sort_col = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort) ? $_GET['sort'] : 'SERIAL';
$sort_dir = isset($_GET['dir'])  && $_GET['dir'] === 'DESC' ? 'DESC' : 'ASC';

$filters = [
    'f_manufacturer' => ['col' => 'MANUFACTURER', 'val' => ''],
    'f_model'        => ['col' => 'MODEL',        'val' => ''],
    'f_resolution'   => ['col' => 'RESOLUTION',   'val' => ''],
    'f_connector'    => ['col' => 'CONNECTOR',     'val' => ''],
    'f_attached'     => ['col' => 'ATTACHED_TO',   'val' => ''],
];
foreach ($filters as $key => &$f) $f['val'] = isset($_GET[$key]) ? trim($_GET[$key]) : '';
unset($f);

$where_parts = []; $where_params = []; $where_types = '';
foreach ($filters as $f) {
    if ($f['val'] !== '') {
        $where_parts[] = "`{$f['col']}` = ?";
        $where_params[] = $f['val'];
        $where_types   .= 's';
    }
}
$where_sql = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$cnt_stmt = mysqli_prepare($connecte, "SELECT COUNT(*) FROM moniteur $where_sql");
if ($where_types) mysqli_stmt_bind_param($cnt_stmt, $where_types, ...$where_params);
mysqli_stmt_execute($cnt_stmt);
mysqli_stmt_bind_result($cnt_stmt, $total_rows);
mysqli_stmt_fetch($cnt_stmt);
mysqli_stmt_close($cnt_stmt);

$total_pages = max(1, ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset = ($page - 1) * $per_page;

$data_stmt = mysqli_prepare($connecte, "SELECT * FROM moniteur $where_sql ORDER BY `$sort_col` $sort_dir LIMIT $per_page OFFSET $offset");
if ($where_types) mysqli_stmt_bind_param($data_stmt, $where_types, ...$where_params);
mysqli_stmt_execute($data_stmt);
$data_result = mysqli_stmt_get_result($data_stmt);

function get_distinct_mon($connecte, $col) {
    $r = mysqli_query($connecte, "SELECT DISTINCT `$col` FROM moniteur WHERE `$col` IS NOT NULL AND `$col`!='' ORDER BY `$col` ASC");
    $vals = [];
    while ($row = mysqli_fetch_row($r)) $vals[] = $row[0];
    return $vals;
}
$lists = [];
foreach (['MANUFACTURER','MODEL','RESOLUTION','CONNECTOR','ATTACHED_TO'] as $col)
    $lists[$col] = get_distinct_mon($connecte, $col);

$inv_models = [];
$rm = mysqli_query($connecte, "SELECT MODEL FROM inventaire ORDER BY MODEL ASC");
while ($row = mysqli_fetch_assoc($rm)) $inv_models[] = $row['MODEL'];

function build_qs_mon($overrides = []) {
    $keys = ['f_manufacturer','f_model','f_resolution','f_connector','f_attached','sort','dir','page'];
    $params = [];
    foreach ($keys as $k) {
        $val = array_key_exists($k, $overrides) ? $overrides[$k] : (isset($_GET[$k]) ? $_GET[$k] : '');
        if ($val !== '') $params[$k] = $val;
    }
    return 'moniteur.php' . (count($params) ? '?' . http_build_query($params) : '');
}

function sort_link_mon($col, $label, $sort_col, $sort_dir) {
    $is_active = ($col === $sort_col);
    $new_dir   = ($is_active && $sort_dir === 'ASC') ? 'DESC' : 'ASC';
    $arrow     = $is_active ? ($sort_dir === 'ASC' ? ' ▲' : ' ▼') : '';
    $url   = build_qs_mon(['sort' => $col, 'dir' => $new_dir, 'page' => '1']);
    $class = $is_active ? 'th-sort th-sort-active' : 'th-sort';
    return '<th class="'.$class.'"><a href="'.$url.'">'.htmlspecialchars($label).$arrow.'</a></th>';
}

function render_pagination_mon($page, $total_pages) {
    if ($total_pages <= 1) return '';
    $html = '<div class="pagination">';
    $html .= $page > 1
        ? '<a class="prev-next" href="'.build_qs_mon(['page'=>$page-1]).'">‹ Préc.</a>'
        : '<span class="prev-next disabled">‹ Préc.</span>';
    $shown = array_unique(array_merge([1,$total_pages], range(max(2,$page-2), min($total_pages-1,$page+2))));
    sort($shown);
    $prev = null;
    foreach ($shown as $p) {
        if ($prev !== null && $p - $prev > 1) $html .= '<span class="ellipsis">…</span>';
        $html .= $p === $page
            ? '<span class="active-page">'.$p.'</span>'
            : '<a href="'.build_qs_mon(['page'=>$p]).'">'.$p.'</a>';
        $prev = $p;
    }
    $html .= $page < $total_pages
        ? '<a class="prev-next" href="'.build_qs_mon(['page'=>$page+1]).'">Suiv. ›</a>'
        : '<span class="prev-next disabled">Suiv. ›</span>';
    return $html . '</div>';
}
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
                <span class="gestion-btn gestion-btn-active" aria-current="page">Moniteurs</span>
            </div>

            <?php
            /* ── Formulaire modification ── */
            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM moniteur WHERE ID=$id");
                $mon = mysqli_fetch_assoc($res);
                if ($mon) {
                    echo '<h2>Modifier un moniteur</h2>';
                    echo '<form method="post"><input type="hidden" name="id" value="'.$mon['ID'].'"><table><tbody>';
                    foreach (['SERIAL','MANUFACTURER','MODEL','RESOLUTION','CONNECTOR'] as $f)
                        echo '<tr><th>'.$f.'</th><td><input type="text" name="'.$f.'" value="'.htmlspecialchars($mon[$f]).'"></td></tr>';
                    echo '<tr><th>SIZE_INCH</th><td><input type="number" name="SIZE_INCH" value="'.htmlspecialchars($mon['SIZE_INCH']).'"></td></tr>';
                    echo '<tr><th>ATTACHED_TO</th><td><select name="ATTACHED_TO"><option value="">-- Non rattaché --</option>';
                    foreach ($inv_models as $mdl) { $s=($mdl==$mon['ATTACHED_TO'])?'selected':''; echo '<option value="'.htmlspecialchars($mdl).'" '.$s.'>'.htmlspecialchars($mdl).'</option>'; }
                    echo '</select></td></tr>';
                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour">Enregistrer</button> <a href="moniteur.php" class="btn-reset">Annuler</a></td></tr>';
                    echo '</tbody></table></form>';
                }
            }

            /* ── Formulaire ajout ── */
            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter un moniteur</h2>';
                echo '<form method="post"><table><tbody>';
                foreach (['SERIAL','MANUFACTURER','MODEL','RESOLUTION','CONNECTOR'] as $f)
                    echo '<tr><th>'.$f.'</th><td><input type="text" name="'.$f.'"></td></tr>';
                echo '<tr><th>SIZE_INCH</th><td><input type="number" name="SIZE_INCH"></td></tr>';
                echo '<tr><th>ATTACHED_TO</th><td><select name="ATTACHED_TO"><option value="">-- Non rattaché --</option>';
                foreach ($inv_models as $mdl) echo '<option value="'.htmlspecialchars($mdl).'">'.htmlspecialchars($mdl).'</option>';
                echo '</select></td></tr>';
                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd">Ajouter</button> <a href="moniteur.php" class="btn-reset">Annuler</a></td></tr>';
                echo '</tbody></table></form>';
            }

            echo $import_message . $update_message . $add_message;
            ?>

            <h3>Liste des moniteurs
                <small style="font-weight:normal;font-size:13px;color:#666;">
                    — <?= $total_rows ?> résultat(s), page <?= $page ?>/<?= $total_pages ?>
                </small>
            </h3>

            <!-- ══ BARRE D'OUTILS ══ -->
            <form method="get" action="moniteur.php">
                <?php if ($sort_col !== 'SERIAL'): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($sort_col) ?>"><?php endif; ?>
                <?php if ($sort_dir !== 'ASC'):    ?><input type="hidden" name="dir"  value="<?= htmlspecialchars($sort_dir) ?>"><?php endif; ?>

                <div class="table-toolbar">
                    <div class="toolbar-filters">
                        <div class="toolbar-filters-row">
                            <?php
                            $filter_labels = [
                                'f_manufacturer' => ['Marque',     'MANUFACTURER'],
                                'f_model'        => ['Modèle',     'MODEL'],
                                'f_resolution'   => ['Résolution', 'RESOLUTION'],
                                'f_connector'    => ['Connecteur', 'CONNECTOR'],
                                'f_attached'     => ['Rattaché à', 'ATTACHED_TO'],
                            ];
                            foreach ($filter_labels as $param => [$label, $col]):
                                $cur_val = $filters[$param]['val'];
                            ?>
                            <div class="toolbar-filter-group">
                                <label><?= $label ?></label>
                                <select name="<?= $param ?>">
                                    <option value="">Tous</option>
                                    <?php foreach ($lists[$col] as $v): ?>
                                        <option value="<?= htmlspecialchars($v) ?>" <?= $cur_val===$v?'selected':'' ?>>
                                            <?= htmlspecialchars($v) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endforeach; ?>

                            <div class="toolbar-right">
                                <div class="toolbar-filter-group">
                                    <label>&nbsp;</label>
                                    <a href="#overlay-import-csv" class="csv">Importer CSV</a>
                                </div>
                                <div class="toolbar-filter-group">
                                    <label>&nbsp;</label>
                                    <a href="moniteur.php?export_csv=1" class="csv2">Exporter CSV</a>
                                </div>
                            </div>
                        </div>

                        <div class="toolbar-actions-row">
                            <button type="submit" class="btn-filter">Filtrer</button>
                            <a href="moniteur.php" class="btn-reset">Réinitialiser</a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Bouton Ajouter : form POST totalement indépendant -->
            <form method="post" action="moniteur.php" style="text-align:right; margin-bottom: 6px;">
                <button type="submit" name="ajout" class="btn-ajouter-bas">+ Ajouter</button>
            </form>

            <!-- ══ TABLEAU ══ -->
            <table border="1">
                <thead>
                <tr>
                    <?php
                    $cols = ['SERIAL','MANUFACTURER','MODEL','SIZE_INCH','RESOLUTION','CONNECTOR','ATTACHED_TO'];
                    foreach ($cols as $c) echo sort_link_mon($c, $c, $sort_col, $sort_dir);
                    ?>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($m = mysqli_fetch_assoc($data_result)): $id = $m['ID']; ?>
                <tr>
                    <td><?= htmlspecialchars($m['SERIAL']) ?></td>
                    <td><?= htmlspecialchars($m['MANUFACTURER']) ?></td>
                    <td><?= htmlspecialchars($m['MODEL']) ?></td>
                    <td><?= htmlspecialchars($m['SIZE_INCH']) ?></td>
                    <td><?= htmlspecialchars($m['RESOLUTION']) ?></td>
                    <td><?= htmlspecialchars($m['CONNECTOR']) ?></td>
                    <td><?= htmlspecialchars($m['ATTACHED_TO']) ?></td>
                    <td class="action-icons">
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="modif_id" value="<?= $id ?>">
                            <button type="submit" name="modifier" class="btn-icone">
                                <img src="../img/crayon.png" alt="Modifier" title="Modifier">
                            </button>
                        </form>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                            <input type="hidden" name="suppr_id" value="<?= $id ?>">
                            <button type="submit" name="supprimer" class="btn-icone">
                                <img src="../img/poubelle.png" alt="Supprimer" title="Supprimer">
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?= render_pagination_mon($page, $total_pages) ?>

        </div>
    </div>
    <footer><p>&copy; 2025 - Projet SAE - Groupe X</p></footer>
</div>

<!-- ══ OVERLAY IMPORT CSV ══ -->
<div class="overlay-csv" id="overlay-import-csv">
    <div class="overlay-box">
        <h3>Importer un fichier CSV</h3>
        <form method="post" enctype="multipart/form-data">
            <label>Fichier CSV</label>
            <input type="file" name="csvFile" accept=".csv" required>

            <label>Séparateur</label>
            <select name="separateur">
                <option value=",">, (virgule)</option>
                <option value=";">; (point-virgule)</option>
                <option value="&#9;">⇥ (tabulation)</option>
                <option value="|">| (pipe)</option>
            </select>

            <label>Ligne de départ (ex : 2 pour ignorer l'en-tête)</label>
            <input type="number" name="ligne_debut" value="2" min="1">

            <div class="overlay-actions">
                <a href="moniteur.php" class="btn-annuler">Annuler</a>
                <button type="submit" name="import_csv" class="btn-importer">Importer</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>