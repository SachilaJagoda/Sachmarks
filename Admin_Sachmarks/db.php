<?php
class Database {

    public static $connection;

    public static function setUpConnection() {
        $host = 'localhost';
        $user = 'root';
        $pass = 'Sachilajava';
        $db = 'sachmarks_db';

        self::$connection = new mysqli($host, $user, $pass, $db);

        if (self::$connection->connect_error) {
            die("Connection failed: " . self::$connection->connect_error);
        }
    }

    public static function closeConnection() {
        if (self::$connection) {
            self::$connection->close();
        }
    }

    public static function iud($q) {
        self::setUpConnection();
        self::$connection->query($q);
        self::closeConnection(); 
    }

    public static function search($q) {
        self::setUpConnection();
        $resultset = self::$connection->query($q);
        self::closeConnection(); 
        return $resultset;
    }

    public static function prepareAndExecute($query, $types, ...$params) {
        self::setUpConnection();
        $stmt = self::$connection->prepare($query);

        if ($stmt === false) {
            die("Error preparing statement: " . self::$connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result(); 
        $stmt->close();
        self::closeConnection(); 

        return $result;
    }
}
?>
