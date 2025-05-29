<?php
session_start();           // Démarre la session pour pouvoir accéder aux données de session
session_destroy();         // Détruit toutes les données de la session en cours (déconnexion)
header('location:index.php');  // Redirige immédiatement l’utilisateur vers la page d'accueil (index.php)
?>
