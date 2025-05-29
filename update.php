<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['id'])) {
    header('Location: index.php'); // Redirection vers la page de connexion
    exit;
}

$user_id = $_SESSION['id']; // ID de l'utilisateur connecté

// Récupération du profil de l'utilisateur
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ? AND role = 'admin' LIMIT 1");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_pass = $_POST['old_pass'] ?? ''; 
    $new_pass = $_POST['new_pass'] ?? ''; 
    $c_pass = $_POST['c_pass'] ?? ''; 
    $name = $_POST['name'] ?? '';

    if (!empty($old_pass) && !empty($new_pass) && !empty($c_pass)) {
        if (password_verify($old_pass, $fetch_profile['password'])) {
            if ($new_pass === $c_pass) {
                $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_pass->execute([$hashed_new_pass, $user_id]);
                $success_msg[] = 'Mot de passe mis à jour avec succès !';
            } else {
                $warning_msg[] = 'Les nouveaux mots de passe ne correspondent pas.';
            }
        } else {
            $warning_msg[] = 'Ancien mot de passe incorrect.';
        }
    } else {
        $warning_msg[] = 'Veuillez remplir tous les champs !';
    }

    // Mise à jour du nom d'utilisateur si un nouveau nom est soumis
    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $update_name->execute([$name, $user_id]);
        $success_msg[] = 'Nom mis à jour avec succès !';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- header section -->
<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Update Profile</h3>

      <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>" maxlength="20" class="box" 
      oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="old_pass" placeholder="Enter old password" maxlength="20" class="box" 
      oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="new_pass" placeholder="Enter new password" maxlength="20" class="box" 
      oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="c_pass" placeholder="Confirm new password" maxlength="20" class="box" 
      oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="submit" value="Update Now" name="submit" class="btn">
   </form>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>
