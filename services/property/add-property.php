<?php
require '../../config/conn.php';
require '../../util/is-post.php';
session_start();

if (is_post()) {
    $conn = null;
    try {
        $name = $_POST["name"];
        $type_id = $_POST["type"];
        $price = $_POST["price"];
        $size = $_POST["size"];
        $description = $_POST["description"];
        $bedrooms = $_POST["bedrooms"];
        $bathrooms = $_POST["bathrooms"];
        $balconies = $_POST["balconies"];
        $city_id = $_POST["city_id"]; // Using city_id instead of country and city
        $address = $_POST["address"];
        $zip = $_POST["zip"];


        $fullDescription = $_POST["full_description"];

        $userId = $_SESSION['user']['id'];

        // Connect to your database
        $conn = $createConnection();

        // Start a transaction
        $conn->beginTransaction();

        // Handle file upload for main image
        $mainImageName = null;
        if (isset($_FILES['main-image'])) {
            $targetDirectory = "../../store/property/main-images/"; // Specify your folder path

            $extension = pathinfo($_FILES["main-image"]["name"], PATHINFO_EXTENSION);
            $mainImageName = $_SESSION['user']['id'] . "_" . generateRandomString() . "." . $extension;

            $targetFilePath = $targetDirectory . $mainImageName;
            if (move_uploaded_file($_FILES["main-image"]["tmp_name"], $targetFilePath)) {
                // File uploaded successfully
            } else {
                throw new Exception("Image uploading failed");
            }
        }

        // Handle file upload for additional images
        $additionalImageNames = [];
        if (isset($_FILES['additional-images'])) {
            $targetDirectory = "../../store/property/images/"; // Specify your folder path
            $additionalImages = $_FILES['additional-images'];
            foreach ($additionalImages['tmp_name'] as $key => $tmpName) {
                $extension = pathinfo($additionalImages["name"][$key], PATHINFO_EXTENSION);
                $imageName = $_SESSION['user']['id'] . "_" . generateRandomString() . "." . $extension;
                $targetFilePath = $targetDirectory . $imageName;
                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    $additionalImageNames[] = $imageName;
                } else {
                    throw new Exception("Image uploading failed");
                }
            }
        }

        // Prepare SQL statement to insert property into the database
        $sqlProperty = "INSERT INTO property (name, type_id, price, description, image_name, city_id, address, zip_code, size, user_id, full_description) VALUES (:name, :type_id, :price, :description, :image_name, :city_id, :address, :zip, :size, :user_id, :full_description)";
        $stmtProperty = $conn->prepare($sqlProperty);

        // Bind values to named placeholders
        $stmtProperty->bindParam(':name', $name);
        $stmtProperty->bindParam(':type_id', $type_id);
        $stmtProperty->bindParam(':price', $price);
        $stmtProperty->bindParam(':description', $description);
        $stmtProperty->bindParam(':image_name', $mainImageName); // Placeholder for the main image name
        $stmtProperty->bindParam(':city_id', $city_id); // Bind city_id instead of country and city
        $stmtProperty->bindParam(':address', $address);
        $stmtProperty->bindParam(':zip', $zip);
        $stmtProperty->bindParam(':size', $size);
        $stmtProperty->bindParam(':user_id', $userId);
        $stmtProperty->bindParam(':full_description', $fullDescription);

        // Execute the statement to insert property
        if ($stmtProperty->execute()) {
            $propertyId = $conn->lastInsertId(); // Get the last inserted property ID

            // Insert bedrooms, toilets, and balconies into a separate table
            $sqlStructure = "INSERT INTO structure (id, bedrooms, bathrooms, balconies) VALUES (:property_id, :bedrooms, :bathrooms, :balconies)";
            $stmtStructure = $conn->prepare($sqlStructure);

            // Bind values to named placeholders for property features
            $stmtStructure->bindParam(':property_id', $propertyId);
            $stmtStructure->bindParam(':bedrooms', $bedrooms);
            $stmtStructure->bindParam(':bathrooms', $bathrooms);
            $stmtStructure->bindParam(':balconies', $balconies);

            // Execute the statement to insert property features
            $stmtStructure->execute();

            // Insert additional image names into the database
            foreach ($additionalImageNames as $imageName) {
                $sqlAdditionalImage = "INSERT INTO image (property_id, name) VALUES (:property_id, :image_name)";
                $stmtAdditionalImage = $conn->prepare($sqlAdditionalImage);
                $stmtAdditionalImage->bindParam(':property_id', $propertyId);
                $stmtAdditionalImage->bindParam(':image_name', $imageName);
                $stmtAdditionalImage->execute();
            }

            // Commit the transaction
            $conn->commit();
        } else {
            echo "Error: " . $stmtProperty->errorInfo()[2]; // Fetch the error message
        }

    } catch (PDOException $e) {
        // Rollback the transaction and handle database errors
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

    $conn = null;

    // Redirect to my properties
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
