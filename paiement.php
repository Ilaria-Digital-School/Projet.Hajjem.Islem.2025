<?php
// Démarre une session PHP (pour gérer les utilisateurs connectés, etc.)
session_start();

// Inclut le fichier de connexion à la base de données
include 'components/connect.php';

// Vérifie si un cookie 'user_id' existe
if(isset($_COOKIE['user_id'])){
    // Si oui, récupère sa valeur
    $user_id = $_COOKIE['user_id'];
}else{

    // Si aucun cookie n'existe, commence le processus de vérification reCAPTCHA

    // Charge automatiquement les classes nécessaires (ex : bibliothèque reCAPTCHA)
    require_once './autoload.php';

    // Récupère l'adresse IP de l'utilisateur
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    // Vérifie si le formulaire a été soumis avec le bouton "ok"
    if (isset($_POST['ok'])) {
        // Crée une instance de reCAPTCHA avec la clé secrète
        $recaptcha = new \ReCaptcha\ReCaptcha("6LcVrygrAAAAAPrWpLlXpmW17UydR9R55Gc5g_Gw");

        // Récupère la réponse du reCAPTCHA envoyée par le formulaire
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];

        // Vérifie la validité du reCAPTCHA en précisant le nom d'hôte attendu
        $resp = $recaptcha->setExpectedHostname('localhost')
                          ->verify($gRecaptchaResponse, $remoteIp);

        // Si le reCAPTCHA est valide
        if ($resp->isSuccess()) {
            echo "<p style='color:green;'>Succès ! reCAPTCHA validé.</p>";
        } else {
            // Sinon, récupère et affiche les codes d'erreur
            $errors = $resp->getErrorCodes();                                                                               
            echo "<p style='color:red;'>Erreur reCAPTCHA : ";
            var_dump($errors);
            echo "</p>";
        }
    }
}
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

<!-- booking section starts  -->

<section class="bookings">

   <h1 class="heading">Mes Reservations</h1>
   <div class="box-container">
   <?php

// Prépare une requête SQL pour sélectionner une réservation spécifique depuis la table `bookings`
// La réservation est identifiée via son `booking_id`, passé en POST
$select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
$select_bookings->execute([$_POST['booking_id']]); // Exécute la requête avec la valeur envoyée par le formulaire

// Vérifie si au moins une réservation correspond au booking_id fourni
if ($select_bookings->rowCount() > 0) {
    
    // Parcourt les réservations trouvées (normalement une seule)
    while ($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
        
        // Récupère les dates de début et de fin du séjour
        $dateDebut = $fetch_booking['check_in'];
        $dateFin = $fetch_booking['check_out'];

        // Crée deux objets DateTime à partir des dates récupérées
        $date1 = new DateTime($dateDebut);
        $date2 = new DateTime($dateFin);

        // Calcule la différence entre les deux dates
        $difference = $date1->diff($date2);

        // Extrait le nombre total de jours (nuits) entre les deux dates
        $nbreNuit = $difference->days;

        // À ce stade, tu peux utiliser $nbreNuit pour afficher ou calculer le prix, etc.
?>

   <div class="box">
      <p>Nom : <span><?= $fetch_booking['name']; ?></span></p>
      <p>Email : <span><?= $fetch_booking['email']; ?></span></p>
      <p>Numéro : <span><?= $fetch_booking['number']; ?></span></p>
      <p>check in : <span><?= $fetch_booking['check_in']; ?></span></p>
      <p>check out : <span><?= $fetch_booking['check_out']; ?></span></p>
      <p>nbre nuit : <span><?= $nbreNuit; ?></span></p>
      <p>Chambres : <span><?= $fetch_booking['rooms']; ?></span></p>
      <p>Adultes : <span><?= $fetch_booking['adults']; ?></span></p>
      <p>Enfants : <span><?= $fetch_booking['childs']; ?></span></p>
      <p>Prix : <span><?= $nbreNuit * ( $fetch_booking['rooms']* 140+ $fetch_booking['adults'] * 0.4 ); ?> € </span></p>
      <p>Reservation id : <span><?= $fetch_booking['booking_id']; ?></span></p>
   </div>

  
   </div>

</section>


<!-- booking section ends -->

    <!--Procédure de paiment-->
    <section class="Payment">
    <div class="container" id="payer">
    <h3 class="title">Details de Paiement</h3>
    <form action="process_payment.php" method="post">
        <input type="hidden" name="booking_id" value="<?=$_POST['booking_id']?>">
        <input type="hidden" name="amount" value="<?=$nbreNuit * ( $fetch_booking['rooms']* 140+ $fetch_booking['adults'] * 0.4 ); ?>">
        <label for="card_number">Numéro de Carte:</label>
        <input type="text" id="card_number" name="card_number" required><br>

        <label for="card_expiry">Date d'Expiration (MM/AA):</label>
        <input type="text" id="card_expiry" name="card_expiry" required><br>

        <label for="card_cvc">CVC:</label>
        <input type="text" id="card_cvc" name="card_cvc" required><br>

        <label for="amount">Montant:<?= $nbreNuit * ( $fetch_booking['rooms']* 140+ $fetch_booking['adults'] * 0.4 ); ?> € </label>
        
         <div class="g-recaptcha" data-sitekey="6LcVrygrAAAAAJoq_R_M2HmN_9BF7xYjYxxYSHsG"></div>
        <button type="submit">Payer</button>
          
    </form>
</div>
</section>

<?php
    }
   }else{
   ?>   
   <div class="box" style="text-align: center;">
      <p style="padding-bottom: .5rem; text-transform:capitalize;">Pas des reservations!</p>
      <a href="index.php#reservation" class="btn">Reserver maintenant</a>
   </div>
   <?php
   }
   ?>
    <script src="js/payment.js"></script>
  <?php 
include "includes/footer.php";
?>
</body>
</html>