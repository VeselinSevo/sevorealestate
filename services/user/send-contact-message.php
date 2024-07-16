<?php
session_start();
require '../../config/conn.php';
require '../../util/flash.php';
require '../../util/is-post.php';

function isValidName($name) {
    return preg_match('/^[A-Z][a-zA-Z\s]{1,}[a-zA-Z]$/', $name);
}

if (is_post()) {
    $errors = [];

    // Sanitize and validate input
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $messageTitle = isset($_POST['message_title']) ? trim($_POST['message_title']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
        setFlashMessage('fullname-validation-msg', 'Full name is required.');
    } elseif (!isValidName($fullName)) {
        $errors[] = 'Full name is not valid. It must be at least 2 characters long and start with a capital letter. Only letters allowed.';
        setFlashMessage('fullname-validation-msg', 'Full name is not valid. It must be at least 2 characters long and start with a capital letter. Only letters allowed.');
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
        setFlashMessage('email-validation-msg', 'Email is required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
        setFlashMessage('email-validation-msg', 'Email is not valid.');
    }

    if (empty($messageTitle)) {
        $errors[] = 'Message title is required.';
        setFlashMessage('message-title-validation-msg', 'Message title is required.');
    }

    if (empty($message)) {
        $errors[] = 'Message is required.';
        setFlashMessage('message-validation-msg', 'Message is required.');
    }

    // Set flash messages for errors
    foreach ($errors as $error) {
        setFlashMessage('validation-error', $error);
    }

    // Check if there are any errors
    if (!empty($errors)) {
        // Redirect back to the contact form with errors
        header('Location: /contact.php');
        exit;
    }

    // Insert message into the database
    try {
        $conn = $createConnection();
        $stmt = $conn->prepare("INSERT INTO message (full_name, email, message_title, message) VALUES (:full_name, :email, :message_title, :message)");
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message_title', $messageTitle);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            setFlashMessage('contact-message-response', 'Thank you for your message!');
            header('Location: /contact.php');
        } else {
            setFlashMessage('contact-message-response', 'There was a problem sending your message. Please try again later.');
            header('Location: /contact.php');
        }

    } catch (PDOException $e) {
        setFlashMessage('database-error', 'Database error: ' . $e->getMessage());
        header('Location: /contact.php');
    }

    // Close the database connection
    $conn = null;
} else {
    setFlashMessage('invalid-request', 'Invalid request.');
    header('Location: /contact.php');
}
?>
