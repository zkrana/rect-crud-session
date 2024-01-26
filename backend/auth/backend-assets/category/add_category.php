<?php
// add_category.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Perform the database insertion
require_once "../../db-connection/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data
    $categoryName = $_POST["categoryName"];
    $categoryDescription = $_POST["categoryDescription"];

    try {
        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO categories (name, category_description) VALUES (:name, :description)";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':name', $categoryName);
        $stmt->bindParam(':description', $categoryDescription);

        if ($stmt->execute()) {
            // Redirect with success parameter
            header("Location: ../../../files/categories.php?success='Category added successfully!'");
            exit();
        } else {
            echo "Error adding category. Please try again.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
