<?php
/**
 * Password Reset Handler
 * 
 * This file handles password reset requests and token validation
 */

// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Request password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_reset') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Please provide a valid email address";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT user_id, first_name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Delete any existing tokens for this user
                $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                // Store new token
                $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['user_id'], $token, $expires_at]);
                
                // In a real application, you would send an email with the reset link
                // For this example, we'll just return the token in the response
                $reset_link = "reset_password.php?token=$token";
                
                $response['success'] = true;
                $response['message'] = "Password reset instructions have been sent to your email.";
                $response['debug_link'] = $reset_link; // Remove this in production
            } else {
                $response['errors'][] = "Invalid or expired token. Please request a new password reset link.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Reset failed: " . $e->getMessage();
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
              