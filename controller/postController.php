<?php
session_start();
require("../modele/dateFormatter.php");
require("../modele/connection.php");
require("../modele/mongodbManager.php");
require("../modele/post.php");
require("../modele/postManager.php");
require("../modele/comment.php");
require("../modele/commentManager.php");
$action = $_GET["action"];

$postManager = new PostManager();
$commentManager = new CommentManager();
$data = $_SESSION["get_data"];

switch ($action)
{
    case "create":
        {
            if(empty($_SESSION["get_data"])){
                header("Location: ../index.php");
                exit();
            }
            $titre = $data["titre"];
            $contenu = $data["contenu"];
            $auteur = $_SESSION['pUserData']['_id'];
            $dateCreation = new MongoDB\BSON\UTCDateTime();
            $dateModification = new MongoDB\BSON\UTCDateTime();

            $post = new Post(null, $titre, $contenu, $auteur, $dateCreation, $dateModification);
            $postManager->create($post);
            unset($_SESSION["get_data"]);

            header("Location: ../index.php");
            break;
        }
    case "view":
        {
            $postId = $_SESSION['postId'];
            $post = $postManager->find($postId);
            if (!$post) {
                die("Post introuvable");
            }
            // Récupere les commentaires selon le style récuperer
            $commentStyle = $_SESSION['commentStyle'] ?? 'nested';
            switch ($commentStyle) {
                case 'inline_oldest':
                    $comments = $commentManager->getCommentsByPostId($postId, 'oldest');
                    break;
                case 'inline_newest':
                    $comments = $commentManager->getCommentsByPostId($postId, 'newest');
                    break;
                case 'threads': // Il sera mis sans profondeur dans la vue
                case 'nested':
                    $comments = $commentManager->getCommentsAsNested($postId);
                    break;
                default:
                    $comments = $commentManager->getCommentsByPostId($postId, 'oldest');
                    break;
            }
            require '../vue/viewPost.php';
            break;
        }
    case "edit":
        {
            $postId = $_SESSION['postId'];
            $post = $postManager->find($postId);
            include '../vue/editPost.php';
            break;
        }
    case "update":
        {
            $postId = $_SESSION['postId'];
            $titre = $data["titre"];
            $contenu = $data["contenu"];
            $dateModification = new MongoDB\BSON\UTCDateTime();
        
            if (empty($titre) || empty($contenu)) {
                $_SESSION['error'] = "Le titre et le contenu ne peuvent pas être vides.";
                header("Location: ../index.php?action=editPost&postId=" . $postId);
                exit();
            }
        
            $updateData = [
                'titre' => $titre,
                'contenu' => $contenu,
                'updated_at' => $dateModification,
            ];
        
            $filter = ['_id' => new MongoDB\BSON\ObjectId($postId)];
            $postManager->update($filter, $updateData);

            header("Location: ../index.php?action=viewPost&postId=" . $postId);
            break;
        }
    case 'delete':
        {
            $postId = $_SESSION['postId'];
            $postManager->deletePostAndComments($postId);
            header("Location:../index.php");
            break;
        }
    default :
        {
            header("Location:../index.php?action=error");
        }
}
?>