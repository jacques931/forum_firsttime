<?php

class PostManager extends MongodbManager
{
    public function __construct()
    {
        parent::__construct("posts");
    }

    public function getPosts($offset, $limit)
    {
        try {
            $pipeline = [
                [
                    '$sort' => [
                        'created_at' => -1
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'users',
                        'localField' => 'auteur',
                        'foreignField' => '_id',
                        'as' => 'author_info'
                    ]
                ],
                [
                    '$skip' => $offset
                ],
                [
                    '$limit' => $limit
                ]
            ];

            $documents = $this->executeMongoCommand($pipeline);

            $posts = [];
            foreach ($documents as $document) {
                $posts[] = new Post(
                    (string)$document->_id,
                    $document->titre,
                    $document->contenu,
                    $document->author_info ?? [],
                    DateFormatter::getDateDifference($document->created_at),
                    DateFormatter::getDateDifference($document->updated_at)
                );
            }

            return $posts;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération des posts : " . $e->getMessage();
            return [];
        }
    }

    public function find($id)
    {
        try {
            $pipeline = [
                [
                    '$match' => [
                        '_id' => new MongoDB\BSON\ObjectId($id)
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'users',
                        'localField' => 'auteur',
                        'foreignField' => '_id',
                        'as' => 'author_info'
                    ]
                ],
                ['$limit' => 1]
            ];

            $document = current($this->executeMongoCommand($pipeline));

            if (!$document) {
                return null;
            }

            $post = new Post(
                (string)$document->_id,
                $document->titre,
                $document->contenu,
                $document->author_info ?? [],
                DateFormatter::formatToDayMonthYear($document->created_at),
                    DateFormatter::formatToDayMonthYear($document->updated_at)
            );

            return $post;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération du post : " . $e->getMessage();
            return null;
        }
    }

    public function getUniqueUserCountByAuthor($authorId)
    {
        try {
            $pipeline = [
                [
                    '$match' => [
                        'auteur' => new MongoDB\BSON\ObjectId($authorId)
                    ]
                ],
                // Lier les commentaires aux posts
                [
                    '$lookup' => [
                        'from' => 'comments',
                        'localField' => '_id',
                        'foreignField' => 'postId',
                        'as' => 'comments'
                    ]
                ],
                [
                    '$unwind' => '$comments'
                ],
                // Extraire les auteurs uniques des commentaires
                [
                    '$group' => [
                        '_id' => null,
                        'uniqueAuthors' => ['$addToSet' => '$comments.auteur']
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'userCount' => ['$size' => '$uniqueAuthors']
                    ]
                ]
            ];

            $result = $this->executeMongoCommand($pipeline);

            return $result[0]->userCount ?? 0;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération du nombre d'utilisateurs uniques : " . $e->getMessage();
            return 0;
        }
    }

    public function deletePostAndComments($postId)
    {
        $objectId = new MongoDB\BSON\ObjectId($postId);
        // Supprime les commentaires liés au post
        $commentManager = new CommentManager();
        $commentManager->deleteCommentsByPostId($postId);
        // Supprime le post
        $this->delete($objectId);
    }

}
?>