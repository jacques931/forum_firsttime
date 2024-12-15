<!-- Code include dans userController -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | FirstTime</title>
    <link rel="icon" type="image/png" href="../asset/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include("../vue/header.php") ?>
    <?php include("../vue/errorMessage.php") ?>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="max-width: 600px; width: 100%;">
            <div class="text-center">
                <img src="../asset/default_avatar.png" width="100px" height="100px" alt="Profile Picture">
                <h2 class="mb-0"><?php echo $userData["pseudo"]; ?></h2>
                <p class="text-muted"><?php echo $userData["email"]; ?></p>
                <p class="text-muted">A rejoint le forum le <?php echo $userData['created_at'] ?></p>
            </div>
            <hr>
            <!-- Informations du profil -->
            <div class="row text-center">
                <div class="col-4">
                    <h5 class="mb-0"><?php echo $postCount ?></h5>
                    <small class="text-muted">Posts</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0"><?php echo $commentCount ?></h5>
                    <small class="text-muted">Messages</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0"><?php echo $userCommentCount ?></h5>
                    <small class="text-muted">Participants</small>
                </div>
            </div>
            <hr>
            <?php
            if (isset($_SESSION['pUserData']['_id']) && $_SESSION['pUserData']['_id'] === $userId) {
                $currentUserId = $_SESSION['pUserData']['_id'];
            ?>
            <!-- Formulaire pour mise à jour -->
            <h4 class="text-center mb-3">Modifier le profil</h4>
            <form action="../index.php?action=updateUser" method="POST">
                <!-- Pseudo -->
                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudo</label>
                    <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Choisissez un pseudo" value="<?php echo $userData["pseudo"]; ?>" required>
                </div>
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" value="<?php echo $userData["email"]; ?>" required>
                </div>
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe">
                </div>
                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
            <?php }?>
        </div>
    </div>

    <?php include("../vue/footer.html") ?>
</body>
</html>
