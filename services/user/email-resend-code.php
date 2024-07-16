<?php
session_start();
require '../../config/conn.php';
require '../../util/is-get.php';
require '../../util/send-mail.php';

if(is_get()) {
    $email = $_SESSION['registration-email'];

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

    // Generate verification code and expiration time
    $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    $expire_time = date("Y-m-d H:i:s", strtotime("+20 minutes"));
    $email_subject = 'Email verification';

    // Get the content of the HTML email template file
    $email_body = file_get_contents('../../public/user/templates/verification_code_email_template.html');

    // Replace placeholders with dynamic values
    $email_body = str_replace('{verification_code}', $verification_code, $email_body);

    try {
        // Update verification code and expiration time in the database
        $conn = $createConnection();
        $query = "UPDATE user SET verification_code = :verification_code, verification_code_expires_at = :expire_time WHERE email = :email";
        $statement = $conn->prepare($query);
        $statement->bindParam(':verification_code', $verification_code);
        $statement->bindParam(':expire_time', $expire_time);
        $statement->bindParam(':email', $email);
        $statement->execute();

        // Send verification email
        sendMail($email, $email_subject ,$email_body);

        $_SESSION['is-resend-successful'] = true;
        echo "Verification code resent successfully!";
        exit();
    } catch (Exception $e) {
        $_SESSION['is-resend-successful'] = false;
        echo "Failed to resend verification code. Please try again later.";
    } finally {
        // Close the database connection
        $conn = null;
    }
}
?>
