<?php
session_start();
require("../modele/dateFormatter.php");
require("../modele/connection.php");
require("../modele/mongodbManager.php");
require("../modele/userManager.php");
require("../modele/postManager.php");
require("../modele/commentManager.php");

$action = $_GET["action"];

$userObject = new User();
$data = $_SESSION["get_data"];

switch ($action)
{
    case "login":
        {
            if(empty($_SESSION["get_data"])){
                header("Location: ../index.php");
                exit();
            }
            $email = $data["email"];
            $password = $data["password"];
            $user = new User();
            $userData = $user->exists($email, $password);
            if($userData){
                $_SESSION['pUserData'] = $userData;
            } else{
                $_SESSION['error'] = "Votre adresse email ou votre mot de passe est incorrect.";
            }
            unset($_SESSION["get_data"]);

            header("Location: ../index.php" . ($userData ? "" : "?action=login"));
            break;
        }
    case "create":
        {
            if(empty($_SESSION["get_data"])){
                header("Location: ../index.php");
                exit();
            }
            $pseudo = $data["pseudo"];
            $email = $data["email"];
            $password = $data["password"];
            $data = [
                "pseudo" => $pseudo,
                "email" => $email,
                "password" => hash("sha256", $password), // Hashage du mot de passe
            ];

            $user = new User();
            $duplicate = $user->checkDuplicate($data['pseudo'], $data['email'], null);
            if ($duplicate === "pseudo") {
                $_SESSION['error'] = "Ce pseudo est déjà pris. Veuillez en choisir un autre.";
            } else if ($duplicate === "email") {
                $_SESSION['error'] = "Cette adresse email est déjà associée à un compte. Veuillez en utiliser une autre ou vous connecter.";
            }
            else{
                $insertUser = $user->create($data);
                //Connexion automatique (Seul utilisateur peut crée un user)
                if ($insertUser) {
                    $_SESSION['pUserData'] = $insertUser;
                } else{
                    $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
                }
            }
            unset($_SESSION["get_data"]);

            header("Location: ../index.php" . ($insertUser ? "" : "?action=signup"));
            break;
        }
    case "update":
        {
            if(empty($_SESSION["get_data"])){
                header("Location: ../index.php");
                exit();
            }
            $pseudo = $data["pseudo"];
            $email = $data["email"];
            $password = $data["password"];
            $userId = $_SESSION['pUserData']['_id'];
            $data = [
                "pseudo" => $pseudo,
                "email" => $email,
            ];
            
            // Ajouter le mot de passe uniquement s'il n'est pas vide
            if (!empty($password)) {
                $data["password"] = hash("sha256", $password); // Hashage du mot de passe
            }
            
            $filter = ["_id" => new MongoDB\BSON\ObjectId($userId)];
            
            $user = new User();
            $duplicate = $user->checkDuplicate($data['pseudo'], $data['email'], $userId);
            if ($duplicate === "pseudo") {
                $_SESSION['error'] = "Ce pseudo est déjà pris. Veuillez en choisir un autre.";
            } else if ($duplicate === "email") {
                $_SESSION['error'] = "Cette adresse email est déjà associée à un compte. Veuillez en utiliser une autre ou vous connecter.";
            }
            else{
                $user->update($filter, $data);
                // Mettre a jour dans la session
                $_SESSION['pUserData'] = [
                    "_id" => $_SESSION['pUserData']['_id'],
                    "pseudo" => $pseudo,
                    "email" => $email,
                ];
            }
            unset($_SESSION["get_data"]);

            header("Location: ../index.php" . ($duplicate ? "?action=profile" : ""));
            break;
        }
    case "profile":
        {
            $user = new User();
            $userId = $_SESSION['userId'];
            $userData = $user->getUserById($userId);

            $postManager = new PostManager();
            $commentManager = new CommentManager();

            $postCount = $postManager->getCollectionsCountByUserId($userId);
            $commentCount = $commentManager->getCollectionsCountByUserId($userId);
            $userCommentCount = $postManager->getUniqueUserCountByAuthor($userId);

            include '../vue/profile.php';
            break;
        }
    default :
        {
            header("Location:../index.php?action=error");
        }
}
?>