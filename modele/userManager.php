<?php

class User extends MongodbManager
{
    public function __construct()
    {
        parent::__construct("users");
    }

    public function create($data)
    {
        try {
            $data['_id'] = new MongoDB\BSON\ObjectId();
            $data['created_at'] = new MongoDB\BSON\UTCDateTime();

            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->insert($data);
            $this->_manager->executeBulkWrite($this->_dbName . '.' . $this->_collectionName, $bulk);
            return [
                '_id' => (string)$data['_id'],
                'email' => $data['email'],
                'pseudo' => $data['pseudo']
            ];
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la création : " . $e->getMessage();
            return false;
        }
    }

    public function checkDuplicate($pseudo, $email, $currentUserId)
    {
        try {
            $filter = [
                '$or' => [
                    ['pseudo' => $pseudo],
                    ['email' => $email]
                ],
                '_id' => ['$ne' => new MongoDB\BSON\ObjectId($currentUserId)]
            ];

            $options = [];
            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $this->_manager->executeQuery($this->_dbName . '.' . $this->_collectionName, $query);
            $user = current($cursor->toArray());

            if ($user) {
                if ($user->pseudo === $pseudo) {
                    return "pseudo";
                }
                if ($user->email === $email) {
                    return "email";
                }
            }
            return false; // Aucun doublon trouvé
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la vérification des doublons : " . $e->getMessage();
            return false;
        }
    }

    public function exists($email, $password)
    {
        try {
            $filter = ['email' => $email];
            $options = [];

            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $this->_manager->executeQuery($this->_dbName . '.' . $this->_collectionName, $query);

            $user = current($cursor->toArray());
            if (!$user) {
                return false;
            }

            // Vérification du mot de passe
            if (hash("sha256", $password) === $user->password) {
                return [
                    '_id' => (string)$user->_id,
                    'email' => $user->email,
                    'pseudo' => $user->pseudo
                ];
            } else {
                return false;
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la vérification : " . $e->getMessage();
            return false;
        }
    }

    public function getUserById($id)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);

            $filter = ['_id' => $objectId];
            $options = [];

            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $this->_manager->executeQuery($this->_dbName . '.' . $this->_collectionName, $query);

            $user = current($cursor->toArray());
            if (!$user) {
                return false;
            }

            return [
                '_id' => (string)$user->_id,
                'email' => $user->email,
                'pseudo' => $user->pseudo,
                'created_at' => DateFormatter::formatToDayMonthYear($user->created_at),
            ];
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }
}
?>