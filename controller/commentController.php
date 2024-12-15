<?php
session_start();
require("../modele/dateFormatter.php");
require("../modele/connection.php");
require("../modele/mongodbManager.php");
require("../modele/comment.php");
require("../modele/commentManager.php");
$action = $_GET["action"];

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
            $postId = $_SESSION['postId'];
            $authorId = $_SESSION['pUserData']['_id'];
            $content = $data['content'];
            $parentCommentId = $data['commentId'] ?? null;
            $dateCreation = new MongoDB\BSON\UTCDateTime();

            if (empty($postId) || empty($content)) {
                die("Données manquantes ou invalides.");
            }

            $comment = new Comment(null, $postId, $content, $authorId, $dateCreation, $parentCommentId);
            $commentManager->create($comment);
            unset($_SESSION["get_data"]);

            header("Location: ../index.php?action=viewPost");
            break;
        }
    case "update":
        {
            if(empty($_SESSION["get_data"])){
                header("Location: ../index.php");
                exit();
            }
            $commentId = $data['commentId'];
            $content = $data['content'];

            if (empty($commentId) || empty($content)) {
                die("Données manquantes ou invalides.");
            }

            $updateData = [
                'contenu' => $content,
            ];

            $objectId = new MongoDB\BSON\ObjectId($commentId);
            $result = $commentManager->update(['_id' => $objectId], $updateData);
            unset($_SESSION["get_data"]);
            header("Location: ../index.php?action=viewPost");
            break;
        }
    case "delete":
        {
            $commentId = $_SESSION['commentId'];
            $commentManager->deleteCommentAndDescendants($commentId);
            header("Location: ../index.php?action=viewPost");
            break;
        }
    default :
        {
            header("Location:../index.php?action=error");
        }
}
?>