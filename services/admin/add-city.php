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

        $cityName = $_POST['city_name'];
        $countryId = $_POST['country_id'];

        if (empty($cityName) || empty($countryId)) {
            echo "City name and country are required.";
        } else {
            $sql = "INSERT INTO city (name, country_id) VALUES (:name, :country_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $cityName);
            $stmt->bindParam(':country_id', $countryId);

            try {
                $stmt->execute();
                setFlashMessage("city-add-response", "City successfully added!");
                header("Location: ../../admin-panel.php?source=add_city");
            } catch (PDOException $e) {
                // Handle database error
                header("Location: ../../admin-panel.php?source=add_city");
                setFlashMessage("city-add-response", "Something went wrong");
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
