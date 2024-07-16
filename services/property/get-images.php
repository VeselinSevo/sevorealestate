<?php
require '../../config/conn.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    error_log("Script started"); // Debugging output
    $conn = $createConnection();
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $property_id = $_GET['id'];
        error_log("Property ID: $property_id"); // Debugging output
        $query = "SELECT name FROM image WHERE property_id = :property_id";
        $statement = $conn->prepare($query);
        $statement->bindParam(':property_id', $property_id, PDO::PARAM_INT);
        $statement->execute();

        $image_names = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        error_log("Image names fetched"); // Debugging output

        echo json_encode($image_names);
    } else {
        error_log("Invalid property_id"); // Debugging output
        echo json_encode(['error' => 'Invalid property_id']);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
