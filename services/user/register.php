<?php
session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/send-mail.php';
require '../../util/flash.php';

if(is_post()) {

    function isValidName($name) {
        return preg_match('/^[A-Z][a-zA-Z\s]{1,}[a-zA-Z]$/', $name);
    }
    // Validation of inputs
    $errors = [];

    // Validate full name
    if(empty($_POST["fullname"])) {
        $errors[] = "Full name is required.";
    } else if(!isValidName($_POST["fullname"])) {
        $errors[] = "Full name is not valid. It must be at least 2 characters long and start with a capital letter. Only letters allowed.";
    } else {
        $fullname = $_POST["fullname"];
    }

    // Validate email
    if(empty($_POST["email"])) {
        $errors[] = "Email is required.";
    } else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $email = $_POST["email"];
    }

    // Validate confirm password
    if(empty($_POST["confirm_password"])) {
        $errors[] = "Confirm password is required.";
    } elseif ($_POST["password"] !== $_POST["confirm_password"]) {
        $errors[] = "Passwords do not match.";
    }

    // Validate password
    if(empty($_POST["password"])) {
        $errors[] = "Password is required.";
    } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Z])[A-Za-z\d]{8,}$/', $_POST["password"])) {
        $errors[] = "Password must contain at least one number and one capital letter, and be at least 8 characters long.";
    } else {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    // Validate terms checkbox
    if(empty($_POST["agree_terms"])) {
        $errors[] = "You must agree to the terms and conditions.";
    }

    if(empty($errors)) {
        // Sending email
        $mail = null;
        $email_verified_time = null;

        // Calculate expiration time 20 minutes in the future
        $expire_time = date("Y-m-d H:i:s", strtotime("+20 minutes"));
        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        $email_subject = 'Email verification';

        // Get the content of the HTML email template file
        $email_body = file_get_contents('../../public/user/templates/verification_code_email_template.html');

        // Replace placeholders with dynamic values
        $email_body = str_replace('{verification_code}', $verification_code, $email_body);

        try {
            //REGISTRATION
            $conn = $createConnection();
            // SQL query to insert user in database
            $query = "INSERT INTO user (full_name, email, password, verification_code, email_verified_at, verification_code_expires_at) 
                      VALUES (:fullname, :email, :password, :verification_code, :email_verified_at, :expire_time)";
            $statement = $conn->prepare($query);

            // Bind parameters
            $statement->bindParam(':fullname', $fullname);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':password', $password);
            $statement->bindParam(':verification_code', $verification_code);
            $statement->bindParam(':email_verified_at', $email_verified_time);
            $statement->bindParam(':expire_time', $expire_time);

            // Execute the SQL statement
            $statement->execute();

            // Get the ID of the inserted user
            $user_id = $conn->lastInsertId();

            // Insert the user ID into user_settings table
            $insertQuery = "INSERT INTO user_settings (user_id) VALUES (:user_id)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $user_id);
            $insertStmt->execute();

            $_SESSION['registration-email'] = $email;

            sendMail($email, $email_subject, $email_body);

            header("Location: ../../email-verification.php");
            exit();
        } catch (Exception $e) {
            // Handle exceptions
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            $conn = null;
        }
    } else {
        // Display errors
        setFlashMessage("register-response", $errors[0]);
        header("Location: ../../register.php");
        exit();
    }
}
?>
