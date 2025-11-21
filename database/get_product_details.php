<?php
session_start();

// Include database connection
require_once 'db_connection.php';

// Get product ID from query parameters
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
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
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit();
    }

    // Get product images
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $product['images'] = $images;

    header('Content-Type: application/json');
    echo json_encode($product);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}