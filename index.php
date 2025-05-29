<?php
session_start();
include '../components/connect.php';

// Si le formulaire a été soumis
if(isset($_POST['submit'])){
    // Récupère et assainit le nom de l'utilisateur
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);

    // Récupère et assainit l'adresse e-mail
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Récupère le mot de passe sans le filtrer (car il sera vérifié via password_verify)
    $pass = $_POST['pass'];

    // Vérification de l'utilisateur dans la base de données
       // Prépare une requête pour rechercher un utilisateur par e-mail
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
    $select_user->execute([$email]); // Exécute la requête avec l'e-mail fourni

    // Récupère les données de l'utilisateur
    $row = $select_user->fetch(PDO::FETCH_ASSOC);


        // Vérifie si l'utilisateur existe et si le mot de passe est correct
    if($select_user->rowCount() > 0 && password_verify($pass, $row['password'])){
        // Crée un cookie sécurisé pour stocker l'ID de l'utilisateur (valide 30 jours)
        setcookie('user_id', $row['id'], time() + 60*60*24*30, '/', '', true, true); // HttpOnly + Secure

        // Récupère le rôle de l'utilisateur (admin ou utilisateur normal)
        $role = $row['role'];
        
        // Redirige selon le rôle de l'utilisateur
        if ($role === 'admin') {
            header('location:./dashboard.php'); // Vers le tableau de bord admin
        } else {
            header('location:index.php'); // Vers l'accueil pour les utilisateurs classiques
        }
    } else {
        // Si e-mail ou mot de passe incorrect, message d'avertissement
        $warning_msg[] = 'Email ou mot de passe incorrect!';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<!-- login section starts  -->

<section class="form-container" style="min-height: 100vh;">

   <form action="" method="POST">
      <h3>Admin Login</h3>
      <!-- <p>default name = <span>admin</span> & password = <span>111</span></p> -->
      <input type="text" name="name" placeholder="enter username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="email" name="email" placeholder="enter email" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" placeholder="enter password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" name="submit" class="btn">
   </form>
</section>
<!-- login section ends -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>