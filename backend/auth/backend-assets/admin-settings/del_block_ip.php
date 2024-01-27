<?php
// Include your database connection code here
require_once "../../db-connection/config.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $ipId = $_GET["id"];

    // Prepare DELETE statement
    $sql = "DELETE FROM blocked_ips WHERE id = :id";
    $stmt = $connection->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(":id", $ipId, PDO::PARAM_INT);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Redirect back to the page where blocked IPs are displayed with a success message
        header("location: ../../../files/admin-settings.php?success=1");
        exit;
    } else {
        // Log the error
        error_log("Error deleting blocked IP: " . implode(" ", $stmt->errorInfo()));
       // Redirect back to the page where blocked IPs are displayed with a generic error message
        header("location: ../../../files/admin-settings.php?error=1");
        exit;
    }

    // Close statement
    unset($stmt);
}

// Close connection
unset($connection);
?>
