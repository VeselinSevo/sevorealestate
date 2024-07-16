<?php

//class Connection {
//    private static $conn = null;
//
//    public $connection = null;
//    private function __construct() {
//        // Database credentials
//        $servername = "localhost"; // Change this to your database server address
//        $username = "root"; // Change this to your database username
//        $password = ""; // Change this to your database password
//        $database = "sevorealestate"; // Change this to your database name
//
//        try {
//            // Create connection
//            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
//            // Set PDO to throw exceptions on error
//            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//            // Additional PDO configurations
//            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//
//            // Echo a message if connected successfully
//            // echo "Connected successfully";
//        } catch(PDOException $e) {
//            // Handle connection error
//            die("Connection failed: " . $e->getMessage());
//        }
//
//        $this->connection = $conn;
//    }
//
//    public static function getConnection() {
//        if (is_null(self::$conn)) {
//            self::$conn = new self();
//        }
//
//        return self::$conn;
//    }
//
//    public function __call($method, $args) {
//        if (method_exists($this->connection, $method)) {
//            return call_user_func_array(array($this->connection, $method), $args);
//        } else {
//            return call_user_func(array($this, $method), $args);
//        }
//    }
//
//    public function __get($property) {
//        if (property_exists($this->connection, $property)) {
//            return $this->connection->$property;
//        } else {
//            return $this->$property;
//        }
//    }
//}
//
//$conn = Connection::getConnection();

$_conn = null;

$createConnection = function() use ($_conn) {
    if ($_conn) {
        return $_conn;
    }
    // Database credentials
    $servername = "db"; // Change this to your database server address
    $username = "root"; // Change this to your database username
    $password = "root"; // Change this to your database password
    $database = "sevorealestate"; // Change this to your database name

    $conn = null;
    try {
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Set PDO to throw exceptions on error
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Additional PDO configurations
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Echo a message if connected successfully
        // echo "Connected successfully";
    } catch(PDOException $e) {
        // Handle connection error
        die("Connection failed: " . $e->getMessage());
    }

    $_conn = $conn;
    return $_conn;
}
?>