<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$input = $password = "";
$input_err = $password_err = $login_err = "";

// Set maximum number of login attempts, lockout time, and block time (in seconds)
$maxAttempts = 3;
$lockoutTime = 300; // 5 minutes
$blockTime = 3600; // 1 hour

// Set the threshold for blocking an IP address
$blockThreshold = 3;

// Check if the login attempt limit has been reached
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $maxAttempts) {
    // Block the IP address
    blockIpAddress($_SERVER['REMOTE_ADDR'], $blockTime);

    // Send error message to the login page
    header("location: ../../index.php?error=account_locked");
    exit;
}


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Increment the login attempt counter
    $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;

    // Implement a delay between login attempts (rate limiting)
    if (isset($_SESSION['last_login_attempt_time']) &&
        time() - $_SESSION['last_login_attempt_time'] < $lockoutTime) {
        // Send error message to the login page
        header("location: ../../index.php?error=rate_limited");
        exit;
    }

    // Set the timestamp of the last login attempt
    $_SESSION['last_login_attempt_time'] = time();

    // Check if input (username or email) is empty
    if (empty(trim($_POST["input"]))) {
        $input_err = "Please enter username or email.";
    } else {
        $input = trim($_POST["input"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($input_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM admin_users WHERE username = :input OR email = :input";

        if ($stmt = $connection->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":input", $param_input, PDO::PARAM_STR);

            // Set parameters
            $param_input = $input;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                if ($stmt->rowCount() == 1) {
                    // Bind result variables
                    $stmt->bindColumn("id", $id);
                    $stmt->bindColumn("username", $result_username);
                    $stmt->bindColumn("password", $hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $result_username;

                        // Reset login attempts
                        unset($_SESSION['login_attempts']);

                        // Log successful access
                        logAccess($_SERVER['REMOTE_ADDR']);

                        // Redirect user to dashboard
                        header("location: ../../files/dashboard.php");
                        exit;
                    } else {
                        // Password is not valid, display a generic error message
                        $login_err = "Invalid username or password.";

                        // Redirect user back to the login page with an error parameter
                        header("location: ../../index.php?error=1");
                        exit;
                    }
                } else {
                    // Username or email doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";

                    // Redirect user back to the login page with an error parameter
                    header("location: ../../index.php?error=1");
                    exit;
                }
            } else {
                // Debug: Print SQL error
                echo "SQL Error: " . implode(" ", $stmt->errorInfo());
            }

            // Close statement
            unset($stmt);
        }
    }
}

// Function to block an IP address and store it in the database
function blockIpAddress($ipAddress, $blockTime) {
    global $connection;

    // Check if the IP address is already blocked
    $checkQuery = "SELECT * FROM blocked_ips WHERE ip_address = ? AND blocked_until > NOW()";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->execute([$ipAddress]);
    $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$checkResult) {
        // IP address is not blocked, proceed to block
        $blockedUntil = date('Y-m-d H:i:s', strtotime("+ $blockTime seconds"));

        // Insert the blocked IP address into the database
        $insertQuery = "INSERT INTO blocked_ips (ip_address, blocked_until) VALUES (?, ?)";
        $insertStmt = $connection->prepare($insertQuery);
        $insertStmt->execute([$ipAddress, $blockedUntil]);
    }

    // Close statements
    $checkStmt = null;
    $insertStmt = null;
}


// Function to log successful access to the database
function logAccess($ipAddress) {
    global $connection;

    // Insert the access log into the database
    $insertQuery = "INSERT INTO access_logs (ip_address, access_time) VALUES (?, NOW())";
    $insertStmt = $connection->prepare($insertQuery);
    $insertStmt->execute([$ipAddress]);
    $insertStmt->closeCursor();
}
?>
