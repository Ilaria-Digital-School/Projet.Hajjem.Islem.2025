<?php
session_start();
include 'components/connect.php';

// Créer une connexion PDO (c’est déjà correct)
$pdo = new PDO('mysql:host=localhost;dbname=hotel_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Traitement du paiement (FAUX traitement, car pas d’API bancaire ici)
$booking_id = $_POST['booking_id'] ?? null;
$card_number = $_POST['card_number'] ?? null;
$card_expiry = $_POST['card_expiry'] ?? null;
$card_cvc = $_POST['card_cvc'] ?? null;
$amount = $_POST['amount'] ?? null;

// Validation simple (vous pouvez renforcer ça)
if (!$booking_id || !$card_number || !$card_expiry || !$card_cvc || !$amount) {
    die("Erreur : Données de paiement incomplètes.");
}


$stmt = $pdo->prepare("INSERT INTO transactions (booking_id, card_number, card_expiry, card_cvc, amount) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$booking_id, $card_number, $card_expiry, $card_cvc, $amount]);

// Marquer la réservation comme payée
$update_stmt = $pdo->prepare("UPDATE bookings SET payer = 1 WHERE booking_id = ?");
$update_stmt->execute([$booking_id]);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title>BookHotel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <?php 
include "includes/header.php";
?>  <!--Début Header2 Navbar-->
  <header class="header">
    <div class="container">
      <nav class="navbar flex1">
        <div class="sticky_logo logo">
          <img src="image/logo.png" alt="">
        </div>
        <ul class="nav-menu">
          <li> <a href="./">Accueil</a> </li>
          <li> <a href="#about">Apropos</a> </li>
          <li> <a href="./Hotel.php">BookHotel</a> </li>
          <li> <a href="./Reservations.php">Reservations</a> </li>
          <li> <a href="#wrapper2">services</a> </li>
          <li> <a href="#shop">Offres</a> </li>
          <li> <a href="#gallary">Gallerie</a> </li>
          <li> <a href="./contact.php">Contact</a> </li> 
          
        </ul>
        <div class="hamburger">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div> 
      </nav>
  </header>
  <!--cede js de Header2-->
  <script>
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    hamburger.addEventListener("click", mobliemmenu);
    function mobliemmenu() {
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");
    }
    window.addEventListener("scroll", function() {
      var header = document.querySelector("header");
      header.classList.toggle("sticky", window.scrollY > 0)
    })
  </script>
  <!--Fin Header2-->
  <section>
    <h2> 
   
</div>

<?php 

if ($stmt->execute()) {
    echo "Paiement réussi!";
} else {
    echo "Erreur: " . $stmt->error;
} ?> </h2>
</section>
  <?php 
include "includes/footer.php";
?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php';?>
</body>
