<?php

session_start();
require 'config/conn.php';


if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $countryId = $_GET['id'];

    try {
        $conn = $createConnection();  // Ensure this function is correct

        // SQL to get country details
        $getCountrySql = "SELECT * FROM country WHERE id = :country_id";

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare($getCountrySql);
        $stmt->bindParam(':country_id', $countryId);
        $stmt->execute();

        // Fetch the country details
        $country = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($country) {
            // Return country details as JSON or use the data as needed
            setFlashMessage('get-country-response', 'Country found.');
        } else {
            // If no country is found, set a flash message and redirect
            setFlashMessage('get-country-response', 'Country not found.');

            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        setFlashMessage('error', 'Error retrieving country: ' . $e->getMessage());

        exit;
    } finally {
        // Close the database connection
        $conn = null;
    }
} else {
    // If country_id is not provided, redirect with an error message
    setFlashMessage('get-country-response', 'Country ID not provided.');

    exit;
}
