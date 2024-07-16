<?php

session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';

if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    // For example:
    header("Location: login.php");
    exit;
}

if (is_post()) {
    if (isset($_POST['message_id'])) {
        $messageId = $_POST['message_id'];

        try {
            $conn = $createConnection();

            // Prepare and execute SQL statement to delete the city
            $stmt = $conn->prepare("DELETE FROM message WHERE id = :message_id");
            $stmt->bindParam(':message_id', $messageId);
            $stmt->execute();

            // Set success flash message
            setFlashMessage('delete-message-response', 'Message deleted successfully.');

            // Redirect to a page indicating successful deletion
            header("Location: ../../admin-panel.php?source=show_messages");
            exit;
        } catch (PDOException $e) {
            // Handle database errors
            setFlashMessage('error', 'Error deleting messages: ' . $e->getMessage());
            header("Location: ../../admin-panel.php?source=show_messages");
            exit;
        } finally {
            // Close the database connection
            $conn = null;
        }
    } else {
        // If city_id is not provided, redirect with an error message
        setFlashMessage('delete-message-response', 'Message ID not provided.');
        header("Location: ../../admin-panel.php?source=show_message");
        exit;
    }
} else {
    // If it's not a POST request, redirect with an error message
    setFlashMessage('delete-message-response', 'Invalid request.');
    header("Location: ../../admin-panel.php?source=show_message");
    exit;
}
