<?php

session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';

if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.php");
    exit;
}

$cityId = $_POST['city_id'];

if (is_post()) {
    if (empty($_POST['city_id']) || empty($_POST['city_name']) || empty($_POST['country_id'])) {
        setFlashMessage('edit-city-response', 'All fields are required.');
        header("Location: ../../admin-panel.php?source=edit_city&id=" . $cityId);
        exit;
    }

    $cityName = trim($_POST['city_name']);
    $countryId = $_POST['country_id'];

    try {
        $conn = $createConnection();

        // Check if a city with the same name already exists in the same country
        $checkCitySql = "SELECT COUNT(*) FROM city WHERE name = :city_name AND country_id = :country_id AND id != :city_id";
        $stmt = $conn->prepare($checkCitySql);
        $stmt->bindParam(':city_name', $cityName);
        $stmt->bindParam(':country_id', $countryId);
        $stmt->bindParam(':city_id', $cityId);
        $stmt->execute();
        $existingCount = $stmt->fetchColumn();

        if ($existingCount > 0) {
            setFlashMessage('edit-city-response', 'That city already exists in the specified country.');
            header("Location: ../../admin-panel.php?source=edit_city&id=" . $cityId);
            exit;
        }

        // Update the city if no duplicates were found
        $updateCitySql = "UPDATE city SET name = :city_name, country_id = :country_id WHERE id = :city_id";
        $stmt = $conn->prepare($updateCitySql);
        $stmt->bindParam(':city_id', $cityId);
        $stmt->bindParam(':city_name', $cityName);
        $stmt->bindParam(':country_id', $countryId);
        $stmt->execute();

        setFlashMessage('edit-city-response', 'City updated successfully.');
        header("Location: ../../admin-panel.php?source=edit_city&id=" . $cityId);
        exit;
    } catch (PDOException $e) {
        setFlashMessage('edit-city-response', 'Error updating city: ' . $e->getMessage());
        header("Location: ../../admin-panel.php?source=edit_city&id=" . $cityId);
        exit;
    } finally {
        $conn = null;
    }
} else {
    setFlashMessage('edit-city-response', 'Invalid request.');
    header("Location: ../../admin-panel.php?source=edit_city&id=" . $cityId);
    exit;
}

?>
