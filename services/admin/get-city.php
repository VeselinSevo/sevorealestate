<?php

session_start();
require 'config/conn.php';


if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $cityId = $_GET['id'];

    try {
        $conn = $createConnection();  // Ensure this function is correct

        // SQL to get city details
        $getCitySql = "SELECT * FROM city WHERE id = :city_id";

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare($getCitySql);
        $stmt->bindParam(':city_id', $cityId);
        $stmt->execute();

        // Fetch the city details
        $city = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($city) {
            // Return city details as JSON or use the data as needed
            setFlashMessage('get-city-response', 'City found.');
        } else {
            // If no city is found, set a flash message and redirect
            setFlashMessage('get-city-response', 'City not found.');

            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        setFlashMessage('error', 'Error retrieving city: ' . $e->getMessage());

        exit;
    } finally {
        // Close the database connection
        $conn = null;
    }
} else {
    // If city_id is not provided, redirect with an error message
    setFlashMessage('get-city-response', 'City ID not provided.');

    exit;
}
