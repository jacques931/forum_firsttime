<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | FirstTime</title>
    <link rel="icon" type="image/png" href="../asset/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include("../vue/header.php") ?>
    <?php include("../vue/errorMessage.php") ?>
    <div class="container mt-4">
        <div class="row">
            <!-- Section pour les posts -->
            <div class="col-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Derniers Posts</h2>
                <a href="../index.php?action=addPost" class="btn btn-primary me-4">
                    <i class="bi bi-plus"></i> Ajouter un Post
                </a>
            </div>
                
                <div class="list-group">
                    <?php 
                    if (!empty($latestPosts)){
                        foreach ($latestPosts as $post){
                            echo $post->renderPreview();
                        }
                    } else{
                        echo '<div class="alert alert-warning" role="alert">
                                Aucun post disponible pour le moment.
                            </div>';
                    }
                    ?>
                </div>
                <div class="mt-4">
                    <nav>
                        <ul class="pagination">
                            <!-- Lien vers la page précédente -->
                            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Précédent</a>
                            </li>

                            <!-- Lien vers chaque page -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Lien vers la page suivante -->
                            <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                
            </div>
            
            <!-- Section pour les messages et statistiques -->
            <div class="col-4">
                <h2 class="mb-4">Derniers Commentaires</h2>
                <div class="list-group">
                    <?php
                        if (!empty($latestComments)){
                            foreach ($latestComments as $comment){
                                echo $comment->renderPreview();
                            }
                        } else{
                            echo '<div class="alert alert-warning" role="alert">
                                    Aucun commentaire disponible pour le moment.
                                </div>';
                        }
                    ?>
                </div>
                
                <!-- Section pour les statistiques -->
                <h2 class="mt-4 mb-4">Statistiques</h2>
                <ul class="list-group">
                    <li class="list-group-item">Nombre de posts : <?php echo $stats['totalPosts']; ?></li>
                    <li class="list-group-item">Nombre de commentaire : <?php echo $stats['totalComments']; ?></li>
                    <li class="list-group-item">Nombre d'utilisateurs : <?php echo $stats['totalUsers']; ?></li>
                </ul>
            </div>
        </div>
    </div>

    <?php include("../vue/footer.html") ?>
</body>
</html>