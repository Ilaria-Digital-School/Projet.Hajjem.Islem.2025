<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['id'])) {
    header('Location: index.php'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit;
}
$user_id = $_SESSION['id']; // Récupère l'identifiant de l'utilisateur connecté

if(isset($_POST['delete'])){ // Vérifie si un formulaire de suppression a été soumis

   $delete_id = $_POST['delete_id']; // Récupère l'identifiant du message à supprimer
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_SPECIAL_CHARS); // Nettoie l'ID pour éviter les caractères spéciaux

   // Vérifie si le message existe
   $verify_delete = $conn->prepare("SELECT * FROM `message` WHERE id_message = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      // Supprime le message
      $delete_bookings = $conn->prepare("DELETE FROM `message` WHERE id_message = ?");
      $delete_bookings->execute([$delete_id]);
      $success_msg[] = 'Message deleted!'; // Message de succès
   }else{
      $warning_msg[] = 'Message deleted already!'; // Message d'erreur si déjà supprimé
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- messages section starts  -->

<section class="grid">

   <h1 class="heading">messages</h1>

   <div class="box-container">

   <?php
      $select_messages = $conn->prepare("SELECT * FROM `message`"); // Récupère tous les messages
      $select_messages->execute();

      if($select_messages->rowCount() > 0){
         while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){ // Parcourt chaque message
   ?>
   <div class="box">
      <p>name : <span><?= $fetch_messages['nom']; ?></span></p>
      <p>email : <span><?= $fetch_messages['email']; ?></span></p>
      <p>subject : <span><?= $fetch_messages['subject']; ?></span></p>
      <p>message : <span><?= $fetch_messages['message']; ?></span></p>
      <!-- Formulaire de suppression -->
      <form action="" method="POST">
         <input type="hidden" name="delete_id" value="<?= $fetch_messages['id_message']; ?>">
         <input type="submit" value="delete message" onclick="return confirm('delete this message?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   }else{
   ?>
   <div class="box" style="text-align: center;">
      <p>no messages found!</p>
      <a href="dashboard.php" class="btn">go to home</a>
   </div>
   <?php
      }
   ?>

   </div>

</section>

<!-- messages section ends -->
















<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>