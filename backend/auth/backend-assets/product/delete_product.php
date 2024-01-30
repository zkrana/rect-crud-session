<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config file
require_once "../../db-connection/config.php";

// Initialize the session
session_start();

// Debugging: Output session data
echo "Session Data: ";
var_dump($_SESSION);

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

// Debugging: Output a message when the user is logged in
echo "User is logged in. Continuing with the script.";

// Check if product ID is provided in the URL
if (!isset($_GET['del_id']) || empty($_GET['del_id'])) {
    header("location: ../../../files/products.php?error=Invalid product ID for deletion.");
    exit;
}

// Get the product ID from the URL
$productId = $_GET['del_id'];

// Debugging: Output product ID
echo "Product ID to delete: " . $productId;

// Fetch product details from the database based on the provided ID
$sqlGetProduct = "SELECT * FROM products WHERE id = :productId";
$stmtGetProduct = $connection->prepare($sqlGetProduct);
$stmtGetProduct->bindParam(":productId", $productId, PDO::PARAM_INT);

if ($stmtGetProduct->execute()) {
    $product = $stmtGetProduct->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        // Redirect to the products page if the product is not found
        header("location: ../../../files/products.php?error=Product not found for deletion.");
        exit;
    }
} else {
    // Redirect with an error message if there's an issue fetching product details
    header("location: ../../../files/products.php?error=Oops! Something went wrong fetching product details. Please try again later.");
    exit;
}

// Delete variations associated with the product
$sqlDeleteVariations = "DELETE FROM variations WHERE product_id = :productId";
$stmtDeleteVariations = $connection->prepare($sqlDeleteVariations);
$stmtDeleteVariations->bindParam(":productId", $productId, PDO::PARAM_INT);

// Prepare the statement for deleting the product
$sqlDeleteProduct = "DELETE FROM products WHERE id = :productId";
$stmtDeleteProduct = $connection->prepare($sqlDeleteProduct);
$stmtDeleteProduct->bindParam(":productId", $productId, PDO::PARAM_INT);

try {
    $connection->beginTransaction();

    // Delete variations first
    if ($stmtDeleteVariations->execute()) {
        // After variations are deleted, delete the product
        if ($stmtDeleteProduct->execute()) {
            // Product deleted successfully

            // Delete product photo file
            $targetDir = __DIR__ . "/../../assets/products/";
            $productPhotoPath = $targetDir . $product['product_photo'];

            if (!empty($product['product_photo']) && file_exists($productPhotoPath)) {
                unlink($productPhotoPath);
            }

            // Delete product folder
            $productFolderPath = $targetDir . $productId;

            if (is_dir($productFolderPath)) {
                // Recursively remove the product folder and its contents
                rrmdir($productFolderPath);
            }

            $connection->commit();
            header("Location: ../../../files/products.php?success=Product deleted successfully.");
            exit;
        } else {
            // Product deletion failed
            $connection->rollBack();
            header("Location: ../../../files/products.php?error=Oops! Something went wrong during product deletion. Please try again later.");
            exit;
        }
    } else {
        // Variations deletion failed
        $connection->rollBack();
        header("Location: ../../../files/products.php?error=Oops! Something went wrong during variations deletion. Please try again later.");
        exit;
    }
} catch (Exception $e) {
    // Handle exceptions
    $connection->rollBack();

    // Debugging: Output the exception message
    echo "Exception Message: " . $e->getMessage();

    header("Location: ../../../files/products.php?error=Error: " . $e->getMessage());
    exit;
}

// Helper function to recursively remove a directory and its contents
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
?>
