<?php
session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';


// Check if the form is submitted
if(is_post()) {
    // Include your database connection file
    $conn = $createConnection();

    // Retrieve user ID from session
    $userId = $_SESSION["user"]['id'];

    // Retrieve form data
    $language = $_POST["language"];
    $currency = $_POST["currency"];
    $cardsPerRow = $_POST["cards_per_row"];
    $theme = $_POST["theme"];

    // Prepare SQL statement
    $sql = "UPDATE user_settings SET language = :language, currency = :currency, cards_per_row = :cards_per_row, theme = :theme WHERE user_id = :user_id";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(":language", $language);
    $stmt->bindParam(":currency", $currency);
    $stmt->bindParam(":cards_per_row", $cardsPerRow);
    $stmt->bindParam(":theme", $theme);
    $stmt->bindParam(":user_id", $userId);

    // Execute the statement
    if ($stmt->execute()) {
        // Settings updated successfully
        // You can redirect the user to a success page or display a success message
        // For example:

        require 'get-settings.php';
        setFlashMessage("settings-changed-success", 'Settings successfully changed.');
        header("Location: ../../account.php");
        // exit;
    } else {
        // Error occurred while updating settings
        // You can redirect the user to an error page or display an error message
        // For example:
        setFlashMessage("error", 'Something went wrong.');
//        header("Location: ../../account.php");
        // exit;
    }


    // Close statement
    $stmt = null;

    // Close database connection (optional if PDO is in persistent mode)
    // $pdo = null;
}
?>
