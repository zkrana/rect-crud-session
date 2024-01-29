<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

// Include config file
require_once "../auth/db-connection/config.php";

// Fetch additional user information from the database using the user ID
$userId = $_SESSION["id"];
$sql = "SELECT profile_photo FROM admin_users WHERE id = :userId";

if ($stmt = $connection->prepare($sql)) {
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $stmt->bindColumn("profile_photo", $profilePhoto);
        if ($stmt->fetch()) {
            // User profile photo found, update the session
            $_SESSION["profile_photo"] = $profilePhoto;
        } else {
            // User not found or profile photo not set, you can handle this case
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    unset($stmt); // Close statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styling/style.css">
</head>
<body style="background:#f7f7f7;">
    <main>
        <div class="app-wrapper">
            <div class="app-sidebar">
                <div class="side-header flex pr-3">
                    <div class="logo flex">
                        <img src="images/logo.webp" alt="logo">
                    </div>
                    <div id="des-nav" class="wrapper-n-icon">
                        <i class="fa-solid fa-bars"></i>
                        <i class="fa-solid fa-xmark close"></i>
                    </div>
                </div>
                <div class="sidebard-nav">
                    <ul>
                        <li class="">
                            <a href="dashboard.php">
                                <i class="fa-solid fa-table-columns"></i>
                                <span class="block">Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="active">
                            <a href="categories.php">
                                <i class="fa-solid fa-list"></i>
                                <span class="block">Categories</span>
                            </a>
                        </li>

                        <li>
                            <a href="products.php">
                               <i class="fa-solid fa-cart-flatbed-suitcase"></i>
                                <span class="block">Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                  <i class="fa-solid fa-cart-shopping"></i>
                                <span class="block">Order</span>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-user-group"></i>
                                <span class="block">Customers</span>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-chart-simple"></i>
                                <span class="block">Statistics</span>
                            </a>
                        </li>

                        <li>
                            <a href="">
                                <i class="fa-solid fa-comments"></i>
                                <span class="block">Reviews</span>
                            </a>
                        </li>

                        <li>
                            <a href="">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                                <span class="block">Transanctions</span>
                            </a>
                        </li>

                        <li>
                            <a href="">
                                <i class="fa-solid fa-briefcase"></i>
                                <span class="block">Hot Offers</span>
                            </a>
                        </li>

                         <li class="devided-nav">
                            <a href="">
                                <i class="fa-solid fa-tag"></i>
                                <span class="block">Appearances</span>
                            </a>
                        </li>

                         <li>
                            <a href="">
                                <i class="fa-solid fa-gear"></i>
                                <span class="block">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="header-body">
                <div class="app-sidebar-mb">
                    <div class="nav-mb-icon">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                </div>
                <div class="user flex-end">
                    <div class="search">
                        <form class="d-flex gap-3" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="account">
                        <!-- Notifications -->
                        <div class="notifications">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <!-- User  -->
                        <div class="wrap-u" onclick="toggleUserOptions()">
                            <div class="user-pro flex">
                                <?php if (isset($_SESSION["profile_photo"])) : ?>
                                    <img src="<?php echo $_SESSION["profile_photo"]; ?>" alt="Profile Photo">
                                <?php else : ?>
                                    <!-- Provide a default image or alternative content -->
                                    <img src="default_profile_photo.jpg" alt="Default Profile Photo">
                                <?php endif; ?>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <!-- User Dropdown -->
                        <div id="userOptions" class="u-pro-options">
                            <div class="flex-col w-full">
                                <div class="u-name">
                                    <div class="user-pro flex">
                                        <?php if (isset($_SESSION["profile_photo"])) : ?>
                                            <img src="<?php echo $_SESSION["profile_photo"]; ?>" alt="Profile Photo">
                                        <?php else : ?>
                                            <!-- Provide a default image or alternative content -->
                                            <img src="default_profile_photo.jpg" alt="Default Profile Photo">
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-col">
                                        <span class="block"><?php echo strtoupper(htmlspecialchars($_SESSION["username"])); ?></span>
                                        <span class="block"> Super Admin</span>
                                    </div>
                                </div>

                                <ul class="pro-menu">
                                    <li><a href="">Profile</a></li>
                                    <li><a href="admin-settings.php">Admin Settings</a></li>
                                    <li><a href="../auth/backend-assets/logout.php" class="">Log out</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-container">
                    <div class="main">
                        <div class="flex">
                            <h1 class="page-heading"> Categories </h1>
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                        </div>

                        <?php
                            // Check for success query parameter
                            if (isset($_GET['success'])) {
                                echo '<div id="error" class="max-w-400px alert alert-success mt-2" role="alert">' . $_GET['success'] . '</div>';
                            }
                        ?>

<?php
// Fetch categories from the database
$sql = "SELECT c.id, c.name, c.category_description, COUNT(p.id) AS product_count,
       c.created_at, c.updated_at,
       COALESCE(parent.parent_category_id, 0) AS parent_category_id,
       COALESCE(parent.level, 0) AS level
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
LEFT JOIN (
    SELECT id, COALESCE(parent_category_id, 0) AS parent_category_id, 0 AS level
    FROM categories
    WHERE parent_category_id IS NULL
    UNION ALL
    SELECT c.id, c.parent_category_id, parent.level + 1
    FROM categories c
    JOIN categories parent ON c.parent_category_id = parent.id
) parent ON c.id = parent.id
GROUP BY c.id";

try {
    $stmt = $connection->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    exit; // Stop execution if there is an error
}

?>

<div class="existing-cat mt-5">
    <h2>Existing Categories</h2>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Category Name</th>
                <th scope="col">Description</th>
                <th scope="col">Product Count</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Function to display categories with icons
            function displayCategories($categories, $parentId = null)
            {
                foreach ($categories as $category) {
                    if ($category['parent_category_id'] == $parentId) {
                        echo '<tr class="level-' . $category['level'] . '">';
                        echo '<th scope="row">' . $category['id'] . '</th>';
                        echo '<td class="level-' . $category['level'] . '">';
                        
                        // Add a caret icon for sub-categories
                        if ($category['level'] > 0) {
                            echo '&#x21AA;';
                        }

                        echo str_repeat('&nbsp;&nbsp;&nbsp;', $category['level']) . htmlspecialchars($category['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($category['category_description']) . '</td>';
                        echo '<td>' . $category['product_count'] . '</td>';
                        echo '<td>' . htmlspecialchars($category['created_at']) . '</td>';
                        echo '<td>' . htmlspecialchars($category['updated_at']) . '</td>';
                        echo '<td>';
                        echo '<a class="btn btn-primary" href="category_edit.php?up_id=' . $category['id'] . '">Edit</a> | ';
                        echo '<a class="btn btn-danger" href="../auth/backend-assets/category/category_delete.php?del_id=' . $category['id'] . '">Delete</a>';
                        echo '</td>';
                        echo '</tr>';

                        // Recursively display child categories
                        displayCategories($categories, $category['id']);
                    }
                }
            }

            // Call the function to display categories, starting from the root (parent_category_id is NULL)
            if (!empty($categories)) {
                displayCategories($categories, null);
            } else {
                echo "<tr><td colspan='7'>No categories found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>





                        <!-- Modal -->
                        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="categoryForm" action="../auth/backend-assets/category/add_category.php" method="post">
                                            <div class="mb-3">
                                                <label for="categoryName" class="form-label">Category Name</label>
                                                <input type="text" class="form-control" id="categoryName" name="categoryName" aria-describedby="emailHelp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="categoryDescription" class="form-label">Category Description</label>
                                                <input type="text" class="form-control" id="categoryDescription" name="categoryDescription">
                                            </div>
                                            <div class="mb-3">
                                                <label for="parentCategory" class="form-label">Parent Category</label>
                                                <select class="form-select" id="parentCategory" name="parentCategory">
                                                    <option value="" selected>No Parent Category</option>
                                                    <?php
                                                    // Fetch all categories to populate the dropdown
                                                    $sql = "SELECT id, name FROM categories";
                                                    $stmt = $connection->prepare($sql);
                                                    $stmt->execute();
                                                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($categories as $category) {
                                                        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Category</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Bootstrap JS (you can use the CDN or download the file and host it locally) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleUserOptions() {
            var options = document.getElementById("userOptions");
            options.style.display = (options.style.display === 'flex') ? 'none' : 'flex';
        }
                document.addEventListener('DOMContentLoaded', function () {
            const wrapperIcon = document.querySelector('.app-sidebar-mb');
            const appWrapperS = document.querySelector('.app-wrapper');
            const deskNav =  document.getElementById("des-nav");

        wrapperIcon.addEventListener('click', function () {
                appWrapperS.classList.toggle('show-sidebar');
            });
        deskNav.addEventListener('click', function () {
                appWrapperS.classList.remove('show-sidebar');
            });
        });
    </script>
    <script src="js/main.js"></script>
</body>
</html>
