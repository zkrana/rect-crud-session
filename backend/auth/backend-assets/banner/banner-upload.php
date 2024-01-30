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


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if a file is selected
    if (isset($_FILES["banner_photo"]) && $_FILES["banner_photo"]["error"] == UPLOAD_ERR_OK) {
        
        // Set your upload directory
        $uploadDir = "../../assets/banner/";
        
        // Create the uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate a unique name for the uploaded file
        $photoName = uniqid() . "_" . basename($_FILES["banner_photo"]["name"]);
        $uploadPath = $uploadDir . $photoName;
        
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES["banner_photo"]["tmp_name"], $uploadPath)) {
            
            try {
                $stmt = $connection->prepare("INSERT INTO banner_photos (photo_name) VALUES (:photoName)");
                $stmt->bindParam(':photoName', $photoName, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $successMessage = "File uploaded successfully and database record added.";
                    header("location: ../../../files/appearance.php?success=" . urlencode($successMessage));
                } else {
                    $errorMessage = "Error: " . print_r($stmt->errorInfo(), true);
                    header("location: ../../../files/appearance.php?error=" . urlencode($errorMessage));
                }

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            
            $stmt = null;
            $connection = null;
            
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file selected or file upload error.";
    }
}
?>
