<?php
require '../../config/conn.php';
$properties = null;
try {
    $conn = $createConnection();
    // SQL query to select all user property

    $userId = $_GET['id'];
    $query = "SELECT property.*, type.id AS type_id, type.name AS type_name, 
       country.name AS country_name, country.short_name AS country_short_name, city.name AS city_name
        FROM property 
        INNER JOIN type ON property.type_id = type.id 
        INNER JOIN city ON property.city_id = city.id
        INNER JOIN country ON city.country_id = country.id
        WHERE user_id = :id";

    // Prepare the SQL statement
    $statement = $conn->prepare($query);

    $statement->bindParam(':id', $userId);

    // Execute the SQL statement
    $statement->execute();

    // Fetch all rows as an associative array
    $properties = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Output the property in PHP

} catch(PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;

// Encode property into JSON
echo json_encode($properties);
?>
