<?php

session_start();
require "util/flash.php";
require "util/is-logged-in.php";
require 'util/is-get.php';
require "util/is-admin.php";
require 'util/unset-session-prop.php';
require 'config/conn.php';


if(is_get()) {
    $token = $_GET["token"];
    $token_hash = hash("sha256", $token);

    try {
        $conn = $createConnection();

        $query = "SELECT * FROM user WHERE reset_token_hash = :token";
        $statement = $conn->prepare($query);

        // Bind parameters
        $statement->bindParam(':token', $token_hash);


        // Execute the SQL statement
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Token not found");
        }

        if(strtotime($user["reset_token_expires_at"] <= time())) {
            die("Token has expired");
        }

        $_SESSION['token'] = $token;

        require 'public/user/reset-password.phtml';
    } catch (Exception $e) {
        // Handle exceptions
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
    //unsetSessionProp('token');
}
?>