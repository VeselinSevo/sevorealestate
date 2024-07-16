<?php

session_start();
require '../../config/conn.php';
require '../../util/is-post.php';
require '../../util/flash.php';

if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.php");
    exit;
}

$countryId = $_POST['country_id'];

if (is_post()) {
    if (empty($_POST['country_id']) || empty($_POST['country_short_name']) || empty($_POST['country_name'])) {
        setFlashMessage('edit-country-response', 'All fields are required.');
        header("Location: ../../admin-panel.php?source=edit_country&id=" . $countryId);
        exit;
    }

    $countryName = trim($_POST['country_name']);
    $countryShortName = trim($_POST['country_short_name']);
    $countryId = $_POST['country_id'];

    try {
        $conn = $createConnection();

        // Check if a country with the same name already exists
        $checkCountrySql = "SELECT COUNT(*) FROM country WHERE name = :country_name AND id != :country_id";
        $stmt = $conn->prepare($checkCountrySql);
        $stmt->bindParam(':country_name', $countryName);
        $stmt->bindParam(':country_id', $countryId);
        $stmt->execute();
        $existingCount = $stmt->fetchColumn();

        if ($existingCount > 0) {
            setFlashMessage('edit-country-response', 'That country already exists.');
            header("Location: ../../admin-panel.php?source=edit_country&id=" . $countryId);
            exit;
        }

        // Update the country if no duplicates were found
        $updateCountrySql = "UPDATE country SET name = :country_name, short_name = :country_short_name WHERE id = :country_id";
        $stmt = $conn->prepare($updateCountrySql);
        $stmt->bindParam(':country_id', $countryId);
        $stmt->bindParam(':country_name', $countryName);
        $stmt->bindParam(':country_short_name', $countryShortName);
        $stmt->execute();

        setFlashMessage('edit-country-response', 'Country updated successfully.');
        header("Location: ../../admin-panel.php?source=edit_country&id=" . $countryId);
        exit;
    } catch (PDOException $e) {
        setFlashMessage('edit-country-response', 'Error updating country: ' . $e->getMessage());
        header("Location: ../../admin-panel.php?source=edit_country&id=" . $countryId);
        exit;
    } finally {
        $conn = null;
    }
} else {
    setFlashMessage('edit-country-response', 'Invalid request.');
    header("Location: ../../admin-panel.php?source=edit_country&id=" . $countryId);
    exit;
}

?>
