<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="bg-light py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Logo et Accueil -->
            <div class="d-flex align-items-center">
                <a href="../index.php">
                    <img src="../asset/logo.jpg" alt="logo" style="width: 120px; height: 60px;">
                </a>
                <a href="../index.php" class="ms-3 title-acceuil">Accueil</a>
            </div>

            <!-- Connexion / Déconnexion / Profil -->
            <?php
            $isLoggedIn = isset($_SESSION['pUserData']);
            if ($isLoggedIn) {
                // Afficher un lien vers le profil et le bouton de déconnexion
                echo '<div class="d-flex align-items-center">';
                echo '<a href="../index.php?action=profile&userId='.$_SESSION['pUserData']['_id'].'" class="me-2"><img src="../asset/default_avatar.png" width="55px" height="55px" alt="Profile Picture"></a>';
                echo '<a href="../index.php?action=logout" class="btn btn-danger">Déconnexion</a>';
                echo '</div>';
            } else {
                // Afficher le bouton de connexion
                echo '<a href="../index.php?action=login" class="btn btn-primary">Connexion</a>';
            }
            ?>
        </div>
    </div>
</header>

