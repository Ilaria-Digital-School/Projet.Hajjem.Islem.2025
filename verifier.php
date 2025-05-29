<?php
session_start();
session_regenerate_id(true);

include 'components/connect.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_db";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Vérifie que les champs email et password ont bien été envoyés par le formulaire
if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']); // Supprime les espaces avant/après dans l'email
    $pwd = trim($_POST['password']); // Supprime les espaces dans le mot de passe
       // Vérifie que l'adresse email est dans un format valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse email invalide."; // Message d'erreur
        exit; // Interrompt l'exécution du script
    }
    // Récupérer les informations de l'utilisateur, y compris le rôle
      // Prépare une requête SQL sécurisée pour récupérer les infos de l'utilisateur
    $stmt = $conn->prepare("SELECT id, name, number, email, password, salt, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Lie le paramètre email à la requête préparée (type string)
    $stmt->execute(); // Exécute la requête
    $stmt->store_result(); // Stocke le résultat pour pouvoir faire fetch ensuite
        // Vérifie si un utilisateur a été trouvé avec cet email
    if ($stmt->num_rows > 0) {
                // Récupère les résultats dans des variables
        $stmt->bind_result($id, $name, $number, $user_email, $stored_hash, $stored_salt, $role);
        $stmt->fetch(); // Récupère la ligne de résultat

        // Vérifie si le mot de passe est correct après avoir concaténé le mot de passe fourni avec le sel
        if (password_verify($pwd . $stored_salt, $stored_hash)) {
        // Enregistre les informations de l'utilisateur dans la session
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['number'] = $number;
            $_SESSION['email'] = $user_email;
            $_SESSION['role'] = $role;

            // Rediriger selon le rôle
            if ($role === 'admin') {
                header('Location: admin/dashboard.php'); // Page admin
            } else {
                header('Location: index.php'); // Page utilisateur
            }
            exit;
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Email ou mot de passe incorrect.";
    }

    $stmt->close();
} else {
    echo "Veuillez remplir tous les champs.";
}

$conn->close();

?>

