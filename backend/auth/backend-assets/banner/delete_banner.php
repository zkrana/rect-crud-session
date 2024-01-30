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


// Check if the "id" parameter is present in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare a DELETE statement
    $sql = "DELETE FROM banner_photos WHERE id = :id";

    if ($stmt = $connection->prepare($sql)) {
        // Bind the parameter
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

        // Set the parameter value
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to the banner photos page after deletion
            header("location: ../../../files/appearance.php?success='Banner successfully deleted.'");
            exit();
        } else {
             // Redirect to the banner photos page after deletion
            header("location: ../../../files/appearance.php?error='Oops! Something went wrong. Please try again later.'");
            exit();
        }

        // Close the statement
        unset($stmt);
    }
}

// Close the database connection
unset($connection);
?>
