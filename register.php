<?php 
session_start(); // Démarre la session pour accéder aux variables de session
include '../components/connect.php'; // Inclut le fichier de connexion à la base de données

$user_id = $_SESSION['id']; // Récupère l'identifiant de l'utilisateur depuis la session

// Vérifie si le formulaire a été soumis
if(isset($_POST['submit'])){

    // Nettoie et filtre les données reçues du formulaire
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS); // Supprime les caractères spéciaux du nom
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Nettoie l'email
    $password = filter_var($_POST['pass'], FILTER_SANITIZE_SPECIAL_CHARS); // Supprime les caractères spéciaux du mot de passe
    $c_pass = filter_var($_POST['c_pass'], FILTER_SANITIZE_SPECIAL_CHARS); // Idem pour le mot de passe de confirmation

    // Vérifie si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $warning_msg[] = 'Email invalide !'; // Ajoute un message d'avertissement si l'email est invalide
    } elseif ($password !== $c_pass) {
        $warning_msg[] = 'Les mots de passe ne correspondent pas !'; // Vérifie que les mots de passe sont identiques
    } else {
        try {
            // Prépare une requête pour vérifier si un utilisateur admin existe déjà avec cet email ou ce nom
            $select_users = $conn->prepare("SELECT * FROM users WHERE email = :email OR name = :name  AND role = 'admin'");
            $select_users->bindParam(':email', $email, PDO::PARAM_STR); // Lier l'email à la requête
            $select_users->bindParam(':name', $name, PDO::PARAM_STR); // Lier le nom à la requête
            $select_users->execute(); // Exécute la requête

            // Vérifie si un utilisateur existe déjà
            if ($select_users->rowCount() > 0) {
                $warning_msg[] = 'Username ou email déjà utilisé !'; // Avertit si le nom ou l'email est déjà pris
            } else {
                // Hashe le mot de passe avec l'algorithme par défaut (bcrypt)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prépare la requête d'insertion du nouvel utilisateur
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR); // Lier le nom
                $stmt->bindParam(':email', $email, PDO::PARAM_STR); // Lier l'email
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Lier le mot de passe hashé
                $role = 'admin'; // Définit le rôle utilisateur comme admin
                $stmt->bindParam(':role', $role, PDO::PARAM_STR); // Lier le rôle
                $stmt->execute(); // Exécute l'insertion
                $success_msg[] = 'Registered successfully!'; // Message de succès
                echo "Utilisateur enregistré avec succès !"; // Affiche un message de succès
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage(); // Affiche une erreur SQL si une exception est levée
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8"> <!-- Définition de l'encodage -->
   <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Pour compatibilité avec IE -->
   <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Pour responsive design -->
   <title>Register</title> <!-- Titre de la page -->

   <!-- Lien vers la bibliothèque Font Awesome pour les icônes -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Lien vers le fichier CSS personnalisé -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- Inclusion de l'en-tête de l'administrateur -->
<?php include '../components/admin_header.php'; ?>

<!-- Début de la section formulaire d'inscription -->
<section class="form-container">
   <form action="" method="POST"> <!-- Formulaire en POST -->
      <h3>register new</h3>
      <!-- Champ nom d'utilisateur, sans espaces -->
      <input type="text" name="name" placeholder="enter username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <!-- Champ email, sans espaces -->
      <input type="email" name="email" placeholder="enter email" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <!-- Champ mot de passe, sans espaces -->
      <input type="password" name="pass" placeholder="enter password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <!-- Champ de confirmation de mot de passe -->
      <input type="password" name="c_pass" placeholder="confirm password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <!-- Bouton de soumission -->
      <input type="submit" value="register now" name="submit" class="btn">
   </form>
</section>

<!-- Lien vers SweetAlert pour les messages jolis (non utilisé ici mais prêt à l'emploi) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- Inclusion du fichier JavaScript personnalisé -->
<script src="../js/admin_script.js"></script>

<!-- Inclusion de la gestion d'affichage des messages de feedback -->
<?php include '../components/message.php'; ?>

</body>
</html>
