<?php
// Call API header
require_once '../../db-connection/cors.php';
// Include database connection config
require_once '../../db-connection/config.php';

// Fetch categories from the database
function fetchCategories($pdo) {
    $categories = array();

    $sql = "SELECT id, name, parent_category_id, level FROM categories";
    $stmt = $pdo->query($sql);

    if ($stmt !== false) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'parent_category_id' => $row['parent_category_id'],
                'level' => $row['level']
            );
        }
    }

    return $categories;
}

// Output the result as JSON
header('Content-Type: application/json');

try {
    // Use the previously established PDO connection
    $pdo = new PDO("mysql:host=" . $config['db_hostname'] . ";dbname=" . $config['db_name'], $config['db_username'], $config['db_password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch and output categories
    echo json_encode(['categories' => fetchCategories($pdo)]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
} finally {
    // Close the database connection
    $pdo = null;
}
?>
