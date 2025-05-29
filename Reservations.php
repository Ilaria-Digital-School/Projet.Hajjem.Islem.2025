<?php
session_start(); // Démarre la session pour accéder aux variables de session

include 'components/connect.php'; // Inclut le fichier de connexion à la base de données

// Vérifie si l'utilisateur est connecté en regardant s'il y a un ID en session
if (!isset($_SESSION['id'])) {
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté
    exit; // Stoppe l'exécution du script
}

// Récupère l'ID de l'utilisateur depuis la session
$user_id = $_SESSION['id'];

// Vérifie si le formulaire d'annulation a été soumis
if (isset($_POST['cancel'])) {

    // Récupère et filtre le booking_id du formulaire pour éviter les injections
    $booking_id = $_POST['booking_id'];
    $booking_id = filter_var($booking_id, FILTER_SANITIZE_SPECIAL_CHARS);

    // Prépare une requête pour vérifier si cette réservation existe
    $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
    $verify_booking->execute([$booking_id]);

    // Si la réservation existe dans la base de données
    if ($verify_booking->rowCount() > 0) {
        // Supprime cette réservation
        $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
        $delete_booking->execute([$booking_id]);

        // Ajoute un message de succès (à afficher côté client)
        $success_msg[] = 'Annulation avec succès!';
    } else {
        // Si la réservation n'existe pas (déjà annulée ou ID invalide)
        $warning_msg[] = 'Réservation déjà annulée!';
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title>BookHotel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
  

      $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ?");
      $select_bookings->execute([$user_id]);
      if($select_bookings->rowCount() > 0){
         while($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)){
            $dateDebut = $fetch_booking['check_in']; ;$dateFin = $fetch_booking['check_out'] ; // Créer les objets DateTime
            $date1 = new DateTime($dateDebut);$date2 = new DateTime($dateFin); 
            // Calculer la différence
            $difference = $date1->diff($date2);
             // Obtenir le nombre de jours
             $nbreNuit = $difference->days;
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
      <?php 
      if ($fetch_booking['payer']) {
        echo "payer";
      }else { ?> 
      <form action="" method="POST">
         <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
         <input type="submit" value="Annuler la reservation" name="cancel" class="btn" onclick="return confirm('Voulez vous annuler la reservation?');">
      </form>
      <form action="paiement.php" method="POST">
         <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
         <input type="submit" value="Passer au paiement" name="Payement" class="btn";">
      </form>
    
      <?php }?>
      
   </div>

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
   </div>

</section>

<!-- booking section ends -->

    <!--Procédure de paiment-->
    <section class="Payment">
    <div class="container" id="payer">
    <h3 class="title">Details de Paiement</h3>
    <form action="process_payment.php" method="post">
    <label for="card_number">Numéro de Carte:</label>
    <input type="text" id="card_number" name="card_number" required><br>

    <label for="card_expiry">Date d'Expiration (MM/AA):</label>
    <input type="text" id="card_expiry" name="card_expiry" required><br>

    <label for="card_cvc">CVV:</label>
    <input type="text" id="card_cvc" name="card_cvc" required><br>

    <label for="amount">Montant:</label>
    <input type="text" id="amount" name="amount" required><br>



</div>
</section>
    <script src="js/payment.js"></script>
   
  <?php 
include "includes/footer.php";
?>
<script>
$(document).ready(function() {
var allValid=false;
var nameValid=false;
var emailValid=false;
var numberValid=false;
var passwordValid=false;
var passwordconfirmValid=true;

$("#name").keyup(function(){
        var nombreMots = jQuery.trim($(this).val()).split(' ').length;
        if($(this).val() === '') {
             nombreMots = 0;
        }
        if(nombreMots <2){
            $("small#res_name").show();
            nameValid=false;
            $("#res_name").css("color", "red").html("Trop court il faut au moins deux mots nom et prenom ");
        }else{
            $("small#res_name").hide();
            nameValid=true;

        }
        verif_tout();
});

/*****************************************Email*************************/
$("#emailReg").keyup(function(){
    verif_email();
    verif_tout();
});


function verif_email(){
  console.log("bv"+emailValid)
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'emailRegister' : $("#emailReg").val()
        },
        success: function(data){
                    if(data == "success"){
                      emailValid=true;
                        $("small#res_email").hide();
                    } else {
                      emailValid=false;
                        $("small#res_email").show();
                        $("#res_email").css("color", "red").html(data);
                    }
                 }
    });
  }
/****************************Phone number************************ */
$("#phone").keyup(function(){

    verif_number();

    verif_tout();
});


function verif_number(){
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'phone' : $("#phone").val()
        },
        success: function(data){
          

                    if(data == "success"){
                      numberValid=true;
                        $("small#res_phone").hide();
                    } else {
                      numberValid=false;

                        $("small#res_phone").show();
                        $("#res_phone").css("color", "red").html(data);
                    }
                 }
    });
}


/***********************************************************password*************************/
$("#passwordReg").keyup(function(){

var password =$(this).val();
/* || password.length > 8 */
  if (password.length < 4 ) {
    $("#res_passwordReg").css("color", "red").html("Trop court il faut au moins 4 caractères ");
      $("small#res_passwordReg").show();
            passwordValid=false;
}else if (!/[A-Z]/.test(password)) {
  $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins une lettre majuscule");
      $("small#res_passwordReg").show();
            passwordValid=false;
   
}else if (!/[a-z]/.test(password)) {
  $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins une lettre minuscule");
      $("small#res_passwordReg").show();
            passwordValid=false;
  }else if (!/\d/.test(password)) {
  $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins un chiffre");
      $("small#res_passwordReg").show();
      passwordValid=false;
    }else if (!/[$@!%*?&]/.test(password)) {
  $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins un caractère spécial");
      $("small#res_passwordReg").show();
            passwordValid=false;
    }else if($("#confirmPassword").val() != "" && $("#confirmPassword").val() != $("#passwordReg").val()){
            $("small#res_passwordReg").show();
            $("#res_passwordReg").css("color", "red").html("Les deux mots de passe sont différents");
            $("#r*es_confirmPassword").css("color", "red").html("Les deux mots de passe sont différents");
            passwordValid=false;
        } else {
          passwordValid=true;
          $("small#res_passwordReg").hide();
        }
    
    verif_tout();
});
/*****************************************  password*************************/
$("#confirmPassword").keyup(function(){
    //On vérifie si les mots de passe coïncident
    if($("#confirmPassword").val() != "" && $("#confirmPassword").val() != $("#passwordReg").val()){
        $("small#res_confirmPassword").show();
        $("#res_confirmPassword").css("color", "red").html("Les deux mots de passe sont différents");
        passwordconfirmValid=false;
         $("small#res_passwordReg").hide();
    } else {
      passwordconfirmValid=true;
         $("small#res_passwordReg").hide();
         verifPhp_password();
    }
    verif_tout();
});
function verifPhp_password(){
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'password' : $("#passwordReg").val(),
            'passwordConfirm' : $("#confirmPassword").val()
        },
        success: function(data){
                    if(data == "success"){
                      passwordconfirmValid=true;
                      passwordValid=true;

                         $("small#res_confirmPassword").hide();
                        } else {
                          passwordconfirmValid=false;
                            $("small#res_confirmPassword").show();
                            $("#res_confirmPassword").css("color", "red").html(data);
                        }
                 }
    });
}
//Traitement du formulaire d'inscription************************
function verif_tout(){
    if (nameValid==true  &&
    emailValid==true  &&
    numberValid==true  &&
    passwordValid==true  &&
    passwordconfirmValid==true){
                   
                    allValid=true;
                    $("#res-all").css("color", "green").html("Vous pouvez envoyer votre formulaire");
                    $('#res-all').attr("disabled", false); 
                             
                } else {
                  allValid=false;
                    $("#res-alll").css("color", "red").html("Veuillez remplir tous les champs avant d’envoyer le formulaire");
                    $('#res-all').attr("disabled", true);
                }

}

//Traitement du formulaire d'inscription************************
$("#form_inscription").submit(function(){
    var name = $("#name").val();
    var email = $("#emailReg").val();
    var number = $("#phone").val();
    var passwordReg = $("#passwordReg").val();
    var confirmPassword = $("#confirmPassword").val();
    //console.log(name+" "+email+" "+number+" "+passwordReg+" "+confirmPassword);
        $.ajax({
            type: "post",         
            url:  "valid.php", 
            asynch : false,       
            data: {
                'name' : name,
                'phone' : number,
                'emailRegister' : email,
                'passwordReg' : passwordReg,
                'confirmPassword' : confirmPassword,
            },
            success: function(data){
                        if(data != "successsuccessregister_success"){
                            $("#res-all").css("color", "red").html(data);
                        } else {
                          $("#container-form-sign").hide();
                          $("#welcome").html("<h2>Bienvenue "+name+" ! Vos données sont enregistrées avec succès</h2>");
                    
                        }
                     }
        });
    });
});



</script>
</body>
</html>