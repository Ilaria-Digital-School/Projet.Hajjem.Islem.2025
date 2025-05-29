<?php 
session_start(); // Démarre une session utilisateur
include 'components/connect.php'; // Fichier de connexion à la base de données

// -------------------- Vérification de l'email --------------------
if (!empty($_POST['emailRegister'])) {
    $email = $_POST['emailRegister'];

    // Vérifie si l'email est au bon format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Adresse email invalide ! ';
    }

    // Vérifie si l'email existe déjà dans la base
    $q = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $q->execute([$email]);
    $numRows = $q->rowCount();

    if ($numRows > 0) {
        echo 'Adresse email déjà utilisée ! ';
    } else {
        echo 'success'; // L'email est valide et disponible
    }
}

// -------------------- Vérification du numéro de téléphone --------------------
if (!empty($_POST['phone'])) {
    $phone = $_POST['phone'];

    $q = $conn->prepare('SELECT id FROM users WHERE number = ?');
    $q->execute([$phone]);
    $numRows = $q->rowCount();

    if ($numRows > 0) {
        echo 'numero de telephone déjà utilisée ! ';
    } else {
        echo 'success'; // Numéro disponible
    }
}

// -------------------- Vérification des mots de passe --------------------
if (!empty($_POST['passwordReg']) && !empty($_POST['passwordConfirm'])) {

    // Vérifie la complexité du mot de passe (mais attention : ta condition est inversée)
    if (preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W){8,12}$#', $_POST['passwordReg'])) {
        // Si le mot de passe respecte la regex, ne pas afficher "faible"
        // Ton message d’erreur est mal placé ici.
        // Inverser la logique est conseillé !
        if ($_POST['passwordReg'] === $_POST['passwordConfirm']) {
            echo 'success'; // Mot de passe fort et confirmation correcte
        } else {
            echo 'Les deux mots de passe sont différents ';
        }
    } else {
        echo 'Mot de passe trop court / faible';
    }
}

// -------------------- Traitement final de l'inscription --------------------
if (!empty($_POST['name'])) {
    $name = $_POST['name'];
    $number = $_POST['phone'];
    $email = $_POST['emailRegister'];
    $pass1 = $_POST['passwordReg'];
    $pass2 = $_POST['confirmPassword'];

    // Vérifie si le numéro est déjà pris
    $q = $conn->prepare('SELECT id FROM users WHERE number = ?');
    $q->execute([$number]);
    $number_check = $q->rowCount();

    // Vérifie si l'email est déjà utilisé
    $q = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $q->execute([$email]);
    $email_check = $q->rowCount();

    // Vérifications supplémentaires
    if (empty($name) || empty($number) || empty($email) || empty($pass1) || empty($pass2)) {
        echo "Tous les champs n'ont pas été remplis.";
    } else if ($number_check > 0) {
        echo "numero de telephone déjà utilisé ";
    } else if ($email_check > 0) {
        echo "Cette adresse mail est déjà utilisée ";
    } else if (is_numeric($name[0])) {
        echo "Le nom doit commencer par une lettre. ";
    } else if ($pass1 != $pass2) {
        echo "Les mots de passe ne correspondent pas. ";
    } else {
        // Hachage du mot de passe avant insertion
        $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);

        // Requête d'insertion dans la base de données
        $req = "INSERT INTO users(name,email,number,password) VALUES ('$name','$email','$number','$hashed_password')";
        
        $res = $conn->query($req);

        if ($res) {
            echo "register_success";

            // Stocke les infos utilisateur en session
            $_SESSION['name'] = $name;
            $_SESSION['number'] = $phone;
            $_SESSION['email'] = $email;
            exit();
        } else {
            echo "echec"; // En cas d'erreur d'insertion
        }
    }
    exit(); // Stoppe le script après traitement
}
?>
