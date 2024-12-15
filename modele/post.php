<?php

class Post
{
    private static $postManager;
    private static $commentManager;

    private $_id;
    private $_titre;
    private $_contenu;
    private $_auteur;
    private $_created_at;
    private $_updated_at;

    // auteur est soit un id soit le pseudo : si c'est une creation c'est un id, une visualisation l'objet user
    public function __construct(
        $id,
        $titre,
        $contenu,
        $auteur,
        $created_at,
        $updated_at = null
    ) {
        $this->_id = $id;
        $this->_titre = $titre;
        $this->_contenu = $contenu;
        $this->_auteur = $auteur;
        $this->_created_at = $created_at;
        $this->_updated_at = $updated_at;
         // Initialisation unique des managers
        if (!isset(self::$commentManager)) {
            self::$commentManager = new CommentManager();
        }
        if (!isset(self::$postManager)) {
            self::$postManager = new PostManager();
        }
    }

    public function getData() {
        return [
            '_id' => $this->_id ? (string)$this->_id : null,
            'titre' => $this->_titre,
            'contenu' => $this->_contenu,
            'auteur' => new \MongoDB\BSON\ObjectId($this->_auteur),
            'created_at' => $this->_created_at,
            'updated_at' => $this->_updated_at
        ];
    }

    public function getId(){
        return $this->_id;
    }

    public function getTitre(){
        return $this->_titre;
    }

    public function getContenu(){
        return $this->_contenu;
    }

    public function renderPreview() {
        $limitCaractContenu = 350;
        $limitCaractTitre = 170;
    
        // Limiter le contenu
        $estTronqueContenu = mb_strlen($this->_contenu) > $limitCaractContenu;
        $contenuExtrait = $estTronqueContenu ? mb_substr($this->_contenu, 0, $limitCaractContenu) . '...' : $this->_contenu;
        $contenuAffiche = nl2br(htmlspecialchars($contenuExtrait));
    
        // Limiter le titre
        $estTronqueTitre = mb_strlen($this->_titre) > $limitCaractTitre;
        $titreExtrait = $estTronqueTitre ? mb_substr($this->_titre, 0, $limitCaractTitre) . '...' : $this->_titre;
    
        return '<div class="list-group-item" onclick="location.href=\'../index.php?action=viewPost&postId=' . $this->_id . '\';" style="cursor: pointer;">' .
           '<h5 class="mb-1 m-0">' . htmlspecialchars($titreExtrait) . '</h5>' .
           '<p>' . $contenuAffiche . '</p>' .
           '<span class="text-muted d-inline">Par <a href="../index.php?action=profile&userId=' . htmlspecialchars($this->_auteur[0]->_id) . 
           '" class="text-decoration-none">' . htmlspecialchars($this->_auteur[0]->pseudo) . '</a> ' . 
           htmlspecialchars($this->_created_at) . 
           '</span><br>' .
           '<span class="text-muted d-inline">' . 
           self::$commentManager->getCommentCountByPostId($this->_id) .' commentaires | '.self::$commentManager->getUniqueUserCountByPost($this->_id).' participants</span>' .
           '</div>';
    }       

    public function showPostDetails() {
        $titreComplet = htmlspecialchars($this->_titre);
        $contenuComplet = nl2br(htmlspecialchars($this->_contenu));
        $auteurId = isset($this->_auteur[0]->_id) ? htmlspecialchars($this->_auteur[0]->_id) : 'Inconnu';
        $auteurPseudo = isset($this->_auteur[0]->pseudo) ? htmlspecialchars($this->_auteur[0]->pseudo) : 'Anonyme';
        $canEdit = isset($_SESSION['pUserData']['_id']) && $_SESSION['pUserData']['_id'] === $auteurId; // Vérifie si l'utilisateur est l'auteur du post
    
        $dateCreation = htmlspecialchars($this->_created_at);
        $dateModification = $this->_updated_at && $this->_updated_at !== $this->_created_at 
            ? '<p class="text-muted mb-0"><em>Modifié le ' . htmlspecialchars($this->_updated_at) . '</em></p>' 
            : '';
    
        // Menu dropdown pour les actions de l'auteur
        $menuDropdown = '';
        if ($canEdit) {
            $menuDropdown = '
                <div class="dropdown">
                    <button class="btn btn-link p-0 text-muted float-end" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-primary" href="../index.php?action=editPost&postId=' . htmlspecialchars($this->_id) . '">
                                <i class="bi bi-pencil-square me-2"></i> Modifier
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="confirmDeletePost(event, \'' . htmlspecialchars($this->_id) . '\')">
                                <i class="bi bi-trash3-fill me-2"></i> Supprimer
                            </a>
                        </li>
                    </ul>
                </div>';
        }
    
        return '<div class="card mt-4">' .
               '<div class="card-header d-flex justify-content-between align-items-center">' .
               '<div>' .
               '<h3>' . $titreComplet . '</h3>' .
               '<p class="text-muted">Par <a href="../index.php?action=profile&userId=' . $auteurId . 
               '" class="text-decoration-none">' . $auteurPseudo . '</a> le ' . $dateCreation . '</p>' .
               $dateModification .
               '</div>' .
               $menuDropdown .
               '</div>' .
               '<div class="card-body">' .
               '<p>' . $contenuComplet . '</p>' .
               '</div>' .
               '</div>';
    }    
    
    
}

?>