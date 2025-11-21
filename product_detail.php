<?php
session_start();

// Include database connection
require_once 'database/db_connection.php';

// Get product ID from query parameters
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$product_id) {
    header('Location: index.php');
    exit();
}

try {
    // Get product details
    $stmt = $pdo->prepare(
        "SELECT p.*, GROUP_CONCAT(ps.size) as sizes
         FROM products p
         LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
         WHERE p.product_id = ?
         GROUP BY p.product_id"
    );
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: index.php');
        exit();
    }

    // Get product images
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - ClassicsBasic</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/product_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
</head>
<body>
    <header>
        <div class="wrapper">
            <div class="logo">
                <a href="index.php">
                    <img src="./src/Logo.png" alt="ClassicsBasic Logo">
                    <h1><span>Classics</span>Basics</h1>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">home</a></li>
                    <li><a href="sneakers.php" class="active">sneaker</a></li>
                    <li><a href="apparel.php">apparel</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="icon-bar">
                <a href="#" class="search-toggle"><i class="ri-search-line"></i></a>
                <a href="#" class="cart-toggle"><i class="ri-shopping-cart-line"></i></a>
            </div>
        </div>
    </header>
    <div class="bar">
        <div class="wrapper">
            <div class="bar-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <a href="userpage.php"><p>Welcome, <span><?php echo htmlspecialchars($user['first_name']); ?>!</span></p></a>
                    </div>
                <?php else: ?>
                    <a href="signup.php"><p>Account</p></a>
                <?php endif; ?>
                <a href="wishlist.php">
                    <p>Wish list</p>
                </a>
            </div>
        </div>
    </div>
    <main class="product-detail">
        <div class="wrapper">
            <div class="product-content">
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="thumbnail-list">
                        <?php foreach ($images as $image): ?>
                            <div class="thumbnail">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Product thumbnail">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="product-info">
                    <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="product-sku">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                    <div class="product-price">
                        <span class="price">€<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    <div class="size-selection">
                        <h3>Select Size</h3>
                        <div class="size-grid">
                            <?php
                            $sizes = explode(',', $product['sizes']);
                            foreach ($sizes as $size): ?>
                                <button class="size-btn" data-size="<?php echo htmlspecialchars($size); ?>">
                                    <?php echo htmlspecialchars($size); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="product-actions">
                        <button class="btn add-to-cart">Add to Cart</button>
                        <span><i class="ri-heart-2-line"></i></span>
                    </div>
                    <div class="product-description">
                        <h3>Product Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    <div class="shipping-info">
                        <h3>Shipping Information</h3>
                        <p>Shipping within the Netherlands takes 1-3 business days, and within the rest of the EU, it takes 2-10 business days.</p>
                    </div>
                    <div class="return-policy">
                        <h3>Return Policy</h3>
                        <p>Returns are possible within 14 days policy. Please visit our contact page to arrange a return.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="wrapper">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="index.php">
                        <h2><span>Classics</span>Basics</h2>
                    </a>
                </div>
                <div class="footer-links">
                    <div class="quick-links">
                        <h3>Information</h3>
                        <ul>
                            <li><a href="#">Shipping Information</a></li>
                            <li><a href="#">Return & Exchange</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="shop-links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#">Sneakers<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Apparel<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Vintage<i class="ri-arrow-right-line"></i></a></li>
                        </ul>
                    </div>
                    <div class="connect">
                        <h3>Connect</h3>
                        <ul>
                            <li><a href="#">Instagram<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Twitter<i class="ri-arrow-right-line"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2024 ClassicsBasic. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="./scripts/product_detail.js"></script>
</body>
</html>