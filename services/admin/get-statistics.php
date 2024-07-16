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

    // Retrieve user ID from session
    $userId = $_SESSION["user"]["id"];

    // Prepare SQL statement to select user settings
    $sql = "SELECT q.id AS question_id, q.text AS question_text,
       a.id AS answer_id, a.text AS answer_text,
       COUNT(ua.user_id) AS num_responses
        FROM user_answer ua 
        INNER JOIN answer a ON ua.answer_id = a.id 
        INNER JOIN question q ON a.question_id = q.id 
        GROUP BY q.id, q.text, a.id, a.text
        ORDER BY q.id, num_responses DESC;";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameter
    //$stmt->bindParam(":user_id", $userId);



    // Execute the statement
    $stmt->execute();

    // Fetch the user settings
    $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);


//    var_dump($statistics);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}




