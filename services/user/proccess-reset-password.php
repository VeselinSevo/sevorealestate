<?php
session_start();
require '../../util/is-get.php';
require '../../config/conn.php';

try {
    $password = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $token = $_POST["token"];
    $token_hash = hash("sha256", $token);
    $conn = $createConnection();

    // Validate password
    $errors = [];

    if (empty($password)) {
        $errors[] = "Password is required.";
    } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter, one uppercase letter, one digit, and be at least 8 characters long.";
    }

    // Validate confirm password
    if (empty($confirmPassword)) {
        $errors[] = "Confirm password is required.";
    } else if ($password !== $confirmPassword) {
        $errors[] = "Password and confirm password do not match.";
    }

    if (!empty($errors)) {
        // Set flash message for password validation errors
        $_SESSION['reset-password-response'] = $errors[0];
        header("Location: ../../reset-password.php?token=$token");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    $select_query = "SELECT * FROM user WHERE reset_token_hash = :token";
    $select_statement = $conn->prepare($select_query);


    $select_statement->bindParam(':token', $token_hash);


    $select_statement->execute();

    $user = $select_statement->fetch(PDO::FETCH_ASSOC);

    $update_query = "UPDATE user SET password = :password, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = :id";
    $update_statement = $conn->prepare($update_query);

    // Bind parameters
    $update_statement->bindParam(':password', $hashedPassword);
    $update_statement->bindParam(':id', $user['id']);

    $update_statement->execute();

    header("Location: ../../reset-password-success.php");

} catch (Exception $e) {
    // Handle exceptions
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}
?>
