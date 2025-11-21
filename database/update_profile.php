<?php
/**
 * User Profile Update Handler
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
    $response['message'] = 'You must be logged in to update your profile';
    header('Location: ../index.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $first_name = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
        $response['errors'][] = 'All fields are required';
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'Invalid email format';
    }
    
    // Check if email already exists (excluding current user)
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $response['errors'][] = 'Email already exists';
        }
    } catch (PDOException $e) {
        $response['errors'][] = 'Database error: ' . $e->getMessage();
    }
    
    // If no errors, update user information
    if (empty($response['errors'])) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ?");
            if ($stmt->execute([$first_name, $last_name, $email, $phone, $_SESSION['user_id']])) {
                // Update session variables
                $_SESSION['username'] = $first_name . ' ' . $last_name;
                $_SESSION['user_email'] = $email;
                
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully';
            } else {
                $response['message'] = 'Failed to update profile';
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