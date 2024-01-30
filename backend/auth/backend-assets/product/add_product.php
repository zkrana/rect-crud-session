<?php
// Include your database connection code here
require_once "../../db-connection/config.php";

// Function to upload product photo and return the file name
function uploadProductPhoto($uploadDir, $productId) {
    $uploadedFile = $uploadDir . basename($_FILES["productPhoto"]["name"]);

    // Check if the file already exists
    if (file_exists($uploadedFile)) {
        return false; // You may handle the error in your way
    }

    // Try to move the uploaded file to the destination directory
    if (move_uploaded_file($_FILES["productPhoto"]["tmp_name"], $uploadedFile)) {
        return basename($_FILES["productPhoto"]["name"]);
    } else {
        return false; // You may handle the error in your way
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $productName = $_POST["productName"];
    $productDescription = $_POST["productDescription"];
    $productPrice = $_POST["productPrice"];
    $productCategory = $_POST["productCategory"];
    $productStock = $_POST["productStock"];
    $productCurrency = $_POST["productCurrency"]; // Assuming you have a form field for selecting currency

    // Upload product photo
    $productId = null;
    $productPhoto = uploadProductPhoto("../../assets/products/", $productId);

    if ($productPhoto) {
        // Prepare INSERT statement
        $sql = "INSERT INTO products (name, description, price, category_id, stock_quantity, product_photo, currency_code) 
                VALUES (:name, :description, :price, :category_id, :stock_quantity, :product_photo, :currency_code)";
        $stmt = $connection->prepare($sql);

        // Bind parameters
        $stmt->bindParam(":name", $productName, PDO::PARAM_STR);
        $stmt->bindParam(":description", $productDescription, PDO::PARAM_STR);
        $stmt->bindParam(":price", $productPrice, PDO::PARAM_STR);
        $stmt->bindParam(":category_id", $productCategory, PDO::PARAM_INT);
        $stmt->bindParam(":stock_quantity", $productStock, PDO::PARAM_INT);
        $stmt->bindValue(":product_photo", $productPhoto, PDO::PARAM_STR);
        $stmt->bindParam(":currency_code", $productCurrency, PDO::PARAM_STR);

        // Execute the prepared statement
        if ($stmt->execute()) {
            $productId = $connection->lastInsertId();

            // Create the product folder based on product ID
            $productFolder = "../../assets/products/" . $productId . "/";
            echo "Product Folder: $productFolder\n"; // Log product folder path

            if (!file_exists($productFolder)) {
                mkdir($productFolder, 0777, true);  // Recursive directory creation
            }

            // Move the main image to the product folder
            $mainImage = $productFolder . basename($productPhoto);
            echo "Main Image Path: $mainImage\n"; // Log main image path
            $isMainImageMoved = move_uploaded_file($_FILES["productPhoto"]["tmp_name"], $mainImage);

            if (!$isMainImageMoved) {
                echo "Failed to move the main image\n";
                echo "Upload error: " . $_FILES["productPhoto"]["error"] . "\n";
            }

            // Handle variations if the user checks for variation
            if (isset($_POST["addVariation"])) {
                // Sim, Storage, Color, and Image Fields
                $simValues = isset($_POST["sim"]) ? $_POST["sim"] : [];
                $storageValues = isset($_POST["storage"]) ? $_POST["storage"] : [];
                $colorValues = isset($_POST["color"]) ? $_POST["color"] : [];
                $imageValues = isset($_FILES["image"]["name"]) ? $_FILES["image"] : [];

                // Iterate through variations and create folders
                for ($i = 0; $i < count($colorValues); $i++) {
                    $variationFolder = $productFolder . "variation_" . ($i + 1) . "/";
                    mkdir($variationFolder);

                    // Move images to the variation folder if provided
                    if (!empty($imageValues["name"][$i]) && $imageValues["error"][$i] == UPLOAD_ERR_OK) {
                        $variationImage = $variationFolder . basename($imageValues["name"][$i]);
                        move_uploaded_file($imageValues["tmp_name"][$i], $variationImage);
                    } else {
                        $variationImage = null; // Set to null if no image provided
                    }

                    // Handle database insertion for variations
                    $sqlVariation = "INSERT INTO variations (product_id, sim, storage, color, image_path) 
                                    VALUES (:product_id, :sim, :storage, :color, :image_path)";
                    $stmtVariation = $connection->prepare($sqlVariation);
                    $stmtVariation->bindParam(":product_id", $productId, PDO::PARAM_INT);
                    $simValue = isset($simValues[$i]) && $simValues[$i] !== '' ? $simValues[$i] : null;
                    $storageValue = isset($storageValues[$i]) && $storageValues[$i] !== '' ? $storageValues[$i] : null;
                    $colorValue = $colorValues[$i] ?? null;
                    $imagePathValue = $variationImage;

                    // Check if user selected the "Select" option and set to null in that case
                    if ($simValue === '' || $simValue === 'Select') {
                        $simValue = null;
                    }

                    if ($storageValue === '' || $storageValue === 'Select') {
                        $storageValue = null;
                    }

                    // Bind parameters
                    $stmtVariation->bindParam(":sim", $simValue, PDO::PARAM_STR);
                    $stmtVariation->bindParam(":storage", $storageValue, PDO::PARAM_STR);
                    $stmtVariation->bindParam(":color", $colorValue, PDO::PARAM_STR);
                    $stmtVariation->bindParam(":image_path", $imagePathValue, PDO::PARAM_STR);

                    // Execute the prepared statement for variations
                    $stmtVariation->execute();
                }
            }

            // Redirect back to the page where products are displayed with success message
            $successMessage = "Product added successfully.";
            if ($isMainImageMoved) {
                $successMessage .= " Main Image Moved: Yes";
                $successMessage .= " Product Folder: $productFolder";
            } else {
                $successMessage .= " Main Image Moved: No";
            }
            header("location: ../../../files/products.php?success=" . urlencode($successMessage));
            exit;

        } else {
            // Handle the error (you might want to display an error message)
            $errorMessage = "Error adding product: " . implode(" ", $stmt->errorInfo());
            $errorMessage .= " Product Folder: $productFolder";
            header("location: ../../../files/products.php?error=" . urlencode($errorMessage));
            exit;
        }
    } else {
        // Handle the error (you might want to display an error message)
        header("location: ../../../files/products.php?error=Error uploading product photo: " . $_FILES["productPhoto"]["error"]);
        exit;
    }
}

// Close connection
unset($connection);
?>
