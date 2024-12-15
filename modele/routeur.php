<?php
class Routeur
{
    private $action;
    private $route;
    public function __construct()
	{	
		$this->action = '?'; 
		$this->route = '?'; 
	}

    private function set_chemin_controleur()
    {
        // Définir les routes
        $routes = [
            // Vue
            'addPost' => './vue/addPost.php',
            'login' => './vue/login.php',
            'signup' => './vue/signup.php',

            // Home et logout
            'home' => './controller/homeController.php',
            'logout' => './controller/logout.php',

            // User actions
            'createUser' => './controller/userController.php?action=create',
            'loginUser' => './controller/userController.php?action=login',
            'updateUser' => './controller/userController.php?action=update',
            'profile' => './controller/userController.php?action=profile',

            // Post actions
            'createPost' => './controller/postController.php?action=create',
            'viewPost' => './controller/postController.php?action=view',
            'deletePost' => './controller/postController.php?action=delete',
            'editPost' => './controller/postController.php?action=edit',
            'updatePost' => './controller/postController.php?action=update',

            // Commentaire actions
            'addCommentaire' => './controller/commentController.php?action=create',
            'deleteCommentaire' => './controller/commentController.php?action=delete',
            'updateCommentaire' => './controller/commentController.php?action=update',
        ];

        // Affecter la route correspondante ou une erreur
        $this->route = $routes[$this->action] ?? 'vue/error.php';
    }

    public function redirect($action)
    {
        $this->action = $action;
        $this->set_chemin_controleur();
        $url = $this->route;
        header("Location:$url");
    }
}
?>