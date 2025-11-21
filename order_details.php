<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include database connection
require_once 'database/db_connection.php';

// Get order ID from query parameters
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if (!$order_id) {
    header('Location: userpage.php');
    exit();
}

try {
    // Get order details with payment and shipping information
    $stmt = $pdo->prepare(
        "SELECT o.*, pm.card_type, pm.card_last_four, pm.card_expiry,
                a.postcode, a.huisnummer, a.toevoeging, a.straatnaam, a.plaats, a.provincie, a.land
         FROM orders o
         LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
         LEFT JOIN addresses a ON o.shipping_address_id = a.address_id
         WHERE o.order_id = ? AND o.user_id = ?"
    );
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: userpage.php');
        exit();
    }

    // Get order items
    $stmt = $pdo->prepare(
        "SELECT oi.*, p.name as product_name, p.image_url
         FROM order_items oi
         LEFT JOIN products p ON oi.product_id = p.product_id
         WHERE oi.order_id = ?"
    );
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - ClassicsBasic</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/signup.css">
    <link rel="stylesheet" href="./css/alert.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="order-details">
        <div class="wrapper">
            <div class="order-details-content">
                <div class="order-details-header">
                    <h1>Order <span>#<?php echo htmlspecialchars($order['order_id']); ?></span></h1>
                    <a href="userpage.php" class="btn back-btn"><i class="ri-arrow-left-line"></i> Back to Profile</a>
                </div>

                <div class="order-sections">
                    <!-- Order Status Section -->
                    <section class="order-section">
                        <h2>Order Status</h2>
                        <div class="status-info">
                            <p class="status <?php echo strtolower($order['order_status']); ?>">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </p>
                            <p class="order-date">Ordered on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <?php if ($order['tracking_number']): ?>
                                <p class="tracking">Tracking Number: <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Order Items Section -->
                    <section class="order-section">
                        <h2>Order Items</h2>
                        <div class="order-items">
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <?php if ($item['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <?php endif; ?>
                                    <div class="item-details">
                                        <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                        <p>Price: €<?php echo number_format($item['price_per_unit'], 2); ?></p>
                                        <p>Subtotal: €<?php echo number_format($item['subtotal'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="order-total">
                                <p>Total: €<?php echo number_format($order['order_total'], 2); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Information Section -->
                    <section class="order-section">
                        <h2>Payment Information</h2>
                        <div class="payment-info">
                            <?php if ($order['card_type']): ?>
                                <p><?php echo htmlspecialchars($order['card_type']); ?> ending in <?php echo htmlspecialchars($order['card_last_four']); ?></p>
                                <p>Expires: <?php echo htmlspecialchars($order['card_expiry']); ?></p>
                            <?php else: ?>
                                <p>Payment information not available</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Shipping Address Section -->
                    <section class="order-section">
                        <h2>Shipping Address</h2>
                        <div class="shipping-info">
                            <p><?php echo htmlspecialchars($order['straatnaam'] . ' ' . $order['huisnummer'] . ($order['toevoeging'] ? ' ' . $order['toevoeging'] : '')); ?></p>
                            <p><?php echo htmlspecialchars($order['postcode']); ?></p>
                            <p><?php echo htmlspecialchars($order['plaats']); ?></p>
                            <p><?php echo htmlspecialchars($order['provincie']); ?></p>
                            <p><?php echo htmlspecialchars($order['land']); ?></p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>