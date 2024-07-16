<?php
require '../../config/conn.php';
$properties = null;

try {
    $conn = $createConnection();

    // Retrieve the search term from the URL parameter
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // SQL query to select properties, with search functionality
    $query = "
        SELECT 
            property.*, 
            type.id AS type_id, 
            type.name AS type_name, 
            country.name AS country_name, 
            country.short_name AS country_short_name, 
            city.name AS city_name
        FROM 
            property 
        INNER JOIN 
            type ON property.type_id = type.id 
        INNER JOIN 
            city ON property.city_id = city.id
        INNER JOIN 
            country ON city.country_id = country.id
        WHERE 
            :search = '' OR property.name LIKE :searchWild
    ";

    // Prepare the SQL statement
    $statement = $conn->prepare($query);

    // Bind parameters
    $searchWild = '%' . $search . '%';
    $statement->bindParam(':search', $search);
    $statement->bindParam(':searchWild', $searchWild);

    // Execute the SQL statement
    $statement->execute();

    // Fetch all rows as an associative array
    $properties = $statement->fetchAll(PDO::FETCH_ASSOC);

//    header("Location: ../../index.php?search=" . $search);
    // Output the properties in JSON format
    echo json_encode($properties);


} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>

