<?php
// Clé secrète 256 bits (32 octets)
define("CLE_CHACHA20", hex2bin("00112233445566778899aabbccddeeff00112233445566778899aabbccddeeff"));

/**
 * Effectue une rotation gauche 32 bits.
 */
function rotation_gauche_32($valeur, $bits) {
    return (($valeur << $bits) & 0xffffffff) | (($valeur & 0xffffffff) >> (32 - $bits));
}


/**
 * Applique le quarter-round de ChaCha20.
 */
function quart_de_ronde(&$a, &$b, &$c, &$d) {

    $a = ($a + $b) & 0xffffffff;
    $d ^= $a;
    $d = rotation_gauche_32($d, 16);

    $c = ($c + $d) & 0xffffffff;
    $b ^= $c;
    $b = rotation_gauche_32($b, 12);

    $a = ($a + $b) & 0xffffffff;
    $d ^= $a;
    $d = rotation_gauche_32($d, 8);

    $c = ($c + $d) & 0xffffffff;
    $b ^= $c;
    $b = rotation_gauche_32($b, 7);
}


/**
 * Génère un bloc ChaCha20 de 64 octets (20 rounds).
 */
function bloc_chacha20($etat) {

    $x = $etat;

    for ($i = 0; $i < 10; $i++) {

        // Rounds colonnes
        quart_de_ronde($x[0],  $x[4],  $x[8],  $x[12]);
        quart_de_ronde($x[1],  $x[5],  $x[9],  $x[13]);
        quart_de_ronde($x[2],  $x[6],  $x[10], $x[14]);
        quart_de_ronde($x[3],  $x[7],  $x[11], $x[15]);

        // Rounds diagonales
        quart_de_ronde($x[0],  $x[5],  $x[10], $x[15]);
        quart_de_ronde($x[1],  $x[6],  $x[11], $x[12]);
        quart_de_ronde($x[2],  $x[7],  $x[8],  $x[13]);
        quart_de_ronde($x[3],  $x[4],  $x[9],  $x[14]);
    }

    for ($i = 0; $i < 16; $i++) {
        $x[$i] = ($x[$i] + $etat[$i]) & 0xffffffff;
    }

    return $x;
}


// ==============================
// GÉNÉRATION DU NONCE
// ==============================

/**
 * Génère un nonce 96 bits sécurisé (12 octets).
 * Compatible PHP < 7.0
 */
function generer_nonce() {

    $strong = false;
    $nonce = openssl_random_pseudo_bytes(12, $strong);

    if ($strong === false || $nonce === false) {
        die("Erreur : génération nonce non sécurisée.");
    }

    return $nonce;
}


// ==============================
// GÉNÉRATION KEYSTREAM
// ==============================

/**
 * Génère un keystream ChaCha20 de 64 octets.
 */
function generer_keystream($nonce, $compteur = 1) {

    // Constantes RFC 8439
    $constantes = str_split("expand 32-byte k", 4);

    $etat = [];

    // Ajout constantes
    foreach ($constantes as $c) {
        $etat[] = unpack("V", $c)[1];
    }

    // Ajout clé 256 bits
    foreach (str_split(CLE_CHACHA20, 4) as $k) {
        $etat[] = unpack("V", $k)[1];
    }

    // Compteur 32 bits
    $etat[] = $compteur;

    // Nonce 96 bits
    foreach (str_split($nonce, 4) as $n) {
        $etat[] = unpack("V", $n)[1];
    }

    $bloc = bloc_chacha20($etat);

    $sortie = "";
    foreach ($bloc as $mot) {
        $sortie .= pack("V", $mot);
    }

    return $sortie;
}


// ==============================
// CHIFFREMENT COMPLET
// ==============================

/**
 * Chiffre un texte avec ChaCha20.
 * Retourne : nonce_hex:texte_chiffré_hex
 */
function chiffrer_chacha20($texte) {

    $nonce = generer_nonce();
    $compteur = 1;

    $resultat = "";
    $longueur = strlen($texte);

    for ($i = 0; $i < $longueur; $i += 64) {

        $keystream = generer_keystream($nonce, $compteur);
        $bloc = substr($texte, $i, 64);

        $resultat .= $bloc ^ substr($keystream, 0, strlen($bloc));

        $compteur++;
    }

    return bin2hex($nonce) . ":" . bin2hex($resultat);
}


/**
 * Déchiffre une donnée ChaCha20.
 */
function dechiffrer_chacha20($donnee) {

    list($nonce_hex, $chiffre_hex) = explode(":", $donnee);

    $nonce = hex2bin($nonce_hex);
    $chiffre = hex2bin($chiffre_hex);

    $compteur = 1;
    $resultat = "";
    $longueur = strlen($chiffre);

    for ($i = 0; $i < $longueur; $i += 64) {

        $keystream = generer_keystream($nonce, $compteur);
        $bloc = substr($chiffre, $i, 64);

        $resultat .= $bloc ^ substr($keystream, 0, strlen($bloc));

        $compteur++;
    }

    return $resultat;
}

?>