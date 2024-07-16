<?php
require '../../config/conn.php';

$cities = null;

try {
    // Check if the country parameter is set
    $conn = $createConnection();
    if(isset($_GET['country_id'])) {
        $countryId = $_GET['country_id'];

        // SQL query to select cities based on the country
        $query = "SELECT city.* FROM city RIGHT JOIN country ON city.country_id = country.id WHERE country.id = :countryId AND city.id IS NOT NULL AND city.name IS NOT NULL";

        // Prepare the SQL statement
        $statement = $conn->prepare($query);

        // Bind the country parameter to the query
        $statement->bindParam(':countryId', $countryId);

        // Execute the SQL statement
        $statement->execute();

        // Fetch all rows as an associative array
        $cities = $statement->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // If country parameter is not set, return all cities
        $query = "SELECT * FROM city WHERE id IS NOT NULL AND name IS NOT NULL";
        $statement = $conn->query($query);
        $cities = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if cities array is empty

        // Encode cities into JSON and output
        echo json_encode($cities);


} catch(PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
