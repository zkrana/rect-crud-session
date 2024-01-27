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
if (!isset($_GET['up_id']) || empty($_GET['up_id'])) {
    header("location: ../../../files/products.php"); // Redirect to the products page if no ID is provided
    exit;
}

// Debugging: Output product ID
echo "Product ID to update: " . $_GET['up_id'];

// Fetch product details from the database based on the provided ID
$productId = $_GET['up_id'];
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

// Debugging: Output fetched product details
echo "Fetched Product Details: ";
var_dump($product);

// Fetch categories from the database
$sql = "SELECT * FROM categories";
$stmt = $connection->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get category name based on category ID
function getCategoryName($categoryId) {
    global $connection;
    $sql = "SELECT name FROM categories WHERE id = :category_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : 'Unknown Category';
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data

    // Debugging: Output submitted form data
    echo "Submitted Form Data: ";
    var_dump($_POST);

    // Retrieve form data
    $productName = $_POST["editProductName"];
    $productDescription = $_POST["editProductDescription"];
    $productPrice = $_POST["editProductPrice"];
    $currencyCode = $_POST["editProductCurrency"];
    $categoryId = $_POST["editProductCategory"];
    $stockQuantity = $_POST["editProductStock"];

    // Validate and sanitize the data as needed

    // Update the product in the database
    $sqlUpdate = "UPDATE products SET 
                    name = :productName,
                    description = :productDescription,
                    price = :productPrice,
                    currency_code = :currencyCode,
                    category_id = :categoryId,
                    stock_quantity = :stockQuantity
                    WHERE id = :productId";

    $stmtUpdate = $connection->prepare($sqlUpdate);
    $stmtUpdate->bindParam(":productName", $productName, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":productDescription", $productDescription, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":productPrice", $productPrice, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":currencyCode", $currencyCode, PDO::PARAM_STR);
    $stmtUpdate->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
    $stmtUpdate->bindParam(":stockQuantity", $stockQuantity, PDO::PARAM_INT);
    $stmtUpdate->bindParam(":productId", $productId, PDO::PARAM_INT);

    // Debugging: Output SQL query
    echo "SQL Query: " . $sqlUpdate;

    // Use transactions to ensure data consistency
    try {
        $connection->beginTransaction();

        if ($stmtUpdate->execute()) {
            // Product updated successfully
            $connection->commit();
            header("Location: ../../../files/products.php?success=Product updated successfully.");
            exit;
        } else {
            // Product update failed
            $connection->rollBack();
            header("Location: ../../../files/products.php?error=Oops! Something went wrong during product update. Please try again later.");
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
