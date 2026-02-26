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

/* ══ EXPORT CSV — avant tout output ══ */
if (isset($_GET['export_csv'])) {
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inventaire_export.csv');
    header('Pragma: no-cache');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['NAME','SERIAL','MANUFACTURER','MODEL','TYPE','CPU','RAM_MB','DISK_GB','OS','DOMAIN','LOCATION','BUILDING','ROOM','MACADDR','PURCHASE_DATE','WARRANTY_END']);
    $r = mysqli_query($connecte, "SELECT NAME,SERIAL,MANUFACTURER,MODEL,TYPE,CPU,RAM_MB,DISK_GB,OS,DOMAIN,LOCATION,BUILDING,ROOM,MACADDR,PURCHASE_DATE,WARRANTY_END FROM inventaire");
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
        if (count($d) < 16) continue;
        $stmt = mysqli_prepare($connecte, "INSERT INTO inventaire (NAME,SERIAL,MANUFACTURER,MODEL,TYPE,CPU,RAM_MB,DISK_GB,OS,DOMAIN,LOCATION,BUILDING,ROOM,MACADDR,PURCHASE_DATE,WARRANTY_END) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssssiiissssssss", $d[0],$d[1],$d[2],$d[3],$d[4],$d[5],$d[6],$d[7],$d[8],$d[9],$d[10],$d[11],$d[12],$d[13],$d[14],$d[15]);
        if (mysqli_stmt_execute($stmt)) $imported++;
        mysqli_stmt_close($stmt);
    }
    fclose($handle);
    $import_message = "<p style='color:green;'>Importation réussie : $imported ligne(s) insérée(s).</p>";
}

/* ══ SUPPRESSION ══ */
if (isset($_POST['supprimer'])) {
    $id = intval($_POST['suppr_id']);
    $res = mysqli_query($connecte, "SELECT * FROM inventaire WHERE ID=$id");
    if ($res && mysqli_num_rows($res) > 0) {
        $eq = mysqli_fetch_assoc($res);
        $stmt = mysqli_prepare($connecte, "INSERT INTO rebut_devices (NAME,SERIAL,MANUFACTURER,MODEL,TYPE,CPU,RAM_MB,DISK_GB,OS,DOMAIN,LOCATION,BUILDING,ROOM,MACADDR,PURCHASE_DATE,WARRANTY_END) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssssiissssssss", $eq['NAME'],$eq['SERIAL'],$eq['MANUFACTURER'],$eq['MODEL'],$eq['TYPE'],$eq['CPU'],$eq['RAM_MB'],$eq['DISK_GB'],$eq['OS'],$eq['DOMAIN'],$eq['LOCATION'],$eq['BUILDING'],$eq['ROOM'],$eq['MACADDR'],$eq['PURCHASE_DATE'],$eq['WARRANTY_END']);
        if (mysqli_stmt_execute($stmt)) {
            $model = mysqli_real_escape_string($connecte, $eq['MODEL']);
            mysqli_query($connecte, "DELETE FROM moniteur WHERE MODEL='$model'");
            mysqli_query($connecte, "DELETE FROM inventaire WHERE ID=$id");
            header('Location: gestion.php'); exit;
        }
    }
}

/* ══ MISE À JOUR ══ */
$update_message = '';
if (isset($_POST['mise_a_jour'])) {
    $stmt = mysqli_prepare($connecte, "UPDATE inventaire SET NAME=?,SERIAL=?,MANUFACTURER=?,MODEL=?,TYPE=?,CPU=?,RAM_MB=?,DISK_GB=?,OS=?,DOMAIN=?,LOCATION=?,BUILDING=?,ROOM=?,MACADDR=?,PURCHASE_DATE=?,WARRANTY_END=? WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "ssssssiiisssssssi", $_POST['NAME'],$_POST['SERIAL'],$_POST['MANUFACTURER'],$_POST['MODEL'],$_POST['TYPE'],$_POST['CPU'],$_POST['RAM_MB'],$_POST['DISK_GB'],$_POST['OS'],$_POST['DOMAIN'],$_POST['LOCATION'],$_POST['BUILDING'],$_POST['ROOM'],$_POST['MACADDR'],$_POST['PURCHASE_DATE'],$_POST['WARRANTY_END'],$_POST['id']);
    mysqli_stmt_execute($stmt);
    $update_message = "<p style='color:green;'>Équipement mis à jour.</p>";
}

/* ══ AJOUT ══ */
$add_message = '';
if (isset($_POST['ajouter_bd'])) {
    $chk = mysqli_prepare($connecte, "SELECT COUNT(*) FROM inventaire WHERE SERIAL=?");
    mysqli_stmt_bind_param($chk, "s", $_POST['SERIAL']);
    mysqli_stmt_execute($chk);
    mysqli_stmt_bind_result($chk, $cnt);
    mysqli_stmt_fetch($chk);
    mysqli_stmt_close($chk);
    if ($cnt > 0) {
        $add_message = "<p style='color:red;'>Ce numéro de série existe déjà !</p>";
    } else {
        $stmt = mysqli_prepare($connecte, "INSERT INTO inventaire (NAME,SERIAL,MANUFACTURER,MODEL,TYPE,CPU,RAM_MB,DISK_GB,OS,DOMAIN,LOCATION,BUILDING,ROOM,MACADDR,PURCHASE_DATE,WARRANTY_END) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssssiissssssss", $_POST['NAME'],$_POST['SERIAL'],$_POST['MANUFACTURER'],$_POST['MODEL'],$_POST['TYPE'],$_POST['CPU'],$_POST['RAM_MB'],$_POST['DISK_GB'],$_POST['OS'],$_POST['DOMAIN'],$_POST['LOCATION'],$_POST['BUILDING'],$_POST['ROOM'],$_POST['MACADDR'],$_POST['PURCHASE_DATE'],$_POST['WARRANTY_END']);
        $add_message = mysqli_stmt_execute($stmt)
            ? "<p style='color:green;'>Équipement ajouté !</p>"
            : "<p style='color:red;'>Erreur : ".mysqli_error($connecte)."</p>";
    }
}

/* ══ FILTRES, TRI & PAGINATION (GET) ══ */
$per_page = 20;
$page     = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Colonnes triables autorisées (whitelist pour sécurité)
$allowed_sort = ['NAME','SERIAL','MANUFACTURER','MODEL','TYPE','CPU','RAM_MB','DISK_GB','OS','DOMAIN','LOCATION','BUILDING','ROOM','MACADDR','PURCHASE_DATE','WARRANTY_END'];
$sort_col = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort) ? $_GET['sort'] : 'NAME';
$sort_dir = isset($_GET['dir'])  && $_GET['dir'] === 'DESC' ? 'DESC' : 'ASC';
$sort_dir_next = $sort_dir === 'ASC' ? 'DESC' : 'ASC'; // pour inverser au prochain clic

$filters = [
    'f_manufacturer' => ['col' => 'MANUFACTURER', 'val' => ''],
    'f_type'         => ['col' => 'TYPE',         'val' => ''],
    'f_cpu'          => ['col' => 'CPU',           'val' => ''],
    'f_os'           => ['col' => 'OS',            'val' => ''],
    'f_domain'       => ['col' => 'DOMAIN',        'val' => ''],
    'f_location'     => ['col' => 'LOCATION',      'val' => ''],
    'f_building'     => ['col' => 'BUILDING',      'val' => ''],
    'f_room'         => ['col' => 'ROOM',          'val' => ''],
];
foreach ($filters as $key => &$f) {
    $f['val'] = isset($_GET[$key]) ? trim($_GET[$key]) : '';
}
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

// Total
$cnt_stmt = mysqli_prepare($connecte, "SELECT COUNT(*) FROM inventaire $where_sql");
if ($where_types) mysqli_stmt_bind_param($cnt_stmt, $where_types, ...$where_params);
mysqli_stmt_execute($cnt_stmt);
mysqli_stmt_bind_result($cnt_stmt, $total_rows);
mysqli_stmt_fetch($cnt_stmt);
mysqli_stmt_close($cnt_stmt);

$total_pages = max(1, ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset = ($page - 1) * $per_page;

// Données triées
$data_stmt = mysqli_prepare($connecte, "SELECT * FROM inventaire $where_sql ORDER BY `$sort_col` $sort_dir LIMIT $per_page OFFSET $offset");
if ($where_types) mysqli_stmt_bind_param($data_stmt, $where_types, ...$where_params);
mysqli_stmt_execute($data_stmt);
$data_result = mysqli_stmt_get_result($data_stmt);

// Listes déroulantes
function get_distinct($connecte, $col) {
    $r = mysqli_query($connecte, "SELECT DISTINCT `$col` FROM inventaire WHERE `$col` IS NOT NULL AND `$col`!='' ORDER BY `$col` ASC");
    $vals = [];
    while ($row = mysqli_fetch_row($r)) $vals[] = $row[0];
    return $vals;
}
$lists = [];
foreach (['MANUFACTURER','TYPE','CPU','OS','DOMAIN','LOCATION','BUILDING','ROOM'] as $col)
    $lists[$col] = get_distinct($connecte, $col);

// Query string helper — préserve filtres + tri
function build_qs($overrides = []) {
    $keys = ['f_manufacturer','f_type','f_cpu','f_os','f_domain','f_location','f_building','f_room','sort','dir','page'];
    $params = [];
    foreach ($keys as $k) {
        $val = array_key_exists($k, $overrides) ? $overrides[$k] : (isset($_GET[$k]) ? $_GET[$k] : '');
        if ($val !== '') $params[$k] = $val;
    }
    return 'gestion.php' . (count($params) ? '?' . http_build_query($params) : '');
}

// Lien de tri pour un en-tête
function sort_link($col, $label, $sort_col, $sort_dir) {
    $is_active = ($col === $sort_col);
    $new_dir   = ($is_active && $sort_dir === 'ASC') ? 'DESC' : 'ASC';
    $arrow     = '';
    if ($is_active) $arrow = $sort_dir === 'ASC' ? ' ▲' : ' ▼';
    $url = build_qs(['sort' => $col, 'dir' => $new_dir, 'page' => '1']);
    $class = $is_active ? 'th-sort th-sort-active' : 'th-sort';
    return '<th class="'.$class.'"><a href="'.$url.'">'.htmlspecialchars($label).$arrow.'</a></th>';
}

// Pagination avec ellipses
function render_pagination($page, $total_pages) {
    if ($total_pages <= 1) return '';
    $html = '<div class="pagination">';
    $html .= $page > 1
        ? '<a class="prev-next" href="'.build_qs(['page'=>$page-1]).'">‹ Préc.</a>'
        : '<span class="prev-next disabled">‹ Préc.</span>';
    $shown = array_unique(array_merge([1,$total_pages], range(max(2,$page-2), min($total_pages-1,$page+2))));
    sort($shown);
    $prev = null;
    foreach ($shown as $p) {
        if ($prev !== null && $p - $prev > 1) $html .= '<span class="ellipsis">…</span>';
        $html .= $p === $page
            ? '<span class="active-page">'.$p.'</span>'
            : '<a href="'.build_qs(['page'=>$p]).'">'.$p.'</a>';
        $prev = $p;
    }
    $html .= $page < $total_pages
        ? '<a class="prev-next" href="'.build_qs(['page'=>$page+1]).'">Suiv. ›</a>'
        : '<span class="prev-next disabled">Suiv. ›</span>';
    return $html . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Unités centrales</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">
    <?php include("header.php"); ?>
    <div class="main-container">
        <?php include("navbar.php"); ?>
        <div class="contenu">

            <div class="gestion-nav">
                <span class="gestion-btn gestion-btn-active" aria-current="page">Unités centrales</span>
                <a href="moniteur.php" class="gestion-btn">Moniteurs</a>
            </div>

            <?php
            /* ── Formulaire modification ── */
            if (isset($_POST['modifier'])) {
                $id = intval($_POST['modif_id']);
                $res = mysqli_query($connecte, "SELECT * FROM inventaire WHERE ID=$id");
                $r = mysqli_fetch_assoc($res);
                if ($r) {
                    echo '<h2>Modifier unité centrale</h2>';
                    echo '<form method="post"><input type="hidden" name="id" value="'.$r['ID'].'"><table><tbody>';
                    foreach (['NAME','SERIAL','MODEL','TYPE','CPU','DOMAIN','LOCATION','BUILDING','ROOM','MACADDR'] as $field)
                        echo '<tr><th>'.$field.'</th><td><input type="text" name="'.$field.'" value="'.htmlspecialchars($r[$field]).'"></td></tr>';
                    echo '<tr><th>MANUFACTURER</th><td><select name="MANUFACTURER">';
                    foreach ($lists['MANUFACTURER'] as $v) { $s=($r['MANUFACTURER']==$v)?'selected':''; echo '<option value="'.htmlspecialchars($v).'" '.$s.'>'.htmlspecialchars($v).'</option>'; }
                    echo '<option value="">-- Autre --</option></select></td></tr>';
                    echo '<tr><th>OS</th><td><select name="OS">';
                    foreach ($lists['OS'] as $v) { $s=($r['OS']==$v)?'selected':''; echo '<option value="'.htmlspecialchars($v).'" '.$s.'>'.htmlspecialchars($v).'</option>'; }
                    echo '<option value="">-- Autre --</option></select></td></tr>';
                    echo '<tr><th>RAM_MB</th><td><input type="number" name="RAM_MB" value="'.htmlspecialchars($r['RAM_MB']).'"></td></tr>';
                    echo '<tr><th>DISK_GB</th><td><input type="number" name="DISK_GB" value="'.htmlspecialchars($r['DISK_GB']).'"></td></tr>';
                    echo '<tr><th>PURCHASE_DATE</th><td><input type="date" name="PURCHASE_DATE" value="'.htmlspecialchars($r['PURCHASE_DATE']).'"></td></tr>';
                    echo '<tr><th>WARRANTY_END</th><td><input type="date" name="WARRANTY_END" value="'.htmlspecialchars($r['WARRANTY_END']).'"></td></tr>';
                    echo '<tr><td colspan="2"><button type="submit" name="mise_a_jour">Enregistrer</button> <a href="gestion.php" class="btn-reset">Annuler</a></td></tr>';
                    echo '</tbody></table></form>';
                }
            }

            /* ── Formulaire ajout ── */
            if (isset($_POST['ajout'])) {
                echo '<h2>Ajouter une unité centrale</h2>';
                echo '<form method="post"><table><tbody>';
                foreach (['NAME','SERIAL','MODEL','TYPE','CPU','DOMAIN','LOCATION','BUILDING','ROOM','MACADDR'] as $field)
                    echo '<tr><th>'.$field.'</th><td><input type="text" name="'.$field.'"></td></tr>';
                echo '<tr><th>MANUFACTURER</th><td><select name="MANUFACTURER">';
                foreach ($lists['MANUFACTURER'] as $v) echo '<option value="'.htmlspecialchars($v).'">'.htmlspecialchars($v).'</option>';
                echo '<option value="">-- Autre --</option></select></td></tr>';
                echo '<tr><th>OS</th><td><select name="OS">';
                foreach ($lists['OS'] as $v) echo '<option value="'.htmlspecialchars($v).'">'.htmlspecialchars($v).'</option>';
                echo '<option value="">-- Autre --</option></select></td></tr>';
                echo '<tr><th>RAM_MB</th><td><input type="number" name="RAM_MB"></td></tr>';
                echo '<tr><th>DISK_GB</th><td><input type="number" name="DISK_GB"></td></tr>';
                echo '<tr><th>PURCHASE_DATE</th><td><input type="date" name="PURCHASE_DATE"></td></tr>';
                echo '<tr><th>WARRANTY_END</th><td><input type="date" name="WARRANTY_END"></td></tr>';
                echo '<tr><td colspan="2"><button type="submit" name="ajouter_bd">Ajouter</button> <a href="gestion.php" class="btn-reset">Annuler</a></td></tr>';
                echo '</tbody></table></form>';
            }

            echo $import_message . $update_message . $add_message;
            ?>

            <h3>Liste des unités centrales
                <small style="font-weight:normal;font-size:13px;color:#454545;">
                    — <?= $total_rows ?> résultat(s), page <?= $page ?>/<?= $total_pages ?>
                </small>
            </h3>

            <!-- ══ BARRE D'OUTILS : filtres GET ══ -->
            <form method="get" action="gestion.php">
                <?php if ($sort_col !== 'NAME'): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($sort_col) ?>"><?php endif; ?>
                <?php if ($sort_dir !== 'ASC'):  ?><input type="hidden" name="dir"  value="<?= htmlspecialchars($sort_dir) ?>"><?php endif; ?>

                <div class="table-toolbar">
                    <div class="toolbar-filters">
                        <div class="toolbar-filters-row">
                            <?php
                            $filter_labels = [
                                'f_manufacturer' => ['Marque',   'MANUFACTURER'],
                                'f_type'         => ['Type',     'TYPE'],
                                'f_cpu'          => ['CPU',      'CPU'],
                                'f_os'           => ['OS',       'OS'],
                                'f_domain'       => ['Domaine',  'DOMAIN'],
                                'f_location'     => ['Location', 'LOCATION'],
                                'f_building'     => ['Bâtiment', 'BUILDING'],
                                'f_room'         => ['Salle',    'ROOM'],
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

                            <!-- Boutons CSV à droite (liens simples, pas dans un form) -->
                            <div class="toolbar-right">
                                <div class="toolbar-filter-group">
                                    <a href="#overlay-import-csv" class="csv">Importer CSV</a>
                                </div>
                                <div class="toolbar-filter-group">
                                    <a href="gestion.php?export_csv=1" class="csv2">Exporter CSV</a>
                                </div>
                            </div>
                        </div><!-- /toolbar-filters-row -->

                        <div class="toolbar-actions-row">
                            <button type="submit" class="btn-filter">Filtrer</button>
                            <a href="gestion.php" class="btn-reset">Réinitialiser</a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Bouton Ajouter : form POST totalement indépendant, hors du form GET -->
            <form method="post" action="gestion.php" style="text-align:right; margin-bottom: 6px;">
                <button type="submit" name="ajout" class="btn-ajouter-bas">+ Ajouter</button>
            </form>

            <!-- ══ TABLEAU ══ -->
            <table border="1">
                <thead>
                <tr>
                    <?php
                    $cols = ['NAME','SERIAL','MANUFACTURER','MODEL','TYPE','CPU','RAM_MB','DISK_GB','OS','DOMAIN','LOCATION','BUILDING','ROOM','MACADDR','PURCHASE_DATE','WARRANTY_END'];
                    foreach ($cols as $c) echo sort_link($c, $c, $sort_col, $sort_dir);
                    ?>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($ligne = mysqli_fetch_assoc($data_result)): $id = $ligne['ID']; ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['NAME']) ?></td>
                    <td><?= htmlspecialchars($ligne['SERIAL']) ?></td>
                    <td><?= htmlspecialchars($ligne['MANUFACTURER']) ?></td>
                    <td><?= htmlspecialchars($ligne['MODEL']) ?></td>
                    <td><?= htmlspecialchars($ligne['TYPE']) ?></td>
                    <td><?= htmlspecialchars($ligne['CPU']) ?></td>
                    <td><?= htmlspecialchars($ligne['RAM_MB']) ?></td>
                    <td><?= htmlspecialchars($ligne['DISK_GB']) ?></td>
                    <td><?= htmlspecialchars($ligne['OS']) ?></td>
                    <td><?= htmlspecialchars($ligne['DOMAIN']) ?></td>
                    <td><?= htmlspecialchars($ligne['LOCATION']) ?></td>
                    <td><?= htmlspecialchars($ligne['BUILDING']) ?></td>
                    <td><?= htmlspecialchars($ligne['ROOM']) ?></td>
                    <td><?= htmlspecialchars($ligne['MACADDR']) ?></td>
                    <td><?= htmlspecialchars($ligne['PURCHASE_DATE']) ?></td>
                    <td><?= htmlspecialchars($ligne['WARRANTY_END']) ?></td>
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

            <?= render_pagination($page, $total_pages) ?>

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
                <a href="gestion.php" class="btn-annuler">Annuler</a>
                <button type="submit" name="import_csv" class="btn-importer">Importer</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>