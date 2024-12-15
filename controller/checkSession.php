<?php
session_start();

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['pUserData'])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    $_SESSION['error'] = "Vous devez être connecté pour effectuer cette action.";
}
?>