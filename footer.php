<?php 

if(isset($_SESSION['email'])) {
?>
    <h2>Bienvenue <?php echo htmlspecialchars($_SESSION['name']); ?> ! Vos données sont enregistrées avec succès</h2>
    <a href="logout.php">Se déconnecter</a>

<?php 
} else {

require_once './autoload.php';
$remoteIp = $_SERVER['REMOTE_ADDR'];

if (isset($_POST['ok'])) {
    $recaptcha = new \ReCaptcha\ReCaptcha("6LcVrygrAAAAAPrWpLlXpmW17UydR9R55Gc5g_Gw");
    $gRecaptchaResponse = $_POST['g-recaptcha-response'];

    $resp = $recaptcha->setExpectedHostname('localhost')
                      ->verify($gRecaptchaResponse, $remoteIp);
    if ($resp->isSuccess()) {
        echo "<p style='color:green;'>Succès ! reCAPTCHA validé.</p>";
    } else {
        $errors = $resp->getErrorCodes();                                                                               
        echo "<p style='color:red;'>Erreur reCAPTCHA : ";
        var_dump($errors);
        echo "</p>";
    }
}
?>

<div id="welcome">
    <h2>N'oubliez pas de vous connecter ou de vous inscrire</h2>
    <p>Pour bénéficier de nos meilleures offres</p>
</div>

<div class="container-form" id="container-form-sign">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <form method="POST">
        <div class="g-recaptcha" data-sitekey="6LcVrygrAAAAAJoq_R_M2HmN_9BF7xYjYxxYSHsG"></div>
        <br/>
        <input type="submit" name="ok" value="Vérifier le reCAPTCHA">
    </form>

    <div class="col">
        <div class="sign-in">
            <h2 class="form-title">Se connecter</h2>
            <form action="verifier.php" method="post" id="form-login">
                <input type="text" class="input-form form-control" placeholder="Entrer votre nom" name="name" id="nameLogin" required>
                <input type="email" class="input-form form-control" placeholder="Entrer votre email" name="email" id="emailLogin" required>
                <input type="password" class="input-form" placeholder="Entrer votre mot de passe" name="password" id="passwordLogin" required>
                <button type="submit" class="btn-sub">Se connecter</button>
            </form>
        </div>
    </div>
    
    <div class="col">
        <div class="sign-up">
            <h2 class="form-title">Inscription</h2>
          <form class="form-inscription" id="form_inscription" onsubmit="return false;">
        <input type="text" id="name"  class="input-form" name="name" required placeholder="entrer votre nom">
        <small id="res_name" class="avert"></small>
        <input type="email" id="emailReg" name="email" class="input-form" placeholder="entrer votre email" required>
        <small id="res_email" class="avert"></small>
        <input type="phone" id="phone" name="phone" placeholder="entrer votre numero de telephone" class="input-form" required>
        <small id="res_phone" class="avert"></small>
    
        <input  class="input-form" type="password" id="passwordReg" name="password" placeholder="entrer votre .mot de passe" required>
        <small id="res_passwordReg" class="phone"></small>
        <input  class="input-form" type="password" id="confirmPassword" name="confirmPassword" placeholder="confirmer votre mot de passe" required>
        <small id="res_confirmPassword" class="avert"></small>
        <input type="submit" class="btn-sub" value="S'inscrire">
    </form>
            <small id="res-all" class="avert"></small>
        </div>
    </div>
</div>

<?php } ?>

<!-- Début Footer -->
<footer>
    <div class="content grid top">
        <div class="box">
            <div class="logo">
                <img src="image/logo.png" alt="Logo" />
            </div>
            <p>
                Vous pouvez rejoindre Booking Memories sur Instagram, Twitter, Facebook et sur notre chaîne YouTube.
            </p>
            <div class="social flex">
                <i class="fab fa-facebook-f"></i>
                <i class="fab fa-twitter"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-youtube"></i>
            </div>
        </div>

        <div class="box">
            <h2>Liens Rapides</h2>
            <ul>
                <li><i class="fas fa-angle-double-right"></i> Réservation</li>
                <li><i class="fas fa-angle-double-right"></i> FAQ</li>
                <li><i class="fas fa-angle-double-right"></i> Contact</li>
            </ul>
        </div>

        <div class="box">
            <h2>Services</h2>
            <ul>
                <li><i class="fas fa-angle-double-right"></i> Accompagnement pour personnes âgées et mineures</li>
                <li><i class="fas fa-angle-double-right"></i> Taxi et location de voiture</li>
                <li><i class="fas fa-angle-double-right"></i> Événements</li>
                <li><i class="fas fa-angle-double-right"></i> Restaurants et cafés</li>
            </ul>
        </div>

        <div class="box">
            <h2>Contact</h2>
            <div class="icon flex">
                <div class="i"><i class="fas fa-map-marker-alt"></i></div>
                <div class="text">
                    <h3>Adresse</h3>
                    <p>Boulevard de l'Europe, 76100 Rouen</p>
                </div>
            </div>
            <div class="icon flex">
                <div class="i"><i class="fas fa-phone"></i></div>
                <div class="text">
                    <h3>Téléphone</h3>
                    <p>000123 456 7898</p>
                </div>
            </div>
            <div class="icon flex">
                <div class="i"><i class="far fa-envelope"></i></div>
                <div class="text">
                    <h3>Email</h3>
                    <p>BookingMemories@gmail.com</p>
                </div>
            </div>
        </div>
    </div>
</footer>
