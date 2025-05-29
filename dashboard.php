<?php
session_start();
include '../components/connect.php';



if (!isset($_SESSION['id'])) {
    header('Location: index.php'); // redirige vers la page de connexion
    exit;
}
$user_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body class="dash">
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Dashboard</h1>

   <div class="box-container">

   <div class="box">
      <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
         $select_profile->execute([$user_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      
         if ($fetch_profile) {
             echo "<h3>Welcome!</h3>";
             echo "<p>{$fetch_profile['name']}</p>";
         } else {
             echo "<h3>Welcome!</h3>";
             echo "<p>Aucun profil trouvé</p>";
         }
      ?>
      <a href="update.php" class="btn">Update Profile</a>
   </div>

   <div class="box">
      <?php
         $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
         $select_bookings->execute();
         $count_bookings = $select_bookings->rowCount();
      ?>
      <h3><?= $count_bookings; ?></h3>
      <p>Total Bookings</p>
      <a href="bookings.php" class="btn">View Bookings</a>
   </div>

   <div class="box">
      <?php
         $select_admins = $conn->prepare("SELECT * FROM `users`");
         $select_admins->execute();
         $count_admins = $select_admins->rowCount();
      ?>
      <h3><?= $count_admins; ?></h3>
      <p>Total Admins</p>
      <a href="admins.php" class="btn">View Admins</a>
   </div>

   <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `message`");
         $select_messages->execute();
         $count_messages = $select_messages->rowCount();
      ?>
      <h3><?= $count_messages; ?></h3>
      <p>Total Messages</p>
      <a href="messages.php" class="btn">View Messages</a>
   </div>

   <div class="box">
      <h3>Quick Select</h3>
      <p>Register</p>
      <a href="register.php" class="btn" style="margin-left: 1rem;">Register</a>
   </div>

   </div>

</section>

<!-- dashboard section ends -->
</body>
</html>





















<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>