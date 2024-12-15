<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte | FirstTime</title>
    <link rel="icon" type="image/png" href="../asset/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include("./header.php") ?>
    <?php include("./errorMessage.php") ?>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
            <h2 class="text-center mb-4">Créer un compte</h2>
            <form action="../index.php?action=createUser" method="POST">
                <!-- Pseudo -->
                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudo</label>
                    <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Choisissez un pseudo" required>
                </div>
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
                </div>
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Créez un mot de passe" required>
                </div>
                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Créer un compte</button>
                </div>
            </form>
            <!-- Login Link -->
            <div class="text-center mt-3">
                <p>Déjà un compte ? <a href="../index.php?action=login">Se connecter</a></p>
            </div>
        </div>
    </div>

    <?php include("./footer.html") ?>
</body>
</html>
