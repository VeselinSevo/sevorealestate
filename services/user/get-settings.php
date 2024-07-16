<?php
session_start();
require '../../config/conn.php';
require '../../util/is-get.php';


if (!isset($_SESSION["user"])) {
    // Redirect the user to the login page or display an error message
    // For example:
     header("Location: login.php");
     exit;
}

try {
    // Include your database connection file
    $conn = $createConnection();

// Retrieve user ID from session
    $userId = $_SESSION["user"]["id"];

// Prepare SQL statement to select user settings
    $sql = "SELECT language, currency, cards_per_row, theme FROM user_settings WHERE user_id = :user_id";

// Prepare the statement
    $stmt = $conn->prepare($sql);

// Bind parameter
    $stmt->bindParam(":user_id", $userId);

// Execute the statement
    $stmt->execute();

// Fetch the user settings
    $userSettings = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user-settings'] = $userSettings;

// Check if user settings were found
    if ($userSettings) {
        // User settings found
        // You can access the settings using $userSettings["language"], $userSettings["currency"], etc.
    } else {
        // No user settings found
        // You can redirect the user to a settings page to set up their preferences or display a message
    }
}
catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}



