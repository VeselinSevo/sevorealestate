<?php
session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/send-mail.php';

    if(is_post()) {
        $email = $_POST["email"];

        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);
        $expire_time = date("Y-m-d H:i:s", strtotime("+20 minutes"));
        $email_subject = 'Reset Password';


        // Get the content of the HTML email template file
        $email_body = file_get_contents('../../public/user/templates/reset_password_email_template.html');

        // Replace dynamic content if needed
        $email_body = str_replace('{token}', $token, $email_body);

        try {
            $conn = $createConnection();

            $query = "UPDATE user SET reset_token_hash = :token, reset_token_expires_at = :expiry WHERE email = :email";
            $statement = $conn->prepare($query);

            // Bind parameters
            $statement->bindParam(':token', $token_hash);
            $statement->bindParam(':expiry', $expire_time);
            $statement->bindParam(':email', $email);


            // Execute the SQL statement
            $statement->execute();

            $rowsAffected = $statement->rowCount();
            if ($rowsAffected > 0) {
               sendMail($email, $email_subject, $email_body);

                $_SESSION['is-password-reset-email-valid'] = true;
                header("Location: ../../reset-password-start.php");
                exit();
            } else {
                // No rows updated, verification failed
                $_SESSION['is-password-reset-email-valid'] = false;
                $_SESSION['reset-password-error'] = 'Email provided is not found.';
                header("Location: ../../reset-password-start.php");
            }
        } catch (Exception $e) {
            // Handle exceptions
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            $conn = null;
        }



    }

?>