<?php
session_start();
include '../components/connect.php';



// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: index.php'); // Redirige vers la page de connexion s'il n'est pas connecté
    exit;
}

$user_id = $_SESSION['id']; // Récupère l'identifiant de l'utilisateur connecté
// Si le formulaire d'annulation est soumis
if(isset($_POST['cancel'])){
   $booking_id = $_POST['booking_id']; // Récupère l'ID de la réservation depuis le formulaire
   $booking_id = filter_var($booking_id, FILTER_SANITIZE_SPECIAL_CHARS); // Nettoie la valeur

   // Vérifie si la réservation existe
   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_booking->execute([$booking_id]);

   if($verify_booking->rowCount() > 0){
      // Supprime la réservation
      $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_booking->execute([$booking_id]);
      $success_msg[] = 'Annulation avec succès!'; // Message de confirmation
   } else {
      $warning_msg[] = 'Réservation déjà annulée !'; // Message d'avertissement
   } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookings</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- bookings section starts  -->

<section class="grid">

   <h1 class="heading">bookings</h1>

   <div class="box-container">

  <?php
   // Récupère toutes les réservations triées par date de check-in décroissante
   $select_bookings = $conn->prepare("SELECT * FROM `bookings` ORDER BY check_in DESC");
   $select_bookings->execute();
     
   if($select_bookings->rowCount() > 0){
      while($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)){
         // Création des objets DateTime pour calculer le nombre de nuits
         $dateDebut = $fetch_booking['check_in'];
         $dateFin = $fetch_booking['check_out'];
         $date1 = new DateTime($dateDebut);
         $date2 = new DateTime($dateFin);
         $difference = $date1->diff($date2);
         $nbreNuit = $difference->days; // Nombre total de nuits
   ?> 
   <div class="box">
      <p>Nom : <span><?= $fetch_booking['name']; ?></span></p>
      <p>Email : <span><?= $fetch_booking['email']; ?></span></p>
      <p>Numéro tel: <span><?= $fetch_booking['number']; ?></span></p>
      <p>check in : <span><?= $fetch_booking['check_in']; ?></span></p>
      <p>check out : <span><?= $fetch_booking['check_out']; ?></span></p>
      <p>nbre nuit : <span><?= $nbreNuit; ?></span></p>
      <p>Chambres : <span><?= $fetch_booking['rooms']; ?></span></p>
      <p>Adultes : <span><?= $fetch_booking['adults']; ?></span></p>
      <p>Enfants : <span><?= $fetch_booking['childs']; ?></span></p>
      <p>Prix : <span><?= $nbreNuit * ( $fetch_booking['rooms']* 140+ $fetch_booking['adults'] * 0.4 ); ?> € </span></p>
      <p>Reservation id : <span><?= $fetch_booking['booking_id']; ?></span></p>
      <h1> <?php 
      //Indicateur de paiement//
      if ($fetch_booking['payer']) {
        echo "PAYMENT EFFECTED";
      } ?> </h1>
      <!-- Formulaire d’annulation-->
      <form action="" method="POST">
         <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
         <input type="submit" value="Delete reservation" name="cancel" class="btn" onclick="return confirm('Voulez vous annuler la reservation?');">
      </form>
      
    
     
      
   </div>

   <?php
    }
   }
   ?>
   </div>
  
</section>
   
<!-- bookings section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>
    
</body>
      </html>