<?php
session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';

if(is_post()) {
    if(empty($_SESSION['user'])) {
        try {
            $conn = $createConnection();
            $query = "SELECT id, email, full_name, role_id, email_verified_at, language, theme, cards_per_row, currency, questionnaire_submitted_at, created_at, password FROM user WHERE email = :email";

            $stmt = $conn->prepare($query);
            // Bind parameters
            $stmt->bindParam(':email', $_POST['email']);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify the hashed password
                if (password_verify($_POST['password'], $user['password'])) {
                    if($user['email_verified_at'] == null) {
                        die("Please verify your account <a href='../../email-verification.php'>Verify account</a>");
                    }

                    // Password is correct
                    $_SESSION['user'] = $user;
                    setFlashMessage("fullname", $user['full_name']);
                    $_SESSION['role_id'] = $user['role_id']; // Add role_id to session
                    // Redirect to index.php or any other page after login

                    require 'get-settings.php';
                    header("Location: ../../index.php");
                    exit();
                } else {
                    // Incorrect password
                    $_SESSION['error'] = "Please try again. Username or password is incorrect.";
                    header("Location: ../../login.php");
                    exit();
               }
            } else {
                // User not found
                $_SESSION['error'] = "Please try again. Username or password is incorrect.";
                header("Location: ../../login.php");
                exit();
            }
        } catch (PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            $conn = null;
        }
    } else {
        $_SESSION['error'] = "You are already logged in. Please log out first.";
        header("Location: ../../login.php");
        exit();
    }
}
?>
