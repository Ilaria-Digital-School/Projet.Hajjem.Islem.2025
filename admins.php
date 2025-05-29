<?php 
session_start();
include '../components/connect.php';


// Suppression d'un administrateur
if(isset($_POST['delete'])){ // Vérifie si le formulaire de suppression a été soumis
    $delete_id = $_POST['delete_id']; // Récupère l'ID à supprimer depuis le formulaire
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_SPECIAL_CHARS); // Assainit la valeur pour éviter les caractères spéciaux

    // Vérifie si un admin avec cet ID existe
    $verify_delete = $conn->prepare("SELECT * FROM `users` WHERE id = ? AND role = 'admin'");
    $verify_delete->execute([$delete_id]);

    if($verify_delete->rowCount() > 0){ // Si un admin est trouvé
        // Supprime l'admin de la base de données
        $delete_admin = $conn->prepare("DELETE FROM `users` WHERE id = ? AND role = 'admin'");
        $delete_admin->execute([$delete_id]);
        $success_msg[] = 'Administrateur supprimé !'; // Message de succès
    } else {
        $warning_msg[] = 'Administrateur déjà supprimé ou inexistant !'; // Message d’avertissement
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="grid">
    <h1 class="heading">Administrateurs</h1>

    <div class="box-container">
        <div class="box" style="text-align: center;">
            <p>Créer un nouvel administrateur</p>
            <a href="register.php" class="btn">S'inscrire</a>
        </div>

        <?php
        // Récupère tous les utilisateurs qui ont le rôle "admin"
        $select_admins = $conn->prepare("SELECT * FROM `users` WHERE role = 'admin'");
        $select_admins->execute();

        if($select_admins->rowCount() > 0){
            while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){
        ?>
        <div class="box">
            <p>Nom : <span><?= htmlspecialchars($fetch_admins['name']); ?></span></p>
            <form action="" method="POST">
                <input type="hidden" name="delete_id" value="<?= $fetch_admins['id']; ?>">
                <button type="submit" name="delete" class="btn">Supprimer</button>
            </form>
        </div>

        <?php
            }
        }
        ?>

    </div>
</section>

</body>
</html>
