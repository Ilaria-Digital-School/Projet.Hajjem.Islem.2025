<?php
session_start(); // Démarre la session pour gérer les données utilisateur

include 'components/connect.php'; // Inclut le fichier de connexion à la base de données

// Vérifie si un cookie 'user_id' existe
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id']; // Récupère l'ID utilisateur depuis le cookie
}else{
   // Sinon, crée un cookie 'user_id' avec un identifiant unique valable 30 jours
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php'); // Redirige vers la page d'accueil après création du cookie
}   


// Si le formulaire de vérification de disponibilité est soumis
if(isset($_POST['check']))
{
  // Récupère et filtre les dates de check-in et check-out
  $check_in = $_POST['check_in'];
  $check_in = filter_var($check_in, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $check_out = $_POST['check_out'];
  $check_out= filter_var($check_out, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  $total_rooms = 0; // Initialisation du compteur de chambres réservées

  // Convertit les dates en timestamps pour la boucle
  $dateDebut = $check_in;
  $dateFin = $check_out;
  $timestampDebut = strtotime($dateDebut);
  $timestampFin = strtotime($dateFin);

  $test = 'ok'; // Variable pour indiquer la disponibilité des chambres

  // Parcourt chaque date entre check-in et check-out inclus
  for ($date = $timestampDebut; $date <= $timestampFin; $date = strtotime('+1 day', $date)) {   
    $currentDate = date('Y-m-d', $date); // Format de la date 'YYYY-MM-DD'

    // Requête pour récupérer les réservations le jour courant
    $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
    $check_bookings->execute([$currentDate]);

    // Additionne le nombre de chambres réservées à la date courante
    while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
    }

    // Ajoute le nombre de chambres demandées dans la requête
    $total_rooms += $_POST['rooms'];

    // Si le total dépasse ou atteint 30 (capacité max), marque comme non disponible
    if($total_rooms >= 30){
      $test = 'ko';
    }
  }

  // Affiche un message d’avertissement ou de succès selon la disponibilité
  if($test == 'ko'){
    $warning_msg[] = 'chambre non disponible';
  }else{
    $success_msg[] = 'Chambre disponible';
  }
}

// Si le formulaire de réservation est soumis
if(isset($_POST['book']))
{
   $booking_id = create_unique_id(); // Génère un ID unique pour la réservation

   // Récupère et filtre les données du formulaire
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0; // Initialise le compteur pour vérifier la disponibilité

   // Récupère les réservations existantes à la date de check-in
   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   // Compte le total de chambres déjà réservées pour cette date
   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // Vérifie si la capacité maximale est atteinte
   if($total_rooms >= 30){
      $warning_msg[] = 'Chambre non disponible';
   }else{

      // Vérifie si une réservation identique existe déjà pour éviter les doublons
      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'chambre deja reservée';
      }else{
         // Insère la nouvelle réservation dans la base de données
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'Reservation de chambre avec succes';
      }

   }

}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta name="description" content="Découvrez comment créer des balises meta description efficaces pour 
améliorer votre référencement et attirer plus de visiteurs sur votre site.">
<head>
  <title>Booking Memories</title>
   <!-- icone de longlet -->
   <?php 
include "includes/header.php";
?>

  <!--Début Header2 Navbar-->
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
  <script>
    //code js de Header2//
// Code JS pour gérer le menu responsive du header (Header2)

    // Sélectionne l'élément avec la classe .hamburger (l'icône du menu)
    const hamburger = document.querySelector(".hamburger");

    // Sélectionne le menu de navigation
    const navMenu = document.querySelector(".nav-menu");

    // Ajoute un événement de clic sur le menu hamburger
    hamburger.addEventListener("click", mobliemmenu);

    // Fonction qui gère l'ouverture/fermeture du menu mobile
    function mobliemmenu() {
        // Ajoute ou enlève la classe "active" sur l'icône hamburger
        hamburger.classList.toggle("active");

        // Ajoute ou enlève la classe "active" sur le menu de navigation
        navMenu.classList.toggle("active");
    }

    // Ajoute un événement lors du défilement de la page (scroll)
    window.addEventListener("scroll", function() {
        // Sélectionne l'élément <header>
        var header = document.querySelector("header");

        // Ajoute la classe "sticky" si l'utilisateur a scrollé vers le bas
        header.classList.toggle("sticky", window.scrollY > 0);
    });
</script>

  <!--Fin Header2-->
  <!--Home-->
  <section class="home" id="home">
    <div class="container">
      <h1>Booking Memories</h1>
      <p>Créer vos innoubliables souvenirs</p> 
      <form action="" method="post">
      <div class="content grid">   
          <div class="box">
          <span>Date d'Arrivée </span> <br>
          <input type="date" placeholder="29/20/2021" name="check_in" class="input" required>
        </div>
        <div class="box">
          <span>Date de Départ</span> <br>
          <input type="date" placeholder="29/20/2021" name="check_out" class="input" required>
        </div>
        <div class="box">
          <span>Adultes</span> <br>
          <input type="number" placeholder="01"name="adults" class="input" required>
        </div>
        <div class="box">
          <span>Enfants</span> <br>
          <input type="number" placeholder="01" name="childs" class="input" required>
        </div>
        <div class="box">
          <span>Chambres</span> <br>
          <input type="number" placeholder="01" name="rooms"  class="input" required>
        </div>
        <div class="box">
          <button  class="flex1">
          <input type="submit" value="Vérifier la disponibilité" name="check" class="btn">
          
            <i class="fas fa-arrow-circle-right"></i>
          </button>
        </div>

      </div>
</form>
    </div>
  </section>
  <section class="about" id="about">
    <div class="container">
      <div class="heading">
        <h5>********************</h5>
        <h2>A propos Nous 
        </h2>
      </div>
      <div class="content flex  top">
        <div class="left">
          <h3>Nous somme votre meilleur choix 
          </h3>
          <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ratione, repudiandae quos, assumenda voluptatem repellendus ullam ipsa excepturi, doloremque quibusdam iure laboriosam velit. Voluptas labore laborum commodi corporis blanditiis dolores sapiente?</p>
          <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Fugiat, sapiente facere! Asperiores excepturi nobis aperiam iure quas libero reprehenderit velit, magnam ratione sequi adipisci, nam suscipit laboriosam pariatur tempore assumenda.</p>
        </div>
        <div class="right">
          <img src="image/a.jpg" alt="">
        </div>
      </div>
    </div>
  </section>
  <section class="wrapper">
    <div class="container">
      <div class="owl-carousel owl-theme">
        <div class="item">
          <div class="heading">
            <h5>Booking Memories</h5>
            <h3>Chambres</h3>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit </p>
            <img src="image/h1.jpg" alt="">
          </div>
        <div class="item">
          <div class="heading">
            <h5>Booking Memories</h5>
            <h3>Accompagnement de l'aeropot</h3>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit </p>
          <img src="image/v1.jpg" alt="">
        </div>
        <div class="item">
          <div class="heading">
            <h5>Booking Memories</h5>
            <h3>Locations voitures</h3>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit </p>
          <img src="image/t1.jpg" alt="">
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js"></script>
  <script>
    $('.owl-carousel').owlCarousel({
      loop: true,
      margin: 10,
      nav: true,
      dots: false,
      navText: ["<i class='far fa-long-arrow-alt-left'></i>", "<i class='far fa-long-arrow-alt-right'></i>"],
      responsive: {
        0: {
          items: 1
        },
        768: {
          items: 1
        },
        1000: {
          items: 1
        }
      }
    })
  </script>
  <section class="wrapper2" id="wrapper2">
    <div class="container">
      <div class="heading mtop">
        <h5>**************</h5>
        <h2>Nos Services</h2>
      </div>
      <div class="content grid mtop">
        <div class="box">
          <i class="fas fa-shipping-fast"></i>
          <h3>Balades et croisière</h3>
          <p>On vous garantie de vivre l'expérience la plus amusante</p>
          <span class='fas fa-bed-arrow-alt-right'></span>
        </div>
        <div class="box">
          <i class="fas fa-mug-hot"></i><i class="fa-solid fa-plate-utensils"></i>
          <h3>Restaurants & Cafés</h3>
          <p>On vous choisit les meuilleurs restaurants et cafés selon votre bugets</p>
          <span class='far fa-long-arrow-alt-right'></span>
        </div>
        <div class="box">
          <i class="fas fa-car"></i>
          <h3>Voitures locations & Taxi</h3>
          <p>on vous mettre en disposition des voitures de location et des Taxi 24/24h et 7/7j</p>
          <span class='far fa-long-arrow-alt-right'></span>
        </div>
        <div class="box">
          <i class="far fa-water"></i>
          <h3>Piscines & Spa</h3>
          <p>vous bénéficiez des les plus beaux piscines et spa</p>
          <span class='far fa-long-arrow-alt-right'></span>
        </div>
      </div>
    </div>
  </section>
  <section class="offer2 about wrapper timer top" id="shop">
    <div class="container">
      <div class="heading">
        <h5>********</h5>
        <h3>Offres Exclusives</h3>
      </div>

      <div class="content grid  top">
        <div class="box">
          <h5>Jusqu'au 50%</h5>
          <h3>Offre Famille</h3>
          <p>Notre offre Famille à partir 50%</p>
          <button class="flex1" onclick="window.location.href='./Hotel.php#reservation';">
            <span>Réserver</span>
            <i class="fas fa-arrow-circle-right"></i>
          </button>
        </div>
        <div class="box">
          <h5>Jusqu'au 30%</h5>
          <h3>Offre Business</h3>
          <p>Lorem ipsum, dolor sit amet consectetur </p>
          <button class="flex1" onclick="window.location.href='./Hotel.php#reservation';">
            <span>Réserver</span>
            <i class="fas fa-arrow-circle-right"></i>
          </button>
        </div>
        <div class="box">
          <h5>Jusqu'au 15%</h5>
          <h3>Autres Offres</h3>
          <p>Lorem ipsum dolor sit amet consectetur,.</p>
          <button class="flex1"onclick="window.location.href='./Hotel.php#reservation';">
            <span>Réserver</span>
            <i class="fas fa-arrow-circle-right"></i>
          </button>
        </div>
      </div>
    </div>
  </section>
  
<section class="gallary top" id="gallary">
  <h2>Gallerie</h2>
  <div class="owl-carousel owl-theme">
    <div class="item">
      <img src="image/g1.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g2.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g3.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g4.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g5.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g1.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g2.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
    <div class="item">
      <img src="image/g3.jpg" alt="" />
      <div class="overlay">
        <i class="fab fa-instagram"></i>
      </div>
    </div>
  </div>
</section>

<script>
  $(".owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    dots: false,
    autoplay: true,
    slideTransition: "linear",
    autoplayTimeout: 4000,
    autoplaySpeed: 4000,
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 1,
      },
      768: {
        items: 3,
      },
      1000: {
        items: 5,
      },
    },
  });
</script>

  <?php 
include "includes/footer.php";
?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
// Lorsque le DOM est complètement chargé, exécute le script
$(document).ready(function() {

  // Variables de validation pour chaque champ
  var allValid = false;               // Validation globale du formulaire
  var nameValid = false;              // Champ "nom"
  var emailValid = false;             // Champ "email"
  var numberValid = false;            // Champ "numéro de téléphone"
  var passwordValid = false;          // Champ "mot de passe"
  var passwordconfirmValid = true;    // Champ "confirmation mot de passe"

  // Événement lors de la saisie dans le champ nom
  $("#name").keyup(function(){
    var nombreMots = jQuery.trim($(this).val()).split(' ').length; // Compte le nombre de mots
    if($(this).val() === '') {
         nombreMots = 0;
    }
    // Vérifie que le nom contient au moins deux mots
    if(nombreMots < 2){
        $("small#res_name").show();
        nameValid = false;
        $("#res_name").css("color", "red").html("Trop court il faut au moins deux mots nom et prenom ");
    } else {
        $("small#res_name").hide();
        nameValid = true;
    }
    verif_tout(); // Vérifie tous les champs après mise à jour
  });

  /************** Validation email ***************/
  // À chaque frappe dans le champ email
  $("#emailReg").keyup(function(){
    verif_email();   // Lance une fonction AJAX
    verif_tout();
  });

  // Fonction AJAX pour valider l’email
  function verif_email(){
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'emailRegister': $("#emailReg").val() // Envoie la valeur du champ email
        },
        success: function(data){
            if(data == "success"){
              emailValid = true;
              $("small#res_email").hide();
            } else {
              emailValid = false;
              $("small#res_email").show();
              $("#res_email").css("color", "red").html(data);
            }
        }
    });
  }

  /*************** Validation numéro téléphone ***************/
  $("#phone").keyup(function(){
    verif_number();
    verif_tout();
  });

  function verif_number(){
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'phone': $("#phone").val()
        },
        success: function(data){
            if(data == "success"){
              numberValid = true;
              $("small#res_phone").hide();
            } else {
              numberValid = false;
              $("small#res_phone").show();
              $("#res_phone").css("color", "red").html(data);
            }
        }
    });
  }

  /************* Validation mot de passe *************/
  $("#passwordReg").keyup(function(){
    var password = $(this).val(); // Récupère la valeur du mot de passe

    // Vérifie les critères de sécurité
    if (password.length < 4 ) {
      $("#res_passwordReg").css("color", "red").html("Trop court il faut au moins 4 caractères ");
      $("small#res_passwordReg").show();
      passwordValid = false;

    } else if (!/[A-Z]/.test(password)) {
      $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins une lettre majuscule");
      $("small#res_passwordReg").show();
      passwordValid = false;

    } else if (!/[a-z]/.test(password)) {
      $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins une lettre minuscule");
      $("small#res_passwordReg").show();
      passwordValid = false;

    } else if (!/\d/.test(password)) {
      $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins un chiffre");
      $("small#res_passwordReg").show();
      passwordValid = false;

    } else if (!/[$@!%*?&]/.test(password)) {
      $("#res_passwordReg").css("color", "red").html("Le mot de passe doit contenir au moins un caractère spécial");
      $("small#res_passwordReg").show();
      passwordValid = false;

    } else if ($("#confirmPassword").val() !== "" && $("#confirmPassword").val() !== $("#passwordReg").val()) {
      $("small#res_passwordReg").show();
      $("#res_passwordReg").css("color", "red").html("Les deux mots de passe sont différents");
      $("#res_confirmPassword").css("color", "red").html("Les deux mots de passe sont différents");
      passwordValid = false;

    } else {
      passwordValid = true;
      $("small#res_passwordReg").hide();
    }

    verif_tout(); // Vérifie tous les champs
  });

  /************* Validation confirmation mot de passe *************/
  $("#confirmPassword").keyup(function(){
    if ($("#confirmPassword").val() !== "" && $("#confirmPassword").val() !== $("#passwordReg").val()) {
      $("small#res_confirmPassword").show();
      $("#res_confirmPassword").css("color", "red").html("Les deux mots de passe sont différents");
      passwordconfirmValid = false;
      $("small#res_passwordReg").hide();
    } else {
      passwordconfirmValid = true;
      $("small#res_passwordReg").hide();
      verifPhp_password(); // Vérification côté serveur
    }
    verif_tout();
  });

  // Vérification serveur de la confirmation du mot de passe
  function verifPhp_password(){
    $.ajax({
        type: "post",
        url:  "valid.php",
        data: {
            'password': $("#passwordReg").val(),
            'passwordConfirm': $("#confirmPassword").val()
        },
        success: function(data){
            if(data == "success"){
              passwordconfirmValid = true;
              passwordValid = true;
              $("small#res_confirmPassword").hide();
            } else {
              passwordconfirmValid = false;
              $("small#res_confirmPassword").show();
              $("#res_confirmPassword").css("color", "red").html(data);
            }
        }
    });
  }

  /************* Vérifie tous les champs *************/
  function verif_tout(){
    if (nameValid && emailValid && numberValid && passwordValid && passwordconfirmValid){
      allValid = true;
      $("#res-all").css("color", "green").html("Vous pouvez envoyer votre formulaire");
      $('#res-all').attr("disabled", false); // Active le bouton
    } else {
      allValid = false;
      $("#res-alll").css("color", "red").html("Veuillez remplir tous les champs avant d’envoyer le formulaire");
      $('#res-all').attr("disabled", true); // Désactive le bouton
    }
  }

  /************* Soumission AJAX du formulaire *************/
  $("#form_inscription").submit(function(){
    // Récupération des valeurs
    var name = $("#name").val();
    var email = $("#emailReg").val();
    var number = $("#phone").val();
    var passwordReg = $("#passwordReg").val();
    var confirmPassword = $("#confirmPassword").val();

    // Envoi AJAX au serveur
    $.ajax({
        type: "post",
        url: "valid.php",
        async: false, // ⚠️ Attention : bloquant, à éviter
        data: {
            'name': name,
            'phone': number,
            'emailRegister': email,
            'passwordReg': passwordReg,
            'confirmPassword': confirmPassword,
        },
        success: function(data){
            if(data != "successsuccessregister_success"){
              $("#res-all").css("color", "red").html(data);
            } else {
              // Cache le formulaire et affiche un message de bienvenue
              $("#container-form-sign").hide();
              $("#welcome").html("<h2>Bienvenue " + name + " ! Vos données sont enregistrées avec succès</h2>");
            }
        }
    });
  });

}); // Fin de $(document).ready


</script>
<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php';?>
 
</body>
</html>
