<?php
require '../../config/conn.php';
require '../../util/is-post.php';
session_start();

if (is_post()) {
    $conn = null;
    try {
        $propertyId = $_POST["property_id"];
        $name = $_POST["name"];
        $type_id = $_POST["type"];
        $price = $_POST["price"];
        $size = $_POST["size"];
        $address = $_POST["address"];
        $zip = $_POST["zip"];
        $description = $_POST["description"];
        $bedrooms = $_POST["bedrooms"];
        $bathrooms = $_POST["bathrooms"];
        $balconies = $_POST["balconies"];
        $city_id = $_POST["city_id"];

        $fullDescription = $_POST["full_description"];

        // Connect to your database
        $conn = $createConnection();

        // Start a transaction
        $conn->beginTransaction();

        // Update main image if a new one is provided
        if (isset($_FILES['main-image']) && $_FILES['main-image']['size'] > 0) {
            // Delete old main image from database and file system
            $sqlSelectOldMainImage = "SELECT image_name FROM property WHERE id = :property_id";
            $stmtSelectOldMainImage = $conn->prepare($sqlSelectOldMainImage);
            $stmtSelectOldMainImage->bindParam(':property_id', $propertyId);
            $stmtSelectOldMainImage->execute();
            $oldMainImageName = $stmtSelectOldMainImage->fetchColumn();
            if ($oldMainImageName) {
                unlink("../../store/property/main-images/" . $oldMainImageName);
                // Delete old main image record from database
                $sqlDeleteOldMainImage = "UPDATE property SET image_name = NULL WHERE id = :property_id";
                $stmtDeleteOldMainImage = $conn->prepare($sqlDeleteOldMainImage);
                $stmtDeleteOldMainImage->bindParam(':property_id', $propertyId);
                $stmtDeleteOldMainImage->execute();
            }

            // Upload and update new main image
            $targetDirectory = "../../store/property/main-images/";
            $extension = pathinfo($_FILES["main-image"]["name"], PATHINFO_EXTENSION);
            $mainImageName = $_SESSION['user']['id'] . "_" . generateRandomString() . "." . $extension;

            $targetFilePath = $targetDirectory . $mainImageName;
            if (!move_uploaded_file($_FILES["main-image"]["tmp_name"], $targetFilePath)) {
                throw new Exception("Main image uploading failed");
            }

            // Update the database with the new main image name
            $sqlUpdateMainImage = "UPDATE property SET image_name = :image_name WHERE id = :property_id";
            $stmtUpdateMainImage = $conn->prepare($sqlUpdateMainImage);
            $stmtUpdateMainImage->bindParam(':image_name', $mainImageName);
            $stmtUpdateMainImage->bindParam(':property_id', $propertyId);
            $stmtUpdateMainImage->execute();
        }

        // Handle file upload for additional images
        if (isset($_FILES['additional-images'])) {
            $targetDirectory = "../../store/property/images/";

            // Delete old additional images from database and file system
            $sqlSelectOldAdditionalImages = "SELECT name FROM image WHERE property_id = :property_id";
            $stmtSelectOldAdditionalImages = $conn->prepare($sqlSelectOldAdditionalImages);
            $stmtSelectOldAdditionalImages->bindParam(':property_id', $propertyId);
            $stmtSelectOldAdditionalImages->execute();
            $oldAdditionalImages = $stmtSelectOldAdditionalImages->fetchAll(PDO::FETCH_COLUMN);

            foreach ($oldAdditionalImages as $oldImageName) {
                unlink($targetDirectory . $oldImageName);
                // Delete old additional image record from database
                $sqlDeleteOldAdditionalImage = "DELETE FROM image WHERE name = :image_name";
                $stmtDeleteOldAdditionalImage = $conn->prepare($sqlDeleteOldAdditionalImage);
                $stmtDeleteOldAdditionalImage->bindParam(':image_name', $oldImageName);
                $stmtDeleteOldAdditionalImage->execute();
            }

            // Upload new additional images
            $additionalImages = $_FILES['additional-images'];
            foreach ($additionalImages['tmp_name'] as $key => $tmpName) {
                $extension = pathinfo($additionalImages["name"][$key], PATHINFO_EXTENSION);
                $imageName = $_SESSION['user']['id'] . "_" . generateRandomString() . "." . $extension;

                $targetFilePath = $targetDirectory . $imageName;
                if (!move_uploaded_file($tmpName, $targetFilePath)) {
                    throw new Exception("Additional image uploading failed");
                }

                // Insert the new additional image into the database
                $sqlInsertAdditionalImage = "INSERT INTO image (property_id, name) VALUES (:property_id, :image_name)";
                $stmtInsertAdditionalImage = $conn->prepare($sqlInsertAdditionalImage);
                $stmtInsertAdditionalImage->bindParam(':property_id', $propertyId);
                $stmtInsertAdditionalImage->bindParam(':image_name', $imageName);
                $stmtInsertAdditionalImage->execute();
            }
        }

        // Update property details
        $sqlUpdateProperty = "UPDATE property SET name = :name, type_id = :type_id, price = :price, description = :description, city_id = :city_id, address = :address, zip_code = :zip, size = :size, full_description = :full_description WHERE id = :property_id";
        $stmtUpdateProperty = $conn->prepare($sqlUpdateProperty);
        $stmtUpdateProperty->bindParam(':name', $name);
        $stmtUpdateProperty->bindParam(':type_id', $type_id);
        $stmtUpdateProperty->bindParam(':price', $price);
        $stmtUpdateProperty->bindParam(':description', $description);
        $stmtUpdateProperty->bindParam(':city_id', $city_id);
        $stmtUpdateProperty->bindParam(':address', $address);
        $stmtUpdateProperty->bindParam(':zip', $zip);
        $stmtUpdateProperty->bindParam(':size', $size);
        $stmtUpdateProperty->bindParam(':property_id', $propertyId);
        $stmtUpdateProperty->bindParam(':full_description', $fullDescription);
        $stmtUpdateProperty->execute();

        // Update property structure details
        $sqlUpdateStructure = "UPDATE structure SET bedrooms = :bedrooms, bathrooms = :bathrooms, balconies = :balconies WHERE id = :property_id";
        $stmtUpdateStructure = $conn->prepare($sqlUpdateStructure);
        $stmtUpdateStructure->bindParam(':bedrooms', $bedrooms);
        $stmtUpdateStructure->bindParam(':bathrooms', $bathrooms);
        $stmtUpdateStructure->bindParam(':balconies', $balconies);
        $stmtUpdateStructure->bindParam(':property_id', $propertyId);
        $stmtUpdateStructure->execute();

        // Commit the transaction
        $conn->commit();

    } catch (PDOException $e) {
        // Rollback the transaction and handle database errors
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

    $conn = null;

    //Redirect to my properties
    header("Location: ../../my-properties.php");
}

// Function to generate a random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>
