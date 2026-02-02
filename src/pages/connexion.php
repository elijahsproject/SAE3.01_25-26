<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion parc IT - Se connecter</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">



    <div class="main-container">




        <div class="contenu_connexion">
            <div class="conteneur_connexion">
                <div class="formulaire">
                    <div class="logo">
                        <img src="../img/logo.png" alt="Logo de l’application de gestion du parc informatique" class="image-logo">
                    </div>
                    <h1 class="nom-entreprise">Nom Appli</h1>
                    <form method="post">
                        <label for="Login" class="sr-only">Login</label>
                        <input type="text" name="Login" id="Login" placeholder="Login" class="champ">

                        <label for="MotDePasse" class="sr-only">Mot de passe</label>
                        <input type="password" name="MotDePasse" id="MotDePasse" placeholder="Mot de passe" class="champ">

                        <button type="submit" class="bouton">Connexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<footer>
    <p>&copy; 2025 - Projet SAE - Groupe X</p>
</footer>
</div>
</body>
</html>
<?php
$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");
if($connecte==False){
    echo "Erreur de connexion";
}
else{
    echo("connecte avec succes");
}
$db = mysqli_select_db($connecte,"rpiBD");
if(!$db){
    echo "utilisation de la base KO";
}
else{
    echo("utilisation de la base OK");
    echo"<br>";
}

if (isset($_POST['Login'], $_POST['MotDePasse'])) {

    $login = $_POST['Login'];
    $mdp   = $_POST['MotDePasse'];

    $sql = "SELECT id, login, role FROM user WHERE login = ? AND password = ?";
    $requete = mysqli_prepare($connecte, $sql);
    mysqli_stmt_bind_param($requete, 'ss', $login, $mdp);
    mysqli_stmt_execute($requete);
    $resultat = mysqli_stmt_get_result($requete);

    if (mysqli_num_rows($resultat) === 1) {

        $user = mysqli_fetch_assoc($resultat);

        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $dateConnexion = date("Y-m-d H:i:s");

        $sqlLog = "INSERT INTO logs_connexions (login, ip, user_agent, date_connexion, statut)
                   VALUES (?, ?, ?, ?, 'SUCCES')";
        $stmtLog = mysqli_prepare($connecte, $sqlLog);
        mysqli_stmt_bind_param($stmtLog, 'ssss', $login, $ip, $agent, $dateConnexion);
        mysqli_stmt_execute($stmtLog);

        $_SESSION['login'] = $user['login'];
        $_SESSION['role']  = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        header("Location: accueil.php");
        exit;
    }
}

?>