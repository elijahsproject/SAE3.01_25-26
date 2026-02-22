<?php
session_start();

require_once "chacha20.php";

$erreur = "";

$connecte = mysqli_connect("localhost", "sae2025", "!sae2025!", "rpiBD");

if (!$connecte) {
    die("Échec de la connexion : " . mysqli_connect_error());
}


if (isset($_POST['Login'], $_POST['MotDePasse'])) {

    $login = $_POST['Login'];
    $mdp   = $_POST['MotDePasse'];

    //On récupère uniquement par login
    $sql = "SELECT id, login, password, role FROM user WHERE login = ?";
    $requete = mysqli_prepare($connecte, $sql);

    if (!$requete) {
        die("Erreur préparation : " . mysqli_error($connecte));
    }

    mysqli_stmt_bind_param($requete, 's', $login);
    mysqli_stmt_execute($requete);
    $resultat = mysqli_stmt_get_result($requete);

    if (mysqli_num_rows($resultat) === 1) {

        $user = mysqli_fetch_assoc($resultat);

        //Déchiffrement mdp
        $motDePasseDechiffre = dechiffrer_chacha20($user['password']);

        // Comparaison
        if ($motDePasseDechiffre === $mdp) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $dateConnexion = date("Y-m-d H:i:s");

            $sqlLog = "INSERT INTO logs_connexions (login, ip, user_agent, date_connexion, statut)
                       VALUES (?, ?, ?, ?, 'SUCCES')";
            $stmtLog = mysqli_prepare($connecte, $sqlLog);
            mysqli_stmt_bind_param($stmtLog, 'ssss', $login, $ip, $agent, $dateConnexion);
            mysqli_stmt_execute($stmtLog);

            $_SESSION['login']   = $user['login'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            header("Location: accueil.php");
            exit;

        } else {

            $erreur = "Login ou mot de passe incorrect";
        }

    } else {

        $erreur = "Login ou mot de passe incorrect";
    }
}

mysqli_close($connecte);
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
                    <h1 class="nom-entreprise">No bug</h1>
                    <form method="post">
                        <label for="Login" class="sr-only">Login</label>
                        <input type="text" name="Login" id="Login" placeholder="Login" class="champ">

                        <label for="MotDePasse" class="sr-only">Mot de passe</label>
                        <input type="password" name="MotDePasse" id="MotDePasse" placeholder="Mot de passe" class="champ">

                        <?php if (!empty($erreur)): ?>
                            <p style="color: red; text-align: center; margin-bottom: 15px; font-weight: bold;">
                                <?= htmlspecialchars($erreur) ?>
                            </p>
                        <?php endif; ?>

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