<?php
session_start(); // Démarre une session PHP (utile si vous utilisez des variables de session)

use ReCaptcha\ReCaptcha; // Importation de la classe ReCaptcha depuis la bibliothèque

// Vérifie si le formulaire a été soumis en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Inclusion du fichier autoload de la bibliothèque ReCaptcha
    require_once '../autoload.php'; // Assurez-vous que ce chemin est correct selon votre structure de fichiers

    // Création d'une instance ReCaptcha avec votre clé secrète
    $recaptcha = new ReCaptcha('6LcVrygrAAAAAPrWpLlXpmW17UydR9R55Gc5g_Gw');

    // Vérifie la réponse reCAPTCHA envoyée par l'utilisateur
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    // Si le reCAPTCHA échoue, on redirige avec un message d'erreur
    if (!$resp->isSuccess()) {
        header("Location: ../contact.php?send=echec_captcha");
        exit; // Arrête l'exécution du script
    }

    // Récupération et nettoyage des données du formulaire
    $nom = htmlspecialchars(trim($_POST['name'])); // Nettoie le nom (sécurité XSS + espaces)
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL); // Valide et nettoie l'email
    $sujet = htmlspecialchars(trim($_POST['subject'])); // Nettoie le sujet
    $message = htmlspecialchars(trim($_POST['message'])); // Nettoie le message

    // Vérifie que les champs obligatoires ne sont pas vides
    if (empty($nom) || empty($email) || empty($message)) {
        header("Location: ../contact.php?send=champ_vide"); // Redirection si champ vide
        exit;
    }

    // Vérifie que l'adresse email est valide
    if (!$email) {
        header("Location: ../contact.php?send=email_invalide"); // Redirection si email invalide
        exit;
    }

    // Informations de connexion à la base de données
    $hote = 'localhost'; // Hôte MySQL
    $nom_bd = 'hotel_db'; // Nom de la base de données
    $utilisateur = 'root'; // Nom d'utilisateur MySQL
    $mot_de_passe = ''; // Mot de passe MySQL

    // Tentative de connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=$hote;dbname=$nom_bd;charset=utf8", $utilisateur, $mot_de_passe);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Active les exceptions en cas d'erreur

        // Prépare la requête SQL pour insérer un message
        $stmt = $pdo->prepare("INSERT INTO message (nom, email, subject, message) VALUES (:nom, :email, :sujet, :message)");

        // Lie les valeurs aux paramètres de la requête
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':sujet', $sujet);
        $stmt->bindParam(':message', $message);

        // Exécute la requête SQL
        if ($stmt->execute()) {
            header("Location: ../contact.php?send=ok"); // Redirection si insertion réussie
            exit;
        } else {
            header("Location: ../contact.php?send=echec_bd"); // Redirection si échec d'insertion
            exit;
        }

    } catch (PDOException $e) {
        // En cas d'erreur avec PDO (ex : erreur de requête ou de connexion)
        header("Location: ../contact.php?send=erreur_exception");
        exit;
    }

} else {
    // Si la page a été accédée sans soumission de formulaire
    header("Location: ../contact.php?send=aucune_donnee");
    exit;
}
?>
