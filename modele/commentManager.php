<?php

class CommentManager extends MongodbManager
{
    public function __construct()
    {
        parent::__construct("comments");
    }

    public function getLastComments($limit = 10)
    {
        $pipeline = $this->buildPipeline([], ['includeUsers' => true, 'sort' => 'newest', 'limit' => $limit]);
        return $this->executeAndTransform($pipeline);
    }

    public function getCommentCountByPostId($postId)
    {
        return $this->countDocuments(['postId' => new MongoDB\BSON\ObjectId($postId)]);
    }

    public function getUniqueUserCountByPost($postId)
    {
        return $this->countDocuments(['postId' => new MongoDB\BSON\ObjectId($postId)], 'auteur');
    }

    public function getCommentsByPostId($postId, $sort)
    {
        try {
            $pipeline = $this->buildPipeline(
                ['postId' => $postId],
                [
                    'includeUsers' => true,
                    'sort' => $sort == 'newest' ? 'newest' : 'oldest'
                ]
            );

            $comments = $this->executeAndTransform($pipeline);

            return $comments;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération des commentaires : " . $e->getMessage();
            return [];
        }
    }

    public function getCommentsAsNested($postId)
    {
        $pipeline = $this->buildPipeline(['postId' => $postId], ['includeUsers' => true, 'sort' => 'oldest']);
        $documents = $this->executeMongoCommand($pipeline);

        // Organiser les commentaires par parent ID
        $commentsByParent = [];
        foreach ($documents as $document) {
            $comment = new Comment(
                (string)$document->_id,
                (string)$document->postId,
                $document->contenu,
                $document->author_info[0],
                DateFormatter::getDateDifference($document->created_at),
                isset($document->parentCommentId) ? (string)$document->parentCommentId : null
            );

            $parentId = $comment->getParentCommentId();
            $commentsByParent[$parentId][] = $comment;
        }

        // Construire l'arborescence des commentaires
        $buildTree = function ($parentId = null) use (&$commentsByParent, &$buildTree) {
            $tree = [];
            if (isset($commentsByParent[$parentId])) {
                foreach ($commentsByParent[$parentId] as $comment) {
                    $comment->setChildren($buildTree($comment->getId())); // Ajouter les enfants
                    $tree[] = $comment;
                }
            }
            return $tree;
        };

        return $buildTree();
    }
    
    private function buildPipeline($filters = [], $options = [])
    {
        $pipeline = [];

        // Ajouter des filtres
        if (!empty($filters['postId'])) {
            $pipeline[] = ['$match' => ['postId' => new MongoDB\BSON\ObjectId($filters['postId'])]];
        }

        // Joindre la collection `users`
        if (!empty($options['includeUsers'])) {
            $pipeline[] = ['$lookup' => [
                'from' => 'users',
                'localField' => 'auteur',
                'foreignField' => '_id',
                'as' => 'author_info'
            ]];
        }

        if (!empty($options['sort'])) {
            $pipeline[] = ['$sort' => ['created_at' => $options['sort'] === 'newest' ? -1 : 1]];
        }

        if (!empty($options['limit'])) {
            $pipeline[] = ['$limit' => $options['limit']];
        }

        return $pipeline;
    }


    private function executeAndTransform($pipeline)
    {
        $documents = $this->executeMongoCommand($pipeline);

        $comments = [];
        foreach ($documents as $document) {
            $comments[] = new Comment(
                (string)$document->_id,
                (string)$document->postId,
                $document->contenu,
                $document->author_info[0],
                DateFormatter::getDateDifference($document->created_at),
                isset($document->parentCommentId) ? (string)$document->parentCommentId : null
            );
        }

        return $comments;
    }


    private function countDocuments($filters = [], $groupField = null)
    {
        $pipeline = [];
        if (!empty($filters)) {
            $pipeline[] = ['$match' => $filters];
        }

        if (!empty($groupField)) {
            $pipeline[] = ['$group' => [
                '_id' => null,
                'uniqueCount' => ['$addToSet' => '$' . $groupField]
            ]];
            $pipeline[] = ['$project' => ['count' => ['$size' => '$uniqueCount']]];
        } else {
            $pipeline[] = ['$count' => 'count'];
        }

        $result = $this->executeMongoCommand($pipeline);
        return $result[0]->count ?? 0;
    }

    public function deleteCommentsByPostId($postId)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($postId);

            // Supprimer les commentaires liés au post
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->delete(['postId' => $objectId]);
            $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);

            echo "Commentaires associés au post supprimés avec succès.";
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la suppression des commentaires associés : " . $e->getMessage();
        } catch (MongoDB\BSON\Exception\InvalidArgumentException $e) {
            echo "L'ID du post fourni est invalide : " . $e->getMessage();
        }
    }

    public function deleteCommentAndDescendants($commentId)
    {
        $objectId = new MongoDB\BSON\ObjectId($commentId);
        $this->deleteDescendants($objectId);
        $this->delete($objectId);
    }

    private function deleteDescendants($parentCommentId)
    {
        try {
            // Récupérer tous les commentaires enfants
            $filter = ['parentCommentId' => $parentCommentId];
            $query = new MongoDB\Driver\Query($filter);
            $cursor = $this->_manager->executeQuery($this->_dbName . '.' . $this->_collectionName, $query);

            foreach ($cursor as $childComment) {
                $childId = $childComment->_id;

                // Appel récursif pour supprimer les descendants
                $this->deleteDescendants($childId);

                $bulk = new MongoDB\Driver\BulkWrite();
                $bulk->delete(['_id' => $childId]);
                $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la suppression des descendants : " . $e->getMessage();
        }
    }

}
?>