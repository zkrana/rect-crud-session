<?php
// Include config file
require_once "../auth/db-connection/config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../../index.php");
    exit;
}

// Check if product ID is provided in the URL
if (!isset($_GET['up_id']) || empty($_GET['up_id'])) {
    header("location: products.php"); // Redirect to the products page if no ID is provided
    exit;
}

// Fetch product details from the database based on the provided ID
$productId = $_GET['up_id'];
$sql = "SELECT * FROM products WHERE id = :productId";
$stmt = $connection->prepare($sql);
$stmt->bindParam(":productId", $productId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        // Redirect to the products page if the product is not found
        header("location: products.php");
        exit;
    }
} else {
    echo "Oops! Something went wrong. Please try again later.";
    exit;
}

// Fetch categories from the database
$sql = "SELECT * FROM categories";
$stmt = $connection->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get category name based on category ID
function getCategoryName($categoryId) {
    global $connection;
    $sql = "SELECT name FROM categories WHERE id = :category_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : 'Unknown Category';
}


// Function to format the price
function formatPrice($price, $currencyCode) {
    return $currencyCode . ' ' . number_format($price, 2);
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
                        
                        <li class="">
                            <a href="categories.php">
                                <i class="fa-solid fa-list"></i>
                                <span class="block">Categories</span>
                            </a>
                        </li>

                        <li class="active">
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
                        <h1 class="page-heading"> Edit Product </h1>
                    </div>

                    <!-- Display the product form for editing -->
                        <form id="editProductForm" action="../auth/backend-assets/product/update_product.php" method="post" enctype="multipart/form-data">

                        <!-- Hidden field for product ID -->
                        <input type="hidden" name="productId" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" name="editProductName" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                        </div>

                        <!-- Product Description -->
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Product Description</label>
                            <input type="text" class="form-control" id="editProductDescription" name="editProductDescription" value="<?php echo htmlspecialchars($product['description'] ?? ''); ?>" required>
                        </div>

                        <!-- Product Price -->
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Product Price</label>
                            <div class="input-group">
                                <span id="editCurrencySymbol" class="input-group-text"></span>
                                <input type="text" class="form-control" id="editProductPrice" name="editProductPrice" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Currency -->
                        <div class="mb-3">
                            <label for="editCurrency" class="form-label">Currency</label>
                            <select class="form-select" id="editCurrency" name="editProductCurrency" required>
                                <!-- Add currency options as needed -->
                                <option value="BDT" <?php echo ($product['currency_code'] == 'BDT') ? 'selected' : ''; ?>>BDT (Bangladeshi Taka)</option>
                                <option value="USD" <?php echo ($product['currency_code'] == 'USD') ? 'selected' : ''; ?>>USD (United States Dollar)</option>
                            </select>
                        </div>

                        <!-- Product Category -->
                        <div class="mb-3">
                            <label for="editProductCategory" class="form-label">Product Category</label>
                            <select class="form-select" id="editProductCategory" name="editProductCategory" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Stock Quantity -->
                        <div class="mb-3">
                            <label for="editProductStock" class="form-label">Stock Quantity</label>
                            <input type="text" class="form-control" id="editProductStock" name="editProductStock" value="<?php echo htmlspecialchars($product['stock_quantity'] ?? ''); ?>" required>
                        </div>

                        <!-- Product Photo -->
                        <div class="mb-3">
                            <label for="editProductPhoto" class="form-label">Product Photo</label>
                            <input type="file" class="form-control" id="editProductPhoto" name="editProductPhoto" accept="image/*">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

    <!-- Bootstrap JS (you can use the CDN or download the file and host it locally) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-rqzjrxH3CSuZ5Ff2LPz1Lh8Qf5OUd9lZAe73FN19SYBWy4MEEI2Ml4hj8Iva5Q8" crossorigin="anonymous"></script>

    <script>
        function toggleUserOptions() {
            var options = document.getElementById("userOptions");
            options.style.display = (options.style.display === 'flex') ? 'none' : 'flex';
        }

  function toggleUserOptions() {
        var options = document.getElementById("userOptions");
        options.style.display = (options.style.display === 'flex') ? 'none' : 'flex';
    }

    function updateCurrencySymbol() {
        console.log("Updating currency symbol...");
        var currencySelect = document.getElementById("editCurrency");
        var currencySymbol = document.getElementById("editCurrencySymbol");
        var selectedCurrency = currencySelect.value;

        console.log("Selected Currency:", selectedCurrency);

        // Update the currency symbol based on the selected currency
        // You can customize this part based on your currency symbols
        switch (selectedCurrency) {
            case "BDT":
                currencySymbol.textContent = "৳";
                break;
            case "USD":
                currencySymbol.textContent = "$";
                break;
            // Add more cases for other currencies as needed
            default:
                currencySymbol.textContent = "";
                break;
        }
    }

    // Attach the updateCurrencySymbol function to the change event of the currency select
    document.getElementById("editCurrency").addEventListener("change", updateCurrencySymbol);

    // Call the function initially to set the default currency symbol
    updateCurrencySymbol();

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
    <!-- <script src="js/main.js"></script> -->
</body>
</html>