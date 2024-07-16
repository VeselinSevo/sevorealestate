<?php

session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';

if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php");
    exit;
}

if (is_post()) {
    if (isset($_POST['country_id'])) {
        $countryId = $_POST['country_id'];

        try {
            $conn = $createConnection();  // Ensure this function is correct

            // SQL to check if there are cities in the country
            $checkCitiesSql = "SELECT COUNT(*) as city_count FROM city WHERE country_id = :country_id";

            // Prepare and execute the SQL statement
            $checkStmt = $conn->prepare($checkCitiesSql);
            $checkStmt->bindParam(':country_id', $countryId);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['city_count'] > 0) {
                // Set flash message indicating there are cities in the country
                setFlashMessage('delete-country-response', 'Cannot delete country. Please delete all cities in the country first.');
                header("Location: ../../admin-panel.php?source=show_countries");
                exit;
            }

            // SQL to delete the country
            $deleteCountrySql = "DELETE FROM country WHERE id = :country_id";

            // Prepare and execute the SQL statement
            $stmt = $conn->prepare($deleteCountrySql);
            $stmt->bindParam(':country_id', $countryId);
            $stmt->execute();

            // Set success flash message
            setFlashMessage('delete-country-response', 'Country deleted successfully.');

            // Redirect to a page indicating successful deletion
            header("Location: ../../admin-panel.php?source=show_countries");
            exit;
        } catch (PDOException $e) {
            // Handle database errors
            setFlashMessage('error', 'Error deleting country: ' . $e->getMessage());
            header("Location: ../../admin-panel.php?source=show_countries");
            exit;
        } finally {
            // Close the database connection
            $conn = null;
        }
    } else {
        // If country_id is not provided, redirect with an error message
        setFlashMessage('delete-country-response', 'Country ID not provided.');
        header("Location: ../../admin-panel.php?source=show_countries");
        exit;
    }
} else {
    // If it's not a POST request, redirect with an error message
    setFlashMessage('delete-country-response', 'Invalid request.');
    header("Location: ../../admin-panel.php?source=show_countries");
    exit;
}
