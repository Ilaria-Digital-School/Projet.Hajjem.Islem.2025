<?php

  $db_name = 'mysql:host=localhost;dbname=hotel_db'; // Chaîne de connexion PDO avec nom d'hôte et base de données
$db_user_name = 'root'; // Nom d'utilisateur de la base de données (par défaut sur localhost)
$db_user_pass = ''; // Mot de passe de l'utilisateur (vide pour root en local généralement)


  $conn = new PDO($db_name, $db_user_name, $db_user_pass); // Création de l'objet PDO pour établir la connexion


   function create_unique_id(){
      $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'; // Ensemble de caractères autorisés
      $rand = array(); // Initialise un tableau vide pour stocker les caractères choisis
      $length = strlen($str) - 1; // Calcule l'index maximum utilisable dans la chaîne $str
      for($i = 0; $i < 20; $i++){
      $n = mt_rand(0, $length); // Génère un index aléatoire
      $rand[] = $str[$n]; // Ajoute le caractère correspondant dans le tableau
   }

   return implode($rand); // Combine les caractères du tableau en une seule chaîne et la retourne

   }

?>