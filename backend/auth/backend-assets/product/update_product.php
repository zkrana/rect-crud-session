<?php
// Include config file
require_once "../../db-connection/config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

// Check if product ID is provided in the URL
if (!isset($_POST['productId']) || empty($_POST['productId'])) {
    header("location: ../../../files/products.php"); // Redirect to the products page if no ID is provided
    exit;
}

// Fetch product details from the database based on the provided ID
$productId = $_POST['productId'];
$sql = "SELECT * FROM products WHERE id = :productId";
$stmt = $connection->prepare($sql);
$stmt->bindParam(":productId", $productId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        // Redirect to the products page if the product is not found
        header("location: ../../../files/products.php");
        exit;
    }
} else {
    header("location: ../../../files/products.php?error=Oops! Something went wrong fetching product details. Please try again later.");
    exit;
}

// Process form data only if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data for the product
    $productName = $_POST["editProductName"];
    $productDescription = $_POST["editProductDescription"];
    $productPrice = $_POST["editProductPrice"];
    $currencyCode = $_POST["editProductCurrency"];
    $categoryId = $_POST["editProductCategory"];
    $stockQuantity = $_POST["editProductStock"];

    // Handle file upload for product photo
    $newProductPhoto = $product['product_photo']; // Default to the existing photo
    if ($_FILES['editProductPhoto']['error'] == 0) {
        $targetDir = __DIR__ . "/../../assets/products/";
        $targetFile = $targetDir . basename($_FILES['editProductPhoto']['name']);

        // Check if the file exists in the temporary location
        if (!file_exists($_FILES['editProductPhoto']['tmp_name']) || !is_uploaded_file($_FILES['editProductPhoto']['tmp_name'])) {
            header("Location: ../../../files/products.php?error=File not received properly.");
            exit;
        }

        // Check file size (adjust as needed)
        if ($_FILES['editProductPhoto']['size'] > 500000) {
            header("Location: ../../../files/products.php?error=File is too large.");
            exit;
        }

        // Allow certain file formats (you can add more as needed)
        $allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $allowedFormats)) {
            header("Location: ../../../files/products.php?error=Invalid file format. Allowed formats: jpg, jpeg, png, gif");
            exit;
        }

        // Delete the old image file
        if (!empty($product['product_photo'])) {
            $oldImagePath = $targetDir . $product['product_photo'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['editProductPhoto']['tmp_name'], $targetFile)) {
            $newProductPhoto = basename($_FILES['editProductPhoto']['name']);
        } else {
            header("Location: ../../../files/products.php?error=Error uploading file: Unable to move file.");
            exit;
        }
    }

    // Validate and sanitize the data as needed

    // Update the product in the database, including the photo
    $sqlUpdate = "UPDATE products SET 
                    name = :productName,
                    description = :productDescription,
                    price = :productPrice,
                    currency_code = :currencyCode,
                    category_id = :categoryId,
                    stock_quantity = :stockQuantity,
                    product_photo = :newProductPhoto
                    WHERE id = :productId";

    $stmtUpdate = $connection->prepare($sqlUpdate);
    $stmtUpdate->bindParam(":productName", $productName, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":productDescription", $productDescription, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":productPrice", $productPrice, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":currencyCode", $currencyCode, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
    $stmtUpdate->bindParam(":stockQuantity", $stockQuantity, PDO::PARAM_INT);
    $stmtUpdate->bindParam(":newProductPhoto", $newProductPhoto, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":productId", $productId, PDO::PARAM_INT);

    // Use transactions to ensure data consistency
    try {
        $connection->beginTransaction();

        if ($stmtUpdate->execute()) {
            // Product updated successfully

            // Handle variations
            $variations = [];
            $simArray = $_POST['sim'] ?? [];
            $storageArray = $_POST['storage'] ?? [];
            $colorsArray = $_POST['colors'] ?? [];
            $imagesArray = $_POST['images'] ?? [];

            // Iterate over the variation arrays to construct the $variations array
            foreach ($simArray as $key => $sim) {
                $storage = $storageArray[$key] ?? '';
                $colors = $colorsArray[$key] ?? [];
                $images = $imagesArray[$key] ?? [];

                // Create an associative array for each variation
                $variation = [
                    'sim' => $sim,
                    'storage' => $storage,
                    'colors' => $colors,
                    'images' => $images,
                ];

                // Add the variation to the $variations array
                $variations[] = $variation;
            }

            // Now, $variations is an array containing all the variations data

            // Update variations in the database
            $sqlUpdateVariations = "UPDATE variations SET 
                                        sim = :sim,
                                        storage = :storage,
                                        color = :color,
                                        image_path = :image_path
                                        WHERE product_id = :productId AND id = :variationId";

            $stmtUpdateVariations = $connection->prepare($sqlUpdateVariations);

            // Iterate over variations and update each one
            foreach ($variations as $key => $variation) {
                $sim = $variation['sim'];
                $storage = $variation['storage'];
                $colors = implode(',', $variation['colors']);
                $images = implode(',', $variation['images']);
                $variationId = $key + 1; // Adjust this based on how your variation IDs are structured

                $stmtUpdateVariations->bindParam(":sim", $sim, PDO::PARAM_STR);
                $stmtUpdateVariations->bindParam(":storage", $storage, PDO::PARAM_STR);
                $stmtUpdateVariations->bindParam(":color", $colors, PDO::PARAM_STR);
                $stmtUpdateVariations->bindParam(":image_path", $images, PDO::PARAM_STR);
                $stmtUpdateVariations->bindParam(":productId", $productId, PDO::PARAM_INT);
                $stmtUpdateVariations->bindParam(":variationId", $variationId, PDO::PARAM_INT);

                // Execute the update query for each variation
                $stmtUpdateVariations->execute();
            }

            // Commit the transaction
            $connection->commit();

            // Redirect or output success message as needed
            header("Location: ../../../files/products.php?success=Product and variations updated successfully.");
            exit;
        } else {
            // Product update failed
            $connection->rollBack();
            header("Location: ../../../files/products.php?error=Failed to update product. Please try again later.");
            exit;
        }
    } catch (Exception $e) {
        // Handle exceptions
        $connection->rollBack();
        header("Location: ../../../files/products.php?error=Error: " . $e->getMessage());
        exit;
    }
}
?>
