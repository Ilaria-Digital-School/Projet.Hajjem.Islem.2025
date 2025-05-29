<?php

if(isset($success_msg)){ // Vérifie si le tableau $success_msg existe
   foreach($success_msg as $success_msg){ // Boucle à travers les messages de succès
      echo '<script>swal("'.$success_msg.'", "" ,"success");</script>'; // Affiche chaque message avec SweetAlert
   }
}


if(isset($warning_msg)){
   foreach($warning_msg as $warning_msg){
      echo '<script>swal("'.$warning_msg.'", "" ,"warning");</script>';
   }
}


if(isset($info_msg)){
   foreach($info_msg as $success_msg){ // Erreur : $success_msg est utilisé au lieu de $info_msg
      echo '<script>swal("'.$info_msg.'", "" ,"info");</script>'; // Utilise le tableau entier au lieu d’un seul message
   }
}


if(isset($error_msg)){
   foreach($error_msg as $error_msg){
      echo '<script>swal("'.$error_msg.'", "" ,"error");</script>';
   }
}

?>
