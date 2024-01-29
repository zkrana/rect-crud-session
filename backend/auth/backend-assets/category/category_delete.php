<?php
// Include your database connection code here
require_once "../../db-connection/config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

// Function to delete category and its subcategories recursively
function deleteCategoryAndSubcategories($categoryId, $connection)
{
    // Fetch subcategories
    $sqlSubcategories = "SELECT id FROM categories WHERE parent_category_id = :id";
    $stmtSubcategories = $connection->prepare($sqlSubcategories);
    $stmtSubcategories->bindParam(':id', $categoryId, PDO::PARAM_INT);
    $stmtSubcategories->execute();
    $subcategories = $stmtSubcategories->fetchAll(PDO::FETCH_COLUMN);

    // Recursively delete subcategories
    foreach ($subcategories as $subcategory) {
        deleteCategoryAndSubcategories($subcategory, $connection);
    }

    // Delete the current category
    $sqlDeleteCategory = "DELETE FROM categories WHERE id = :id";
    $stmtDeleteCategory = $connection->prepare($sqlDeleteCategory);
    $stmtDeleteCategory->bindParam(':id', $categoryId, PDO::PARAM_INT);
    $stmtDeleteCategory->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["del_id"])) {
    $categoryId = $_GET["del_id"];

    try {
        // Start a transaction to ensure the consistency of the database
        $connection->beginTransaction();

        // Delete category and its subcategories
        deleteCategoryAndSubcategories($categoryId, $connection);

        // Commit the transaction if all operations are successful
        $connection->commit();

        // Redirect back to the categories page with success message
        header("location: ../../../files/categories.php?success=Category deleted successfully.");
        exit;
    } catch (PDOException $e) {
        // Roll back the transaction in case of an error
        $connection->rollBack();

        // Redirect back to the categories page with error message
        header("location: ../../../files/categories.php?error=Error deleting category: " . $e->getMessage());
        exit;
    }
}

// Close connection
unset($connection);
?>
