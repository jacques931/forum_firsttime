<?php

class MongodbManager
{
    protected $_manager;
    protected $_dbName;
    protected $_collectionName;

    public function __construct($collectionName)
    {
        $this->_dbName = "forum";
        $this->_collectionName = $collectionName;
        Connection::connect();
        $this->_manager = Connection::getManager();
    }

    public function getCollectionsCount()
    {
        try {
            $pipeline = [
                [
                    '$count' => 'count'
                ]
            ];
            $result = $this->executeMongoCommand($pipeline);

            return $result[0]->count ?? 0;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération du nombre de commentaires : " . $e->getMessage();
            return 0;
        }
    }
    public function getCollectionsCountByUserId($userId)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($userId);

            $pipeline = [
                [
                    '$match' => ['auteur' => $objectId]
                ],
                [
                    '$count' => 'count'
                ]
            ];

            $result = $this->executeMongoCommand($pipeline);

            return $result[0]->count ?? 0;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération du nombre de documents pour l'utilisateur {$userId} : " . $e->getMessage();
            return 0;
        }
    }

    public function create($post)
    {
        try {
            $data = $post->getData();
            $data['_id'] = new MongoDB\BSON\ObjectId();
    
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->insert($data);
            $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la création : " . $e->getMessage();
        }
    }
    public function update($filter, $update)
    {
        try {
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->update($filter, ['$set' => $update], ['multi' => false, 'upsert' => false]);
            $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);
            return true;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la mise à jour : " . $e->getMessage();
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);

            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->delete(['_id' => $objectId]);
            $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);

            echo "Document avec l'ID {$id} supprimé avec succès.";
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la suppression : " . $e->getMessage();
        } catch (MongoDB\BSON\Exception\InvalidArgumentException $e) {
            echo "L'ID fourni est invalide : " . $e->getMessage();
        }
    }

    public function executeMongoCommand($pipeline)
    {
        $command = new MongoDB\Driver\Command([
            'aggregate' => $this->_collectionName,
            'pipeline' => $pipeline,
            'cursor' => new stdClass(),
        ]);

        $cursor = $this->_manager->executeCommand($this->_dbName, $command);
        return $cursor->toArray();
    }
}
?>