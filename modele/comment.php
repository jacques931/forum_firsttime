<?php
class Comment
{
    private $_id;
    private $_postId;
    private $_contenu;
    private $_author;
    private $_createdAt;
    private $_parentCommentId;

    private $_children;

    // auteur est soit un id soit le pseudo : si c'est une creation c'est un id, une visualisation l'objet user
    public function __construct(
        $id,
        $postId,
        $contenu,
        $author,
        $createdAt,
        $parentCommentId = null,
        $children = null
    ) {
        $this->_id = $id;
        $this->_postId = $postId;
        $this->_contenu = $contenu;
        $this->_author = $author;
        $this->_createdAt = $createdAt;
        $this->_parentCommentId = $parentCommentId;
        $this->_children = $children;
    }

    public function getData() {
        return [
            '_id' => $this->_id ? (string)$this->_id : null,
            'postId' => new \MongoDB\BSON\ObjectId($this->_postId),
            'contenu' => $this->_contenu,
            'auteur' => new \MongoDB\BSON\ObjectId($this->_author),
            'created_at' => $this->_createdAt,
            'parentCommentId' => $this->_parentCommentId ? new \MongoDB\BSON\ObjectId($this->_parentCommentId) : null
        ];
    }

    public function getParentCommentId(){
        return $this->_parentCommentId;
    }

    public function getId(){
        return $this->_id;
    }

    public function getChildren(){
        return $this->_children;
    }

    public function setChildren($children){
        $this->_children = $children;
    }

    public function renderPreview()
    {
        $limitCaractContenu = 150;
        // Limiter le contenu
        $estTronqueContenu = mb_strlen($this->_contenu) > $limitCaractContenu;
        $contenuExtrait = $estTronqueContenu ? mb_substr($this->_contenu, 0, $limitCaractContenu) . '...' : $this->_contenu;
        $contenuAffiche = nl2br(htmlspecialchars($contenuExtrait));

        return '
            <div class="list-group-item" style="cursor: pointer;" onclick="window.location.href=\'../index.php?action=viewPost&postId=' . htmlspecialchars($this->_postId) . '\'">
                <p class="mb-1">' . $contenuAffiche . '</p>
                <small class="text-muted">
                    Par <a href="../index.php?action=profile&userId=' . htmlspecialchars($this->_author->_id) . 
                    '" class="text-decoration-none">' . htmlspecialchars($this->_author->pseudo) . '</a> 
                    ' . htmlspecialchars($this->_createdAt) . '
                </small>
            </div>
        ';
    }

    public function renderHTML($level)
    {
        $replyButton = '';
        $deleteButton = '';
        $editButton = '';
        // Vérifier si l'utilisateur connecté est l'auteur du commentaire
        if (isset($_SESSION['pUserData']) && $_SESSION['pUserData']['_id'] == $this->_author->_id) {
            $deleteButton = '
                <a href="#" class="me-3 text-decoration-none text-danger delete-btn" data-comment-id="' . $this->_id . '" onclick="confirmDeleteComment(event, \'' . $this->_id . '\')">
                    <i class="bi bi-trash"></i> Supprimer
                </a>';

            $editButton = '
                <a href="#" class="me-3 text-decoration-none text-primary edit-btn" 
                    data-comment-id="'.$this->_id.'" 
                    data-comment-content="'.$this->_contenu.'">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                ';
        }
        // Afficher le bouton "Répondre" uniquement pour les niveaux inférieurs ou égaux à 2
        if ($level < 2) { 
            $replyButton = '
                <a href="#" class="me-3 text-decoration-none reply-btn" data-comment-id="' . $this->_id . '">
                    <i class="bi bi-reply"></i> Répondre
                </a>';
        }

        $html = '
            <div class="flex-shrink-0">
                <img src="../asset/default_avatar.png" width="50px" height="50px" alt="Avatar">
            </div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="../index.php?action=profile&userId=' . $this->_author->_id . '" class="text-decoration-none">
                            <strong>' . htmlspecialchars($this->_author->pseudo) . '</strong>
                        </a>
                        <span class="text-muted">• ' . htmlspecialchars($this->_createdAt) . '</span>
                    </div>
                </div>
                <p class="mb-1">' . nl2br(htmlspecialchars($this->_contenu)) . '</p>
                <div class="d-flex text-muted small">
                    ' . $replyButton . $editButton . $deleteButton . '
                </div>
            </div>';

        return $html;
    }
}

?>