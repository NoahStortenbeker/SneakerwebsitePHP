<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$payment_method_id = $data['payment_method_id'] ?? null;

if (!$payment_method_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
    exit();
}

try {
    // Check if payment method belongs to user
    $stmt = $pdo->prepare("SELECT user_id FROM payment_methods WHERE payment_method_id = ?");
    $stmt->execute([$payment_method_id]);
    $payment = $stmt->fetch();

    if (!$payment || $payment['user_id'] !== $_SESSION['user_id']) {
        throw new Exception('Invalid payment method');
    }

    // Delete payment method
    $stmt = $pdo->prepare("DELETE FROM payment_methods WHERE payment_method_id = ?");
    $stmt->execute([$payment_method_id]);

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}