<?php

session_start();
require 'config/conn.php';
require 'util/is-get.php';


if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    // For example:
    header("Location: login.php");
    exit;
}

try {
    // Include your database connection file
    $conn = $createConnection();

    // Prepare SQL statement to select user settings
    $sql = "SELECT * FROM country";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameter
    //$stmt->bindParam(":user_id", $userId);

    // Execute the statement
    $stmt->execute();

    // Fetch the user settings
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);


//    var_dump($statistics);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}