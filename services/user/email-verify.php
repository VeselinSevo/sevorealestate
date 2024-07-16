<?php
session_start();
require '../../config/conn.php';
require '../../util/is-post.php';

if(is_post()) {
    $email = $_SESSION["registration-email"];
    $postData = file_get_contents("php://input");

    // Decode the JSON data
    $requestData = json_decode($postData, true); // true to get an associative array

    $verification_code = null;
    // Check if 'verificationCode' exists in the decoded data
    if (isset($requestData['verificationCode'])) {
        $verification_code = $requestData['verificationCode'];
        // Now you have $verificationCode, you can use it in your PHP script
    } else {
        // Handle case where 'verificationCode' is not found
        http_response_code(400); // Bad Request
        exit("Verification code not found in request.");
    }

    $now = date("Y-m-d H:i:s");

    // Check if the user is already verified
    try {
        $conn = $createConnection();
        $query = "SELECT email_verified_at FROM user WHERE email = :email";
        $statement = $conn->prepare($query);
        $statement->bindParam(':email', $email);
        $statement->execute();
        $result = $statement->fetch();

        if ($result['email_verified_at'] !== null) {
            echo "User is already verified.";
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    } finally {
        // Close the database connection
        $conn = null;
    }

    try {

        $conn = $createConnection();
        $query = "UPDATE user SET email_verified_at = :now WHERE email = :email AND verification_code = :verification_code AND verification_code_expires_at > :now";

        $stmt = $conn->prepare($query);
        // Bind parameters
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':verification_code', $verification_code);

        // Execute the SQL statement
        $stmt->execute();

        $rowsAffected = $stmt->rowCount();
        if ($rowsAffected > 0) {
            // Verification successful
            // Redirect or perform further actions
            header("Location: ../../email-verification-success.php");
            exit(); // Stop script execution after redirection
        } else {
            // No rows updated, verification failed
            echo 'Verification code is invalid.';

        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
}
?>
