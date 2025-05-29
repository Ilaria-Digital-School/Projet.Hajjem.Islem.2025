<?php
// Démarre une session pour gérer les variables de session
session_start();

// Inclut la connexion à la base de données
include 'components/connect.php';

// Vérifie si l'utilisateur est connecté via l'ID en session
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];   // Récupère l'ID utilisateur

    // Prépare une requête pour récupérer le nom, email et numéro de téléphone de l'utilisateur
    $stmt = $conn->prepare("SELECT name, email, number FROM users WHERE id = ?");
    $stmt->execute([$user_id]);   // Exécute la requête avec l'ID utilisateur

    // Si un utilisateur est trouvé dans la BDD
    if ($stmt->rowCount() > 0) {
        // Récupère les données utilisateur sous forme de tableau associatif
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stocke les valeurs dans des variables
        $name = $user_data['name'];
        $email = $user_data['email'];
        $number = $user_data['number'];
    }
}

// Si le formulaire de réservation est soumis (bouton 'book' pressé)
if(isset($_POST['book']))
{
    // Clé secrète Google reCAPTCHA (pour la vérification du captcha côté serveur)
    $recaptcha_secret = '6LcVrygrAAAAAPrWpLlXpmW17UydR9R55Gc5g_Gw'; 

    // Récupère la réponse envoyée par le widget reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // URL de l'API Google reCAPTCHA pour la vérification
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

    // Envoie la requête à l'API avec la clé secrète, la réponse captcha et l'IP du client
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response . '&remoteip=' . $_SERVER['REMOTE_ADDR']);

    // Décode la réponse JSON de l'API en objet PHP
    $recaptcha = json_decode($recaptcha);

    // Si la vérification du captcha échoue
    if (!$recaptcha->success) {
        // Ajoute un message d'avertissement pour captcha invalide
        $warning_msg[] = 'Captcha invalide. Veuillez cocher la case "Je ne suis pas un robot".';
    } else {
        // Sinon, le captcha est validé, on continue avec la logique de réservation

        // Génère un identifiant unique pour la réservation (fonction externe à définir)
        $booking_id = create_unique_id();

        // Récupère et nettoie les données POST envoyées par le formulaire
        $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $number = filter_var($_POST['number'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $rooms = filter_var($_POST['rooms'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $check_in = filter_var($_POST['check_in'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $check_out = filter_var($_POST['check_out'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $adults = filter_var($_POST['adults'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $childs = filter_var($_POST['childs'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $total_rooms = 0;  // Initialisation du compteur de chambres réservées

        // Vérifie les réservations existantes pour la même date d'arrivée
        $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
        $check_bookings->execute([$check_in]);

        // Parcourt toutes les réservations trouvées et somme le nombre de chambres réservées
        while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
            $total_rooms += $fetch_bookings['rooms'];
        }

        // Si le nombre total de chambres réservées dépasse ou égale 30, plus de dispo
        if($total_rooms >= 30){
            $warning_msg[] = 'Chambre non disponible';
        } else {
            // Vérifie si l'utilisateur a déjà une réservation identique (évite doublons)
            $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
            $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

            // Si une réservation identique existe
            if($verify_bookings->rowCount() > 0){
                $warning_msg[] = 'Chambre déja reserver';
            } else {
                // Sinon, insère la nouvelle réservation en base de données
                $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
                $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
                $success_msg[] = 'Chambre reservée avec succes!';
            }
        }
    }
}
?>

<?php
// Chargement automatique des classes via autoload (probablement pour reCAPTCHA PHP client)
require_once './autoload.php';

// Récupère l'adresse IP du client
$remoteIp = $_SERVER['REMOTE_ADDR'];

// Si un autre formulaire est soumis avec le bouton 'ok'
if (isset($_POST['ok'])){
    // Crée un nouvel objet ReCaptcha avec la clé secrète
    $recaptcha = new \ReCaptcha\ReCaptcha("6LcVrygrAAAAAPrWpLlXpmW17UydR9R55Gc5g_Gw");

    // Récupère la réponse du reCAPTCHA depuis le formulaire
    $gRecaptchaResponse = $_POST['g-recaptcha-response'];

    // Configure l'hôte attendu (localhost et 127.0.0.1 ici), puis vérifie le captcha avec la réponse et l'IP client
    $resp = $recaptcha->setExpectedHostname('localhost', '127.0.0.1')
                      ->verify($gRecaptchaResponse, $remoteIp);

    // Si la vérification est un succès
    if ($resp->isSuccess()) {
        echo "succes!";
        // Le captcha est validé, on peut continuer avec la logique souhaitée
    } else {
        // Sinon, récupère les erreurs retournées et les affiche (debug)
        $errors = $resp->getErrorCodes();
        var_dump($errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title>Hotel</title>
  <meta name="description" >
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php 
include "includes/header.php";
?>
   <header class="header">
    <div class="container">
      <nav class="navbar flex1">
        <div class="sticky_logo logo">
          <img src="image/logo.png" alt="">
        </div>
        <ul class="nav-menu">
          <li> <a href="./">Accueil</a> </li>
          <li> <a href="./Hotel.php">BookHotel</a> </li>
          <li> <a href="./Reservations.php">Reservations</a> </li>
          <li> <a href="#room">Nos services</a> </li>
          <li> <a href="#services">Nos Offres Exclusives</a> </li>
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
  <section class="hotel" id="hotel">
    <div class="container">   
      <h1>Booking Memories</h1>
      <p>On vous garantie un meuilleur séjour</p>
    </div>
  </section>
  <section class="room wrapper2 top" id="room">
    <div class="container">
      <div class="heading">
        <h5>**********</h5>
        <h2>Nos Services</h2>
      </div>
      <div class="content flex mtop">
        <div class="left grid2">
          <div class="box">
            <i class="fas fa-desktop"></i>
            <p>Prix Bas</p>
            <h3>Pas des frais de réservation</h3>
          </div>
          <div class="box">
            <i class="fas fa-dollar-sign"></i>
            <p>Prix Bas</p>
            <h3>Un meuilleur tarif</h3>
          </div>
          <div class="box">
            <i class="fab fa-resolving"></i>
            <p>Prix Bas</p>
            <h3>Reservations 24/7</h3>
          </div>
          <div class="box">
            <i class="fal fa-alarm-clock"></i>
            <p>Prix Bas</p>
            <h3>Excellente Service des Chambres</h3>
          </div>
          <div class="box">
            <i class="fas fa-mug-hot"></i>
            <p>Prix Bas</p>
            <h3>Petit déjeuner gratuit</h3>
          </div>
          <div class="box">
            <i class="fas fa-user-tie"></i>
            <p>Prix Bas</p>
            <h3>Séjour bébé gratuit</h3>
          </div>
        </div>
        <div class="right">
          <img src="image/r.jpg" alt="">
        </div>
      </div>
    </div>
  </section>
  <section class="offer mtop" id="services">
    <div class="container">
      <div class="heading">
        <h5>*********</h5>
        <h3>Nos Offres Exclusives
        </h3>
      </div>

      <div class="content grid2 mtop">
        <div class="box flex">
          <div class="left">
            <img src="image/o1.jpg" alt="">
          </div>
          <div class="right">
            <h4>Chambre de Luxe</h4>
            <div class="rate flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p> Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <h5>à partir de 250euro/nuit</h5>
            <button class="flex1" onclick="window.location.href='Hotel.php#reservation';">
              <span>Réserver</span>
              <i class="fas fa-arrow-circle-right"></i>
            </button>
          </div>
        </div>
        <div class="box flex">
          <div class="left">
            <img src="image/o2.jpg" alt="">
          </div>
          <div class="right">
            <h4>Suite Familiale Hotel</h4>
            <div class="rate flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p> Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <h5>à partir de 150euro/nuit</h5>
            <button class="flex1" onclick="window.location.href='Hotel.php#reservation';">
              <span>Réserver</span>
              <i class="fas fa-arrow-circle-right"></i>
            </button>
          </div>
        </div>
        <div class="box flex">
          <div class="left">
            <img src="image/o3.jpg" alt="">
          </div>
          <div class="right">
            <h4>Chambre Hotel belle vue</h4>
            <div class="rate flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p> Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <h5>à partir de 80euro/nuit</h5>
            <button class="flex1" onclick="window.location.href='./Hotel.php#reservation';">
              <span>Réserver</span>
              <i class="fas fa-arrow-circle-right"></i>
            </button>
          </div>
        </div>
        <div class="box flex">
          <div class="left">
            <img src="image/o4.jpg" alt="">
          </div>
          <div class="right">
            <h4>Chambre Economique</h4>
            <div class="rate flex">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p> Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <h5>à partir de 50euro/nuit</h5>
            <button class="flex1" onclick="window.location.href='Hotel.php#reservation';">
              <span>Réserver</span>
              <i class="fas fa-arrow-circle-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>


<section class="reservation" id="reservation">

      <?php if (!empty($warning_msg)): ?>
   <?php foreach ($warning_msg as $msg): ?>
      <div class="alert warning"><?= htmlspecialchars($msg) ?></div>
   <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($success_msg)): ?>
   <?php foreach ($success_msg as $msg): ?>
      <div class="alert success"><?= htmlspecialchars($msg) ?></div>
   <?php endforeach; ?>
<?php endif; ?>

<form action="" method="post">
   <h3>Réservation</h3>
   <div class="flex">
      <div class="box">
         <p>Nom <span>*</span></p>
         <input type="text" name="name" maxlength="50" placeholder="Entrez votre nom" class="input" value="<?= isset($user_data['name']) ? htmlspecialchars($user_data['name']) : '' ?>" required>
      </div>
      <div class="box">
         <p>Email <span>*</span></p>
         <input type="email" name="email" maxlength="50" placeholder="Entrez votre email" class="input" value="<?= isset($user_data['email']) ? htmlspecialchars($user_data['email']) : '' ?>" required>
      </div>
      <div class="box">
         <p>Numéro <span>*</span></p>
         <input type="number" name="number" maxlength="10" min="0" max="9999999999" placeholder="Entrez votre numéro" class="input" value="<?= isset($user_data['number']) ? htmlspecialchars($user_data['number']) : '' ?>" required>
      </div>
      <div class="box">
         <p>Chambres<span>*</span></p>
         <select name="rooms" class="input" required>
            <option value="1" selected>1 chambre</option>
            <option value="2">2 chambres</option>
            <option value="3">3 chambres</option>
            <option value="4">4 chambres</option>
            <option value="5">5 chambres</option>
            <option value="6">6 chambres</option>
         </select>
      </div>
      <div class="box">
         <p>check in <span>*</span></p>
         <input type="date" name="check_in" class="input" required>
      </div>
      <div class="box">
         <p>check out <span>*</span></p>
         <input type="date" name="check_out" class="input" required>
      </div>
      <div class="box">
         <p>Adultes <span>*</span></p>
         <select name="adults" class="input" required>
            <option value="1" selected>1 adulte</option>
            <option value="2">2 adultes</option>
            <option value="3">3 adultes</option>
            <option value="4">4 adultes</option>
            <option value="5">5 adultes</option>
            <option value="6">6 adultes</option>
         </select>
      </div>
      <div class="box">
         <p>Enfants <span>*</span></p>
         <select name="childs" class="input" required>
            <option value="0" selected>0 Enfant</option>
            <option value="1">1 Enfant</option>
            <option value="2">2 Enfants</option>
            <option value="3">3 Enfants</option>
            <option value="4">4 Enfants</option>
            <option value="5">5 Enfants</option>
            <option value="6">6 Enfants</option>
         </select>
      </div>
    </div>
   <input type="submit" value="Réserver maintenant" name="book" class="btn" id="btn">  
    <div class="g-recaptcha" data-sitekey="6LcVrygrAAAAAJoq_R_M2HmN_9BF7xYjYxxYSHsG"></div>
      <br/>
      <input type="submit" name="ok" value="Submit">
  
</form>
</section>

  <?php 
include "includes/footer.php";
?>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
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
<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php';?>
</body>


</html>