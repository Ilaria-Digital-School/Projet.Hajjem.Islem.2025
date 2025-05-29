<?php
session_start(); // Démarre la session PHP ou reprend la session existante

include 'components/connect.php'; // Inclut le fichier de connexion à la base de données

$name = '';  // Initialise la variable $name avec une chaîne vide par défaut
$email = ''; // Initialise la variable $email avec une chaîne vide par défaut

// Vérifie si la variable de session 'id' existe (utilisateur connecté)
if (isset($_SESSION['id'])) {

    $user_id = (int) $_SESSION['id']; // Récupère l'ID utilisateur depuis la session et le convertit en entier pour plus de sécurité

    try {
        // Prépare la requête SQL pour récupérer le nom et l'email de l'utilisateur correspondant à l'ID
        $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");

        // Exécute la requête en passant l'ID utilisateur en paramètre
        $stmt->execute([$user_id]);

        // Vérifie si au moins une ligne a été retournée (utilisateur trouvé)
        if ($stmt->rowCount() > 0) {

            // Récupère les données sous forme de tableau associatif
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Stocke le nom récupéré dans la variable $name
            $name = $user_data['name'];

            // Stocke l'email récupéré dans la variable $email
            $email = $user_data['email'];
        }

        $stmt = null; // Libère la ressource de la requête préparée
    } catch (PDOException $e) {
        // En cas d'erreur lors de la requête, on log l'erreur (à adapter selon besoin)
        error_log('Database error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <!-- nom site -->
  <title>Contact</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
  // Sélectionne l'élément avec la classe "hamburger" (bouton menu mobile)
  const hamburger = document.querySelector(".hamburger");

  // Sélectionne l'élément avec la classe "nav-menu" (menu de navigation)
  const navMenu = document.querySelector(".nav-menu");

  // Ajoute un écouteur d'événement au clic sur le bouton hamburger
  hamburger.addEventListener("click", mobliemmenu);

  // Fonction appelée quand on clique sur le bouton hamburger
  function mobliemmenu() {
    // Active ou désactive la classe "active" sur le bouton hamburger (animation X)
    hamburger.classList.toggle("active");

    // Active ou désactive la classe "active" sur le menu (affichage ou masquage du menu)
    navMenu.classList.toggle("active");
  }

  // Ajoute un écouteur d'événement au scroll de la fenêtre
  window.addEventListener("scroll", function() {
    // Sélectionne l'élément <header>
    var header = document.querySelector("header");

    // Ajoute la classe "sticky" au header si la page est scrollée (scrollY > 0), sinon la retire
    header.classList.toggle("sticky", window.scrollY > 0);
  })
</script>

<!------ Include the above in your HEAD tag ---------->

<div class="jumbotron jumbotron-sm">
  <div class="container">
      <div class="row">
          <div class="col-sm-12 col-lg-12">
              <h1 class="h1">
                  Contactez Nous <small>N'hésitez pas à communiquer avec nous pour tous questions, commentaires ou suggestions </small></h1>
          </div>
      </div>
  </div>
</div>


<div class="container">
  <div class="row">
      <div class="col-md-8">
          <div class="well well-sm">
            <?php    
            if (!empty($_GET["send"]) && $_GET["send"]== "ok") 
            {
              echo "message bien envoyé";
            }
            
            ?>
              <form action="php/sendContact.php" method="POST">
  <!-- Ajout du reCAPTCHA -->
  <div class="g-recaptcha" data-sitekey="6LcVrygrAAAAAJoq_R_M2HmN_9BF7xYjYxxYSHsG"></div>
  <br/>
  <!-- Champs du formulaire -->
  <input type="text" class="form-control" name="name" placeholder="Votre nom" value="<?= isset($user_data['name']) ? htmlspecialchars($user_data['name']) : '' ?>" required>
  <input type="email" class="form-control" name="email" placeholder="Votre email" value="<?= isset($user_data['email']) ? htmlspecialchars($user_data['email']) : '' ?>" required>
  <select name="subject" class="form-control" required>
    <option value="">Sélectionnez un sujet</option>
    <option value="service">Service clients</option>
    <option value="suggestions">Suggestions</option>
    <option value="product">Support technique</option>
  </select>
  <textarea name="message" class="form-control" placeholder="Message" required></textarea>
  <button type="submit" class="btn btn-primary">Envoyer</button>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

          </div>
      </div>
      <div class="col-md-4">
          <form>
          <legend><span class="glyphicon glyphicon-globe"></span>Notre Bureau</legend>
          <address>
              <strong>Adresse</strong><br>
              Boulevard de l'Europe<br>
              Rouen, France<br>
              <abbr title="Phone">
                 Tél:</abbr>
              (123) 12345678
          </address>
          <address>
              <strong>Adresse Email</strong><br>
              <a href="mailto:#">BookingMemories@example.com</a>
          </address>
          </form>
      </div>
  </div>
</div>

  <iframe id="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5190.068788077612!2d1.0812870756159918!3d49.427163771414975!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e0de6e126b90b9%3A0x86bc515250d93d53!2sBd%20de%20l&#39;Europe%2C%2076100%20Rouen!5e0!3m2!1sfr!2sfr!4v1731408404161!5m2!1sfr!2sfr" width="1250" height="550" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

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
 
