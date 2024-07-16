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


if(is_post()) {
    try {
        // Include your database connection file
        $conn = $createConnection();

        $countryName = $_POST['country_name'];
        $shortName = $_POST['country_short_name'];

        if (empty($countryName) || empty($shortName)) {
            echo "Country name and short name are required.";
        } else {
            $sql = "INSERT INTO country (name, short_name) VALUES (:name, :short_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $countryName);
            $stmt->bindParam(':short_name', $shortName);

            try {
                $stmt->execute();
                setFlashMessage("country-add-response", "Country successfully added!");
                header("Location: ../../admin-panel.php?source=add_country");
            } catch (PDOException $e) {
                // Handle database error
                header("Location: ../../admin-panel.php?source=add_country");
                setFlashMessage("country-add-response", "Something went wrong");
            }


        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
}
