<?php
require_once "../../db-connection/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $categoryName = $_POST["categoryName"];
    $categoryDescription = $_POST["categoryDescription"];

    // Update category details in the database
    $sql = "UPDATE categories SET name = :categoryName, category_description = :categoryDescription WHERE id = :category_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":categoryName", $categoryName, PDO::PARAM_STR);
    $stmt->bindParam(":categoryDescription", $categoryDescription, PDO::PARAM_STR);
    $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect with success parameter
        header("Location: ../../../files/categories.php?success='Category updated successfully!'");
        exit();
    } else {
        echo "Error updating category. Please try again.";
    }
} else {
    // Redirect or show an error message if 'category_id' is not set
    header("Location: ../../../files/categories.php");
    exit();
}
?>
