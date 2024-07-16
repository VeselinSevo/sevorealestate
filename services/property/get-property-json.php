<?php
require 'config/conn.php';

try {
    // Retrieve the property ID from the URL parameter
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    // Check if ID is provided
    if ($id) {
        $conn = $createConnection();
        // SQL query to select a property by ID
        $query = "SELECT
                    property.*,
                    structure.*,
                    type.name AS type_name,
                    user.full_name
                    FROM
                        property
                    INNER JOIN
                        type ON property.type_id = type.id
                    INNER JOIN
                        structure ON property.id = structure.id
                    INNER JOIN
                            user ON property.user_id = user.id
                    WHERE
                    property.id = :id";


        // Prepare the SQL statement
        $statement = $conn->prepare($query);

        // Bind parameter
        $statement->bindParam(':id', $id);

        // Execute the SQL statement
        $statement->execute();

        // Fetch the property as an associative array
        $property = $statement->fetch(PDO::FETCH_ASSOC);

        // Check if property exists
        if (!$property) {
            header("Location: index.php");
            echo "Property not found.";

        }
    } else {
        echo "No property ID provided.";
    }
} catch(PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
echo json_encode($property);
?>
