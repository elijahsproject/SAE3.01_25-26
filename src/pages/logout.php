<?php
session_start();
$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if(isset($_SESSION['log_id'])){
    $idLog = $_SESSION['log_id'];
    $dateDeconnexion = date("Y-m-d H:i:s");

    $sql = "UPDATE logs_connexions
            SET date_deconnexion = ?, 
                duree_sec = TIMESTAMPDIFF(SECOND, date_connexion, ?)
            WHERE id = ?";

    $stmt = mysqli_prepare($connecte, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $dateDeconnexion, $dateDeconnexion, $idLog);
    mysqli_stmt_execute($stmt);
}

session_destroy();
header("Location: accueil.php");
exit;
?>