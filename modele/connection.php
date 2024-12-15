<?php
class Connection
{
    private static $manager;
    private static $uri = 'mongodb+srv://<user>:<pwd>@cluster.hipsj.mongodb.net/';

    public static function connect()
    {
        try {
            self::$manager = new MongoDB\Driver\Manager(self::$uri);
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Problème de connexion : " . $e->getMessage();
            exit();
        }
    }

    public static function getManager()
    {
        return self::$manager;
    }
}

?>