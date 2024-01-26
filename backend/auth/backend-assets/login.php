<?php
// Initialize the session
session_start();

// Check if the user is already logged in, redirect to dashboard if true
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../../files/dashboard.php");
    exit;
}

// Include config file
require_once "../db-connection/config.php";

// Check if connection is successful
if ($connection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM admin_users WHERE username = :username";

        if ($stmt = $connection->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                if ($stmt->rowCount() == 1) {
                    // Bind result variables
                    $stmt->bindColumn("id", $id);
                    $stmt->bindColumn("username", $username);
                    $stmt->bindColumn("password", $hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;

                        // Redirect user to dashboard
                        header("location: ../../files/dashboard.php");
                        exit;
                    } else {
                        // Password is not valid, display a generic error message
                        $login_err = "Invalid username or password.";
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Close connection
    unset($connection);
}
?>
