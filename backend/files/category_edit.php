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


// Edit Category
require_once "../auth/db-connection/config.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['up_id'])) {
    $category_id = $_GET['up_id'];

    // Fetch category details based on the ID
    $sql = "SELECT * FROM categories WHERE id = :category_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        // Redirect or show an error message if the category is not found
        header("Location: category_list.php");
        exit();
    }
} else {
    // Redirect or show an error message if 'up_id' is not set
    header("Location: category_list.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                    <div class="wrapper-n-icon">
                        <i class="fa-solid fa-bars"></i>
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
                            <a href="">
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
                                    <li><a href="">Settings</a></li>
                                    <li><a href="../auth/backend-assets/logout.php" class="">Log out</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-container">
                    <div class="main">
                        <div class="flex">
                            <h1 class="page-heading">Edit Categories </h1>
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                        </div>

                        <!-- Existing Categories -->
                        <div class="mt-5">
                            <form method="post" action="../auth/backend-assets/category/update_category.php">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">

                                <div class="form-group">
                                    <label for="categoryName">Category Name:</label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo $category['name']; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="categoryDescription">Category Description:</label>
                                    <input type="text" class="form-control" id="categoryDescription" name="categoryDescription" value="<?php echo $category['category_description']; ?>">
                                </div>

                                <button type="submit" class="btn btn-primary">Update Category</button>
                            </form>
                        </div>

                        <!-- Categories -->
                        <!-- Modal -->
                        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Your form goes here -->
                                        <form id="categoryForm" action="../auth/backend-assets/category/add_category.php" method="post">
                                            <div class="mb-3">
                                                <label for="categoryName" class="form-label">Category Name</label>
                                                <input type="text" class="form-control" id="categoryName" name="categoryName" aria-describedby="emailHelp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="categoryDescription" class="form-label">Category Description</label>
                                                <input type="text" class="form-control" id="categoryDescription" name="categoryDescription">
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
    </script>
    <script src="js/add-category.js"></script>
</body>
</html>
