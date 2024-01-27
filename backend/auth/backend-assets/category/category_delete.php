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


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["del_id"])) {
    $categoryId = $_GET["del_id"];

    // Prepare DELETE statement
    $sql = "DELETE FROM categories WHERE id = :id";
    $stmt = $connection->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(":id", $categoryId, PDO::PARAM_INT);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Redirect back to the categories page with success message
        header("location: ../../../files/categories.php?success=Category deleted successfully.");
        exit;
    } else {
        // Redirect back to the categories page with error message
        header("location: ../../../files/categories.php?error=Error deleting category: " . implode(" ", $stmt->errorInfo()));
        exit;
    }

    // Close statement
    unset($stmt);
}

// Close connection
unset($connection);
?>
