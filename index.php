<?php

session_start();
require("./modele/routeur.php");

$action = 'home';
if(isset($_GET["action"]))
{
    $action = $_GET["action"];
    if (isset($_GET["userId"])) {
        $userId = $_GET["userId"];
        $_SESSION["userId"] = $userId;
    } elseif (isset($_GET["postId"])) {
        $postId = $_GET["postId"];
        $_SESSION["postId"] = $postId;
    } elseif (isset($_GET["commentId"])) {
        $commentId = $_GET["commentId"];
        $_SESSION["commentId"] = $commentId;
    } 
    if (isset($_GET["commentStyle"])) {
        $commentStyle = $_GET["commentStyle"];
        $_SESSION["commentStyle"] = $commentStyle;
    }
} else{
    unset($_SESSION["commentStyle"]);
}

// On met les données POST dans une session
if(isset($_POST))
{
    $formData = array();
    $formData = $_POST;
    $_SESSION["get_data"] = $formData;
}

$routeur = new Routeur();
$routeur->redirect($action); //redirection vers une autre page

?>