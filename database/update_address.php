<?php
/**
 * User Address Update Handler
 */

session_start();

// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to update your address';
    header('Location: ../index.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING);
    $huisnummer = filter_input(INPUT_POST, 'huisnummer', FILTER_SANITIZE_STRING);
    $toevoeging = filter_input(INPUT_POST, 'toevoeging', FILTER_SANITIZE_STRING);
    $straatnaam = filter_input(INPUT_POST, 'straatnaam', FILTER_SANITIZE_STRING);
    $plaats = filter_input(INPUT_POST, 'plaats', FILTER_SANITIZE_STRING);
    $provincie = filter_input(INPUT_POST, 'provincie', FILTER_SANITIZE_STRING);
    $land = filter_input(INPUT_POST, 'land', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($postcode) || empty($huisnummer) || empty($straatnaam) || empty($plaats) || empty($provincie) || empty($land)) {
        $response['errors'][] = 'All required fields must be filled';
    }
    
    // If no errors, update or insert address
    if (empty($response['errors'])) {
        try {
            // Check if user already has a default address
            $stmt = $pdo->prepare("SELECT address_id FROM addresses WHERE user_id = ? AND is_default = true");
            $stmt->execute([$_SESSION['user_id']]);
            $existing_address = $stmt->fetch();
            
            if ($existing_address) {
                // Update existing address
                $stmt = $pdo->prepare("UPDATE addresses SET postcode = ?, huisnummer = ?, toevoeging = ?, straatnaam = ?, plaats = ?, provincie = ?, land = ? WHERE address_id = ?");
                $success = $stmt->execute([$postcode, $huisnummer, $toevoeging, $straatnaam, $plaats, $provincie, $land, $existing_address['address_id']]);
            } else {
                // Insert new address
                $stmt = $pdo->prepare("INSERT INTO addresses (user_id, postcode, huisnummer, toevoeging, straatnaam, plaats, provincie, land, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, true)");
                $success = $stmt->execute([$_SESSION['user_id'], $postcode, $huisnummer, $toevoeging, $straatnaam, $plaats, $provincie, $land]);
            }
            
            if ($success) {
                $response['success'] = true;
                $response['message'] = 'Address updated successfully';
            } else {
                $response['message'] = 'Failed to update address';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Please correct the following errors:';
    }
}

// Set success/error message in session
if ($response['success']) {
    $_SESSION['success_message'] = $response['message'];
} else {
    $_SESSION['error_message'] = $response['message'] . '\n' . implode('\n', $response['errors']);
}

// Redirect back to profile page
header('Location: ../userpage.php');
exit();