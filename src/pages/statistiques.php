<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques du parc</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper_stat">
    <?php include_once("header.php"); ?>

    <div class="main-container">
        <?php include("navbar.php"); ?>

        <div class="contenu_index">
            <h1>Statistiques du parc informatique</h1>
            <div class="box"
                <p class = "intro-text">Cette page a pour objectif de proposer une vue d’ensemble claire et interactive du parc informatique à travers différentes statistiques et représentations graphiques.
                    Elle permet d’analyser l’état du matériel, son évolution, sa répartition et son utilisation, afin de faciliter le diagnostic et la prise de décision dans la gestion du parc.
                    Les données sont issues des fichiers d’inventaire et des journaux de connexion étudiés dans le cadre de ce projet.</p>
            </div>
            <div class="box graph-container">
                <div class="chart">
                    <h3>Distribution de l'âge des machines</h3>
                    <img src="../img/age_machines.png" alt="Histogramme âge machines">
                    <p>Ce graphique montre la répartition des machines selon leur âge en années.</p>
                </div>
                <br>
                <div class="chart">
                    <h3>Durée moyenne de connexion par utilisateur</h3>
                    <img src="../img/temps_connexion.png" alt="Durée moyenne de connexion">
                    <p>Durée moyenne de connexion par utilisateur sur la période analysée.</p>
                </div>

                <br>
                <div class="chart">
                    <h3>Répartition des machines par OS</h3>
                    <img src="../img/os_distribution.png" alt="Répartition OS">
                    <p>Nombre de machines par système d'exploitation.</p>
                </div>
                <br>
                <div class="chart">
                    <h3>Statut de garantie des machines</h3>
                    <img src="../img/garantie_status.png" alt="Machines hors/sous garantie">
                    <p>Nombre de machines sous garantie vs machines hors garantie.</p>
                </div>
            </div>

            <?php
            $now = time();

            function loadCsv($file) {
                $rows = array();
                if (!file_exists($file)) return $rows;
                if (($h = fopen($file, "r")) !== false) {
                    $headers = fgetcsv($h);
                    while (($data = fgetcsv($h)) !== false) {
                        $rows[] = array_combine($headers, $data);
                    }
                    fclose($h);
                }
                return $rows;
            }

            $devices  = loadCsv("../csv/inventory_devices.csv");
            $logs     = loadCsv("../csv/connections.csv");

            /* Récupération OS disponibles */
            $allOS = array();
            foreach ($devices as $d) {
                if (!empty($d["OS"])) $allOS[trim($d["OS"])] = trim($d["OS"]);
            }
            sort($allOS);

            $isSubmitted = ($_SERVER["REQUEST_METHOD"] === "POST");

            $selectedOS       = $isSubmitted && !empty($_POST["os"]) ? $_POST["os"] : "";
            $selectedCampus   = $isSubmitted && !empty($_POST["campus"]) ? $_POST["campus"] : "";
            $selectedBuilding = $isSubmitted && !empty($_POST["building"]) ? $_POST["building"] : "";
            $selectedRoom     = $isSubmitted && !empty($_POST["room"]) ? $_POST["room"] : "";
            $X_years          = $isSubmitted && isset($_POST['age_years']) && $_POST['age_years']!="" ? intval($_POST['age_years']) : "";
            $N_days           = $isSubmitted && isset($_POST['exp_days']) && $_POST['exp_days']!="" ? intval($_POST['exp_days']) : "";

            function formatDuration($seconds) {
                $seconds = (int)$seconds;
                if ($seconds < 60) return $seconds . " sec";
                $minutes = floor($seconds / 60);
                $sec = $seconds % 60;
                if ($minutes < 60) return $minutes . " min " . $sec . " sec";
                $hours = floor($minutes / 60);
                $minutes = $minutes % 60;
                return $hours . " h " . $minutes . " min " . $sec . " sec";
            }
            ?>

            <div class="box">
                <form method="POST">
                    <h3>Répartition des machines par OS</h3>
                    <select name="os">
                        <option value="">-- Choisir un OS --</option>
                        <?php foreach ($allOS as $os) {
                            $sel = ($selectedOS === $os) ? "selected" : "";
                            echo "<option $sel>$os</option>";
                        } ?>
                    </select>

                    <h3>Répartition des machines par campus</h3>
                    <select name="campus">
                        <option value="">-- Choisir un campus --</option>
                        <?php
                        $campusList = array();
                        foreach ($devices as $d) if (!in_array($d["LOCATION"], $campusList)) $campusList[] = $d["LOCATION"];
                        sort($campusList);
                        foreach ($campusList as $c) {
                            $sel = ($selectedCampus === $c) ? "selected" : "";
                            echo "<option $sel>$c</option>";
                        }
                        ?>
                    </select>

                    <h3>Répartition des machines par bâtiment</h3>
                    <select name="building">
                        <option value="">-- Choisir un bâtiment --</option>
                        <?php
                        $buildingList = array();
                        foreach ($devices as $d) if (!in_array($d["BUILDING"], $buildingList)) $buildingList[] = $d["BUILDING"];
                        sort($buildingList);
                        foreach ($buildingList as $b) {
                            $sel = ($selectedBuilding === $b) ? "selected" : "";
                            echo "<option $sel>$b</option>";
                        }
                        ?>
                    </select>

                    <h3>Répartition des machines par salle</h3>
                    <select name="room">
                        <option value="">-- Choisir une salle --</option>
                        <?php
                        $roomList = array();
                        foreach ($devices as $d) if (!in_array($d["ROOM"], $roomList)) $roomList[] = $d["ROOM"];
                        sort($roomList);
                        foreach ($roomList as $r) {
                            $sel = ($selectedRoom === $r) ? "selected" : "";
                            echo "<option $sel>$r</option>";
                        }
                        ?>
                    </select>

                    <h3>Pourcentage de machines ayant plus de X années</h3>
                    <input type="number" name="age_years" value="<?php echo $X_years; ?>">

                    <h3>Pourcentage de machines ayant une garantie expirant dans N jours</h3>
                    <input type="number" name="exp_days" value="<?php echo $N_days; ?>">

                    <input type="submit" value="Calculer">
                </form>
            </div>

            <?php if ($isSubmitted): ?>
                <div class="box">
                    <h2>Résultats</h2>
                    <?php
                    $totalDevices = count($devices);

                    // OS
                    if ($selectedOS !== "") {
                        $matchOS = 0;
                        foreach ($devices as $d) if (!empty($d["OS"]) && trim($d["OS"]) === $selectedOS) $matchOS++;
                        echo "<p><b>Probabilité qu'une machine utilise l'OS = $selectedOS :</b> ".round($matchOS/$totalDevices*100,2)."%</p>";
                    }

                    // Campus
                    if ($selectedCampus !== "") {
                        $matchCampus = 0;
                        foreach ($devices as $d) if (!empty($d["LOCATION"]) && trim($d["LOCATION"]) === $selectedCampus) $matchCampus++;
                        echo "<p><b>Probabilité qu'une machine soit dans le campus = $selectedCampus :</b> ".round($matchCampus/$totalDevices*100,2)."%</p>";
                    }

                    // Bâtiment
                    if ($selectedBuilding !== "") {
                        $matchBuilding = 0;
                        foreach ($devices as $d) if (!empty($d["BUILDING"]) && trim($d["BUILDING"]) === $selectedBuilding) $matchBuilding++;
                        echo "<p><b>Probabilité qu'une machine soit dans le bâtiment = $selectedBuilding :</b> ".round($matchBuilding/$totalDevices*100,2)."%</p>";
                    }

                    // Salle
                    if ($selectedRoom !== "") {
                        $matchRoom = 0;
                        foreach ($devices as $d) if (!empty($d["ROOM"]) && trim($d["ROOM"]) === $selectedRoom) $matchRoom++;
                        echo "<p><b>Probabilité qu'une machine soit dans la salle = $selectedRoom :</b> ".round($matchRoom/$totalDevices*100,2)."%</p>";
                    }

                    // Machines > X années
                    if ($X_years !== "") {
                        $total=0; $older=0;
                        foreach ($devices as $d) {
                            if (!empty($d['PURCHASE_DATE']) && strtotime($d['PURCHASE_DATE'])) {
                                $total++;
                                $age = ($now - strtotime($d['PURCHASE_DATE']))/(365*24*3600);
                                if ($age > $X_years) $older++;
                            }
                        }
                        $probOlder = $total>0 ? round($older/$total*100,2) : 0;
                        echo "<p><b>Probabilité qu’une machine ait plus de $X_years ans :</b> $probOlder%</p>";
                    }
                    ?>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Projet SAE - Groupe X</p>
    </footer>
</div>
</body>
</html>
