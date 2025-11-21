<?php
/**
 * Order Details Handler
 */

session_start();

// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'order' => null,
    'items' => []
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to view order details';
    echo json_encode($response);
    exit();
}

// Get order ID from query parameters
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if (!$order_id) {
    $response['message'] = 'Invalid order ID';
    echo json_encode($response);
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
        $response['message'] = 'Order not found or access denied';
        echo json_encode($response);
        exit();
    }

    // Get order items
    $stmt = $pdo->prepare(
        "SELECT oi.*, p.name as product_name, p.image_path
         FROM order_items oi
         LEFT JOIN products p ON oi.product_id = p.product_id
         WHERE oi.order_id = ?"
    );
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['order'] = $order;
    $response['items'] = $items;
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);