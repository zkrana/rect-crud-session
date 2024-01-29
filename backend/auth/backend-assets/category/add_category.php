<?php
// add_category.php
require_once "../../db-connection/config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data
    $categoryName = $_POST["categoryName"];
    $categoryDescription = $_POST["categoryDescription"];
    $parentCategoryId = isset($_POST["parentCategory"]) ? $_POST["parentCategory"] : null;

    try {
        // Determine the level based on the parent category
        $level = 0;

        if ($parentCategoryId !== null && $parentCategoryId !== "") {
            // Check if the parent category exists
            $sqlParent = "SELECT level FROM categories WHERE id = :parentId";
            $stmtParent = $connection->prepare($sqlParent);
            $stmtParent->bindParam(':parentId', $parentCategoryId, PDO::PARAM_INT);
            $stmtParent->execute();
            $parentLevel = $stmtParent->fetchColumn();

            if ($parentLevel !== false) {
                $level = $parentLevel + 1;
            } else {
                echo "Error: Parent category does not exist.";
                exit;
            }
        }

        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO categories (name, category_description, parent_category_id, level) VALUES (:name, :description, :parentCategoryId, :level)";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':name', $categoryName);
        $stmt->bindParam(':description', $categoryDescription);

        // Check if $parentCategoryId is null and set it to NULL in the database
        if ($parentCategoryId === "") {
            $parentCategoryId = null;
        }

        $stmt->bindParam(':parentCategoryId', $parentCategoryId, PDO::PARAM_INT);
        $stmt->bindParam(':level', $level, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect with success parameter
            header("Location: ../../../files/categories.php?success='Category added successfully!'");
            exit();
        } else {
            echo "Error adding category. Please try again.";
            var_dump($stmt->errorInfo());  // Display more detailed error information
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close connection
unset($connection);
?>
