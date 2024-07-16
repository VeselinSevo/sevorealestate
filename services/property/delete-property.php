<?php
require '../../config/conn.php';
require '../../util/is-post.php';
session_start();

if (is_post()) {
    $conn = null;
    try {
        // Check if the property ID and user ID are provided via POST
        if(isset($_POST['property_id']) && isset($_SESSION['user']['id'])) {
            // Retrieve the property ID and user ID from the POST data and session
            $propertyId = $_POST['property_id'];
            $userId = $_SESSION['user']['id'];

            // Connect to the database
            $conn = $createConnection();

            // Start a transaction
            $conn->beginTransaction();

            // Check if the property belongs to the current user
            $stmtCheckOwnership = $conn->prepare("SELECT COUNT(*) FROM property WHERE id = :property_id AND user_id = :user_id");
            $stmtCheckOwnership->bindParam(':property_id', $propertyId);
            $stmtCheckOwnership->bindParam(':user_id', $userId);
            $stmtCheckOwnership->execute();
            $propertyCount = $stmtCheckOwnership->fetchColumn();

            if ($propertyCount > 0) {
                // Select main image name associated with the property
                $stmtMainImage = $conn->prepare("SELECT image_name FROM property WHERE id = :property_id");
                $stmtMainImage->bindParam(':property_id', $propertyId);
                $stmtMainImage->execute();
                $mainImageName = $stmtMainImage->fetchColumn();

                // Select additional image names associated with the property
                $stmtAdditionalImages = $conn->prepare("SELECT name FROM image WHERE property_id = :property_id");
                $stmtAdditionalImages->bindParam(':property_id', $propertyId);
                $stmtAdditionalImages->execute();
                $additionalImageNames = $stmtAdditionalImages->fetchAll(PDO::FETCH_COLUMN);

                // Delete additional images from the file system
                foreach ($additionalImageNames as $imageName) {
                    $imagePath = "../../store/property/images/" . $imageName;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // Delete related records from the 'images' table
                $stmtImages = $conn->prepare("DELETE FROM image WHERE property_id = :property_id");
                $stmtImages->bindParam(':property_id', $propertyId);
                $stmtImages->execute();

                // Delete related record from the 'structure' table
                $stmtStructure = $conn->prepare("DELETE FROM structure WHERE id = :property_id");
                $stmtStructure->bindParam(':property_id', $propertyId);
                $stmtStructure->execute();

                // Delete the property itself
                $stmtProperty = $conn->prepare("DELETE FROM property WHERE id = :id");
                $stmtProperty->bindParam(':id', $propertyId);
                $stmtProperty->execute();

                // Commit the transaction
                $conn->commit();

                // Delete main image from the file system
                if ($mainImageName) {
                    $mainImagePath = "../../store/property/main-images/" . $mainImageName;
                    if (file_exists($mainImagePath)) {
                        unlink($mainImagePath);
                    }
                }

                // Check if the property was successfully deleted
                if ($stmtProperty->rowCount() > 0) {
                    header("Location: /my-properties.php");
                } else {
                    echo "Property with ID $propertyId not found.";
                }
            } else {
                echo "You do not have permission to delete this property.";
            }
        } else {
            echo "Property ID or user ID not provided.";
        }

    } catch (PDOException $e) {
        // Roll back the transaction on error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $conn = null;
}
?>
