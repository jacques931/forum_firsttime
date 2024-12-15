<?php
session_start();
require("../modele/dateFormatter.php");
require("../modele/connection.php");
require("../modele/mongodbManager.php");
require("../modele/post.php");
require("../modele/postManager.php");
require("../modele/comment.php");
require("../modele/commentManager.php");
require("../modele/userManager.php");

$postManager = new PostManager();
$commentManager = new CommentManager();
$userManager = new User();

// Crée la pagination pour les posts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$postsPerPage = 10;
$offset = ($page - 1) * $postsPerPage;

$latestPosts = $postManager->getPosts($offset, $postsPerPage);
$totalPosts = $postManager->getCollectionsCount();
$totalPages = ceil($totalPosts / $postsPerPage);

// Message
$latestComments = $commentManager->getLastComments();

// Statistique
$stats = [
    'totalPosts' => $totalPosts,
    'totalComments' => $commentManager->getCollectionsCount(),
    'totalUsers' => $userManager->getCollectionsCount()
];

include "../vue/home.php";

?>