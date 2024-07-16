<?php
require '../../config/conn.php';
$countries = null;
try {
    $conn = $createConnection();
    // SQL query to select all property
    $query = "SELECT * FROM country";


    // Prepare the SQL statement
    $statement = $conn->prepare($query);

    // Execute the SQL statement
    $statement->execute();

    // Fetch all rows as an associative array
    $countries = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Output the property in PHP

} catch(PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;

// Encode property into JSON
echo json_encode($countries);
?>