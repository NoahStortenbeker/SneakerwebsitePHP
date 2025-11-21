<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include database connection
require_once 'database/db_connection.php';

// Get user information
try {
    $stmt = $pdo->prepare("SELECT u.*, a.* FROM users u LEFT JOIN addresses a ON u.user_id = a.user_id AND a.is_default = true WHERE u.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Get order history
    $stmt = $pdo->prepare("SELECT o.*, pm.card_type, pm.card_last_four FROM orders o LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id WHERE o.user_id = ? ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();

    // Get payment methods
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $payment_methods = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ClassicsBasic</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/signup.css">
    <link rel="stylesheet" href="./css/alert.css">
    <link rel="stylesheet" href="./css/userpage.css">
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
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>home</a></li>
                    <li><a href="sneakers.php">sneaker</a></li>
                    <li><a href="apparel.php">apparel</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="icon-bar">
                <a href="#" class="search-toggle"><i class="ri-search-line"></i></a>
                <a href="#" class="cart-toggle"><i class="ri-shopping-cart-line"></i></a>
                <div class="cart-overlay"></div>
                <div class="search-container">
                    <form class="search-form">
                        <input type="text" placeholder="Search for products..." class="search-input">
                        <button type="submit" class="search-button"><i class="ri-search-line"></i></button>
                        <button type="button" class="search-close"><i class="ri-close-line"></i></button>
                    </form>
                </div>
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
    <main class="profile">
        <div class="wrapper">
            <div class="profile-content">
                <div class="profile-title">
                    <h1>Welcome, <span><?php echo htmlspecialchars($user['first_name']); ?>!</span></h1>
                    <a href="database/logout.php" class="btn logout-btn"><p>Logout</p></a>
                </div>
                <div class="profile-menu">
                    <ul>
                        <li><a href="#personal-info" class="active">Personal Information</a></li>
                        <li><a href="#address">Default Address</a></li>
                        <li><a href="#payment">Payment Methods</a></li>
                        <li><a href="#orders">Order History</a></li>
                    </ul>
                </div>
                <div class="profile-sections">
                    <!-- Personal Information Section -->
                    <section class="profile-section">
                        <h2>Personal Information</h2>
                        <form id="profile-form" action="database/update_profile.php" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" id="first-name" name="first-name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" id="last-name" name="last-name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn update-btn">Update Information</button>
                        </form>
                    </section>

                    <!-- Address Section -->
                    <section class="profile-section">
                        <h2>Default Address</h2>
                        <form id="address-form" action="database/update_address.php" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="postcode">Postcode</label>
                                    <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($user['postcode'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="huisnummer">Huisnummer</label>
                                    <input type="text" id="huisnummer" name="huisnummer" value="<?php echo htmlspecialchars($user['huisnummer'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="toevoeging">Toevoeging</label>
                                    <input type="text" id="toevoeging" name="toevoeging" value="<?php echo htmlspecialchars($user['toevoeging'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="straatnaam">Straatnaam</label>
                                    <input type="text" id="straatnaam" name="straatnaam" value="<?php echo htmlspecialchars($user['straatnaam'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="plaats">Plaats</label>
                                    <input type="text" id="plaats" name="plaats" value="<?php echo htmlspecialchars($user['plaats'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="provincie">Provincie</label>
                                    <input type="text" id="provincie" name="provincie" value="<?php echo htmlspecialchars($user['provincie'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="land">Land</label>
                                    <select id="land" name="land" required>
                                        <?php
                                        $countries = ['netherlands' => 'Netherlands', 'belgium' => 'Belgium', 'germany' => 'Germany', 'france' => 'France', 'uk' => 'United Kingdom', 'other' => 'Other'];
                                        foreach ($countries as $value => $label) {
                                            $selected = ($user['land'] ?? '') === $value ? 'selected' : '';
                                            echo "<option value=\"$value\" $selected>$label</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn update-btn">Update Address</button>
                        </form>
                    </section>

                    <!-- Payment Methods Section -->
                    <section class="profile-section">
                        <h2>Payment Methods</h2>
                        <div class="payment-methods">
                            <?php if (empty($payment_methods)): ?>
                                <p>No payment methods saved.</p>
                            <?php else: ?>
                                <?php foreach ($payment_methods as $method): ?>
                                    <div class="payment-method">
                                        <div class="payment-info">
                                            <p><?php echo htmlspecialchars($method['card_type']); ?> ending in <?php echo htmlspecialchars($method['card_last_four']); ?></p>
                                            <p>Expires: <?php echo htmlspecialchars($method['card_expiry']); ?></p>
                                        </div>
                                        <?php if ($method['is_default']): ?>
                                            <span class="default-badge">Default</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <button class="btn add-payment-btn">Add New Payment Method</button>
                        </div>
                    </section>

                    <!-- Order History Section -->
                    <section class="profile-section">
                        <h2>Order History</h2>
                        <div class="order-history">
                            <?php if (empty($orders)): ?>
                                <p>No orders found.</p>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <div class="order-item">
                                        <div class="order-header">
                                            <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                                            <span class="order-status <?php echo strtolower($order['order_status']); ?>">
                                                <?php echo htmlspecialchars($order['order_status']); ?>
                                            </span>
                                        </div>
                                        <div class="order-details">
                                            <p>Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                            <p>Total: €<?php echo number_format($order['order_total'], 2); ?></p>
                                            <?php if ($order['tracking_number']): ?>
                                                <p>Tracking: <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn view-order-btn" data-order-id="<?php echo $order['order_id']; ?>">View Details</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="wrapper">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="index.html">
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
    <script src="./scripts/profile.js"></script>
</body>
</html>