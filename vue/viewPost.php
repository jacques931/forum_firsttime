<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du Post | FirstTime</title>
    <link rel="icon" type="image/png" href="../asset/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .comment-level-0 {
            border-left: 4px solid rgb(108, 179, 255);
            padding-left: 10px;
        }

        .comment-level-1 {
            border-left: 4px solid rgb(255, 116, 116);
            padding-left: 10px;
        }

        .comment-level-2 {
            border-left: 4px solid rgb(118, 255, 150);
            padding-left: 10px;
        }

        /* Bordures par défaut pour les niveaux supérieurs */
        [class^="comment-level-"] {
            margin-bottom: 1rem;
            padding: 10px;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <?php include("../vue/header.php") ?>
    <?php include("../vue/errorMessage.php") ?>

    <div class="container mt-5 mb-5">
        <!-- Détail du post -->
        <?php echo $post->showPostDetails(); ?>

        <!-- Bouton pour afficher/masquer le formulaire d'ajout de commentaire -->
        <div class="mt-4">
            <button id="toggleCommentForm" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Ajouter un commentaire
            </button>
        </div>

        <!-- Formulaire d'ajout de commentaire -->
        <div id="commentFormContainer" class="border mt-4 p-2" style="display: none;">
            <h5>Ajouter un commentaire</h5>
            <form action="../index.php?action=addCommentaire" method="POST">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="4" placeholder="Écrivez votre commentaire ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
        
        <!-- Section des commentaires -->
        <div class="card mt-4 p-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Commentaires (<?php echo $commentManager->getCommentCountByPostId($post->getId()); ?>)</h4>
                <div class="d-flex align-items-center">
                    <label for="commentDisplayStyle" class="form-label me-2 mb-0 text-secondary">Afficher :</label>
                    <select id="commentDisplayStyle" class="form-select form-select-sm w-auto">
                        <option value="inline_oldest" <?php echo $commentStyle === 'inline_oldest' ? 'selected' : ''; ?>>
                            Réponses en ligne, la plus ancienne en premier
                        </option>
                        <option value="inline_newest" <?php echo $commentStyle === 'inline_newest' ? 'selected' : ''; ?>>
                            Réponses en ligne, la plus récente en premier
                        </option>
                        <option value="threads" <?php echo $commentStyle === 'threads' ? 'selected' : ''; ?>>
                            Réponses en fils de discussions
                        </option>
                        <option value="nested" <?php echo $commentStyle === 'nested' ? 'selected' : ''; ?>>
                            Réponses imbriquées
                        </option>
                    </select>
                </div>
            </div>
            <?php if (!empty($comments)): ?>
                <?php
                function renderCommentTree($comments, $commentStyle, $level = 0)
                {
                    $html = '';
                    foreach ($comments as $comment) {
                        $levelClass = 'comment-level-' . $level;
                
                        $html .= '<div class="d-flex mb-3 ' . $levelClass . '" style="margin-left: ' . ($level * 50) . 'px;">';
                        $html .= $comment->renderHTML($level);
                        $html .= '</div>';
                
                        // Afficher les sous-commentaires récursivement
                        if (!empty($comment->getChildren())) {
                            $nextLevel = ($commentStyle === 'threads') ? $level : $level + 1;
                            $html .= renderCommentTree($comment->getChildren(), $commentStyle, $nextLevel);
                        }
                    }
                    return $html;
                }
                
                echo renderCommentTree($comments, $commentStyle);
                ?>
            <?php else: ?>
                <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
            <?php endif; ?>
        </div>

        <!-- Formulaire de réponse -->
        <div id="replyFormContainer" class="mt-3" style="display: none;">
            <form action="../index.php?action=addCommentaire" method="POST">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3" placeholder="Écrivez votre réponse ici..." required></textarea>
                </div>
                <input type="hidden" id="replyCommentId" name="commentId" value="">
                <button type="submit" class="btn btn-secondary btn-sm">Répondre</button>
            </form>
        </div>

        <div id="editFormContainer" class="mt-3" style="display: none;">
            <form action="../index.php?action=updateCommentaire" method="POST">
                <input type="hidden" id="editCommentId" name="commentId" value="">
                <div class="mb-3">
                    <textarea class="form-control" id="editContent" name="content" rows="3" placeholder="Écrivez votre réponse ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Mettre à jour</button>
            </form>
        </div>
    </div>

    <?php include("../vue/footer.html") ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDeleteComment(event, commentId) {
            event.preventDefault();

            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette action supprimera définitivement ce commentaire.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Supprimer",
                cancelButtonText: "Annuler",
                buttonsStyling: false, // Désactive le style par défaut de SweetAlert
                customClass: {
                    confirmButton: "btn btn-danger  me-3", // Bouton rouge "Supprimer"
                    cancelButton: "btn btn-secondary" // Bouton gris "Annuler"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Effectuer la suppression ou redirection
                    window.location.href = "../index.php?action=deleteCommentaire&commentId=" + commentId;
                }
            });
        }

        function confirmDeletePost(event, postId) {
            event.preventDefault();
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette action supprimera définitivement le post.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Supprimer",
                cancelButtonText: "Annuler",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-danger me-2",
                    cancelButton: "btn btn-secondary"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../index.php?action=deletePost&postId=" + postId;
                }
            });
        }

        async function checkSession() {
            try {
                const response = await fetch("checkSession.php");
                const data = await response.json();

                if (data.status === "success") {
                    return true;
                } else {
                    location.href="../index.php?action=login";
                }
            } catch (error) {
                console.error("Erreur lors de la vérification de la session :", error);
                return false;
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Formulair crée un commentaire
            const toggleCommentFormButton = document.getElementById("toggleCommentForm");
            const commentFormContainer = document.getElementById("commentFormContainer");

            toggleCommentFormButton.addEventListener("click", async () => {
                const isUserConnected = await checkSession();

                if (isUserConnected) {
                    if (commentFormContainer.style.display === "none" || commentFormContainer.style.display === "") {
                        commentFormContainer.style.display = "block";
                        toggleCommentFormButton.innerHTML = '<i class="bi bi-dash-circle"></i> Masquer le formulaire';
                    } else {
                        commentFormContainer.style.display = "none";
                        toggleCommentFormButton.innerHTML = '<i class="bi bi-plus-circle"></i> Ajouter un commentaire';
                    }
                }
            });

            // Formulaire répondre au commentaire
            const replyButtons = document.querySelectorAll(".reply-btn");
            const replyFormContainer = document.getElementById("replyFormContainer");
            const replyCommentIdInput = document.getElementById("replyCommentId");

            // Formulaire modifier commentaire
            const editButtons = document.querySelectorAll(".edit-btn");
            const editFormContainer = document.getElementById("editFormContainer");
            const editCommentIdInput = document.getElementById("editCommentId");
            const editContentInput = document.getElementById("editContent");

            // Gérer l'ouverture d'un formulaire à la fois
            const closeAllForms = () => {
                replyFormContainer.style.display = "none";
                editFormContainer.style.display = "none";
            };

            replyButtons.forEach(button => {
                button.addEventListener("click", async (event) => {
                    event.preventDefault();
                    const isUserConnected = await checkSession();

                    if (isUserConnected) {
                        closeAllForms(); // Fermer tous les autres formulaires

                        const commentId = button.getAttribute("data-comment-id");
                        replyCommentIdInput.value = commentId;

                        button.closest(".d-flex").insertAdjacentElement("afterend", replyFormContainer);
                        replyFormContainer.style.display = "block";
                    }
                });
            });

            editButtons.forEach(button => {
                button.addEventListener("click", async (event) => {
                    event.preventDefault();
                    const isUserConnected = await checkSession();

                    if (isUserConnected) {
                        closeAllForms(); // Fermer tous les autres formulaires

                        const commentId = button.getAttribute("data-comment-id");
                        const commentContent = button.getAttribute("data-comment-content");
                        editCommentIdInput.value = commentId;
                        editContentInput.value = commentContent;

                        button.closest(".d-flex").insertAdjacentElement("afterend", editFormContainer);
                        editFormContainer.style.display = "block";
                    }
                });
            });

            // Style affichage des commentaires
            const commentDisplayStyle = document.getElementById("commentDisplayStyle");
            commentDisplayStyle.addEventListener("change", async (event) => {
                const selectedStyle = event.target.value; // Récupérer l'option sélectionnée
                const postId = "<?php echo $post->getId(); ?>"; // ID du post

                try {
                    location.href=`../index.php?action=viewPost&postId=${postId}&commentStyle=${selectedStyle}`;
                } catch (error) {
                    console.error("Erreur lors du chargement des commentaires :", error);
                }
            });
        });

    </script>
</body>
</html>
