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

// Prepare and execute the SQL query to delete the product
$sqlDelete = "DELETE FROM products WHERE id = :productId";
$stmtDelete = $connection->prepare($sqlDelete);
$stmtDelete->bindParam(":productId", $productId, PDO::PARAM_INT);

try {
    $connection->beginTransaction();

    if ($stmtDelete->execute()) {
        // Product deleted successfully
        $connection->commit();
        header("Location: ../../../files/products.php?success=Product deleted successfully.");
        exit;
    } else {
        // Product deletion failed
        $connection->rollBack();
        header("Location: ../../../files/products.php?error=Oops! Something went wrong during product deletion. Please try again later.");
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
?>
