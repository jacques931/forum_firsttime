<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['pUserData']);
if (!$isLoggedIn) {
    header("Location:../index.php?action=login");
    $_SESSION['error'] = "Vous devez être connecté pour effectuer cette action.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Post | FirstTime</title>
    <link rel="icon" type="image/png" href="../asset/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include("../vue/header.php") ?>
    <?php include("../vue/errorMessage.php") ?>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4 w-75">
            <h2 class="text-center mb-4">Modifier un Post</h2>
            <form action="../index.php?action=updatePost&postId=<?php echo $post->getId(); ?>" method="POST">
                <!-- Titre -->
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre" value="<?php echo htmlspecialchars($post->getTitre()); ?>" required>
                </div>

                <!-- Contenu -->
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" id="contenu" name="contenu" rows="15" placeholder="Écrivez le contenu du post" required><?php echo htmlspecialchars($post->getContenu()); ?></textarea>
                </div>

                <!-- Bouton de soumission -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../vue/footer.html") ?>
</body>
</html>
