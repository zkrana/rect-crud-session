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


// Fetch categories from the database
$sql = "SELECT * FROM categories";
$stmt = $connection->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get category name based on category ID
// Function to fetch category name based on category ID
function getCategoryName($categoryId) {
    global $connection; // Assuming $connection is your database connection variable

    $sql = "SELECT name FROM categories WHERE id = :category_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['name'] : 'Unknown Category';
}

// Function to count the total number of products
function countProducts() {
    global $connection;

    $sql = "SELECT COUNT(*) FROM products";
    $stmt = $connection->query($sql);
    return $stmt->fetchColumn();
}

// Fetch products from the database with pagination
$limit = 20; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number

// Calculate the offset for the SQL query
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM products LIMIT :limit OFFSET :offset";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to format the price
function formatPrice($price, $currencyCode) {
    // Customize this function based on your needs
    return $currencyCode . ' ' . number_format($price, 2);
}

// Function to format the price with currency icon
function formatPriceWithIcon($price, $currencyCode) {
    // Customize this function based on your needs
    $formattedPrice = formatPrice($price, $currencyCode);

    // Add currency icon based on the currency code
    switch ($currencyCode) {
        case 'BDT':
            $currencyIcon = '৳'; // Add the BDT icon
            break;
        case 'USD':
            $currencyIcon = '$'; // Add the USD icon
            break;
        // Add more cases for other currencies as needed
        default:
            $currencyIcon = ''; // Default to empty if no match
            break;
    }

    return $currencyIcon . ' ' . $formattedPrice;
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
                            <h1 class="page-heading"> Products </h1>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
                        </div>

                        <?php
                        // Check for success query parameter
                        if (isset($_GET['success'])) {
                            $successMsg = $_GET['success'];
                            echo '<div id="error" class="max-w-400px alert alert-success mt-2" role="alert">' . $successMsg . '</div>';
                        }

                        // Display error message if available
                        if (isset($_GET['error'])) {
                            $errorMsg = $_GET['error']; // You should set an appropriate error message here
                            echo '<div class="alert alert-danger" role="alert">' . $errorMsg . '</div>';
                        }
                        ?>


                        <!-- Existing Products -->
                        <div class="existing-products mt-5">
                            <h2>Existing Products</h2>
                            <?php
                                // Check for success parameter in the URL
                                if (isset($_GET["success"]) && $_GET["success"] == 1) {
                                    echo "<div id='error' class='alert alert-success'>Product added successfully.</div>";
                                } elseif (isset($_GET["error"]) && $_GET["error"] == 1) {
                                    echo "<div id='error' class='alert alert-danger'>Error adding product. Please try again.</div>";
                                }
                            ?>
                            <div class="main-prodict-d-wrapper mt-4">
                                <!-- Display the product cards using a loop -->
                                <?php foreach ($products as $product): ?>
                                    <div class="card">
                                        <div class="pro-d-photo">
                                             <img src="../auth/assets/products/<?php echo $product['product_photo']; ?>" alt="<?php echo isset($product['name']) ? $product['name'] : 'Unknown Product'; ?>" >
                                        </div>
                                        <h4 class="mb-0 product-dash-heading"><?php echo isset($product['name']) ? $product['name'] : 'Unknown Product'; ?></h4>
                                        <div class="card-body">
                                            <?php if (isset($product['price']) && isset($product['currency_code'])) : ?>
                                                <p><strong>Price:</strong> <?php echo formatPriceWithIcon($product['price'], $product['currency_code']); ?></p>
                                            <?php else : ?>
                                                <p><strong>Price:</strong> Not available</p>
                                            <?php endif; ?>

                                            <?php if (isset($product['category_id'])) : ?>
                                                <p><strong>Category:</strong> <?php echo getCategoryName($product['category_id']); ?></p>
                                            <?php else : ?>
                                                <p><strong>Category:</strong> Unknown Category</p>
                                            <?php endif; ?>

                                            <?php if (isset($product['stock_quantity'])) : ?>
                                                <p><strong>Stock Quantity:</strong> <?php echo $product['stock_quantity']; ?></p>
                                            <?php else : ?>
                                                <p><strong>Stock Quantity:</strong> Not available</p>
                                            <?php endif; ?>

                                            <?php if (isset($product['created_at'])) : ?>
                                                <p><strong>Created At:</strong> <?php echo $product['created_at']; ?></p>
                                            <?php else : ?>
                                                <p><strong>Created At:</strong> Not available</p>
                                            <?php endif; ?>

                                            <?php if (isset($product['updated_at'])) : ?>
                                                <p><strong>Updated At:</strong> <?php echo $product['updated_at']; ?></p>
                                            <?php else : ?>
                                                <p><strong>Updated At:</strong> Not available</p>
                                            <?php endif; ?>

                                            <div class="btn-group" role="group" aria-label="Product Actions">
                                                <a class="btn" href="edit_product.php?up_id=<?php echo isset($product['id']) ? $product['id'] : ''; ?>">
                                                <i class="fa-solid fa-pencil"></i> Edit</a> 
                                                <a class="btn" href="../auth/backend-assets/product/delete_product.php?del_id=<?php echo isset($product['id']) ? $product['id'] : ''; ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </a>


                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <nav aria-label="Page navigation example text-center">
                                <ul class="pagination">
                                    <?php
                                    // Previous page link
                                    $prevPage = $page - 1;
                                    echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $prevPage . '">Previous</a></li>';

                                    // Page links
                                    for ($i = 1; $i <= ceil(countProducts() / $limit); $i++) {
                                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                    }

                                    // Next page link
                                    $nextPage = $page + 1;
                                    echo '<li class="page-item ' . ($page >= ceil(countProducts() / $limit) ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $nextPage . '">Next</a></li>';
                                    ?>
                                </ul>
                            </nav>
                        </div>


                        <!-- Modal -->
                        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="productForm" action="../auth/backend-assets/product/add_product.php" method="post"
                                            enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="productName" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="productName" name="productName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="productDescription" class="form-label">Product Description</label>
                                                <input type="text" class="form-control" id="productDescription" name="productDescription"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="productPrice" class="form-label">Product Price</label>
                                                <div class="input-group">
                                                    <span id="currencySymbol" class="input-group-text"></span>
                                                    <input type="text" class="form-control" id="productPrice" name="productPrice" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="currency" class="form-label">Currency</label>
                                                <select class="form-select" id="currency" name="productCurrency" required>
                                                    <option value="BDT" selected>BDT (Bangladeshi Taka)</option>
                                                    <option value="USD">USD (United States Dollar)</option>
                                                    <!-- Add more currency options as needed -->
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="productCategory" class="form-label">Product Category</label>
                                                <!-- Display the product category dropdown -->
                                                <select class="form-select" id="productCategory" name="productCategory" required>
                                                    <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="productStock" class="form-label">Stock Quantity</label>
                                                <input type="text" class="form-control" id="productStock" name="productStock" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="productPhoto" class="form-label">Product Photo</label>
                                                <input type="file" class="form-control" id="productPhoto" name="productPhoto" accept="image/*"
                                                    required>
                                            </div>

                                            <!-- Variation Option -->
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="addVariation" name="addVariation">
                                                <label class="form-check-label" for="addVariation">Add Variation</label>
                                            </div>

                                            <!-- Variation Fields -->
                                            <div id="variationFields" style="display: none;">

                                            <!-- Sim Fields -->
                                            <div id="simFields">
                                                <div class="mb-3">
                                                    <label for="sim" class="form-label">sim</label>
                                                    <select id="sim" name="sim">
                                                        <option value="dual">Dual</option>
                                                        <option value="eSim">eSim</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Storage Fields -->
                                            <div id="storageFields">
                                                <div class="mb-3">
                                                    <label for="storage" class="form-label">storage</label>
                                                    <select id="storage" name="storage">
                                                        <option value="128mb">128 MB</option>
                                                        <option value="512mb">512 MB</option>
                                                        <option value="1BG">1 BG</option>
                                                        <option value="1TB">1 TB</option>
                                                    </select>
                                                </div>
                                            </div>


                                                <!-- Color Fields -->
                                                <div id="colorFields">
                                                    <div class="mb-3">
                                                        <label for="color" class="form-label">Color</label>
                                                        <input type="text" class="form-control" name="color[]" required>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-secondary mb-3" onclick="addColorField()">Add Color</button>

                                                <!-- Image Fields -->
                                                <div id="imageFields">
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Image</label>
                                                        <input type="file" class="form-control" name="image[]" accept="image/*" required>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-secondary mt-3" onclick="addImageField()">Add Image</button>


                                            </div>

                                            <button type="submit" class="btn btn-primary mt-4">Add Product</button>
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

        // Variation Product

      function addColorField() {
        var colorFields = document.getElementById('colorFields');
        var newColorField = document.createElement('div');
        newColorField.innerHTML = '<div class="mb-3"><label for="color" class="form-label">Color</label>' +
            '<input type="text" class="form-control" name="color[]" required></div>';
        colorFields.appendChild(newColorField);
    }

    function addImageField() {
        var imageFields = document.getElementById('imageFields');
        var newImageField = document.createElement('div');
        newImageField.innerHTML = '<div class="mb-3"><label for="image" class="form-label">Image</label>' +
            '<input type="file" class="form-control" name="image[]" accept="image/*" required></div>';
        imageFields.appendChild(newImageField);
    }

    document.getElementById('addVariation').addEventListener('change', function () {
        var variationFields = document.getElementById('variationFields');
        variationFields.style.display = this.checked ? 'block' : 'none';
    });
    </script>
    <script src="js/main.js"></script>
</body>
</html>
