<?php
// Include your database connection code here
require_once "../../db-connection/config.php";

// Function to upload product photo and return the file name
function uploadProductPhoto() {
    $uploadDir = "../../assets/products/";
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
    $productPhoto = uploadProductPhoto();

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
        $stmt->bindParam(":product_photo", $productPhoto, PDO::PARAM_STR);
        $stmt->bindParam(":currency_code", $productCurrency, PDO::PARAM_STR);

        // Add binding for currency_code
        $stmt->bindParam(":currency_code", $productCurrency, PDO::PARAM_STR);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Redirect back to the page where products are displayed with success message
            header("location: ../../../files/products.php?success=Product added successfully.");
            exit;
        } else {
            // Handle the error (you might want to display an error message)
            header("location: ../../../files/products.php?error=Error adding product: " . implode(" ", $stmt->errorInfo()));
            exit;
        }
    } else {
        // Handle the error (you might want to display an error message)
        header("location: ../../../files/products.php?error=Error uploading product photo.");
        exit;
    }
}

// Close connection
unset($connection);
?>
