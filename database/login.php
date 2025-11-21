<?php
/**
 * User Login Handler
 * 
 * This file processes user login attempts and manages sessions
 */

// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = filter_input(INPUT_POST, 'login-email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['login-password'] ?? '';
    $remember_me = isset($_POST['remember-me']) ? true : false;
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        $response['errors'][] = "Email and password are required";
        $response['message'] = "Please enter both email and password";
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct, start session
                session_start();
                
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                
                // Update last login timestamp
                $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                // Create session record
                $session_token = bin2hex(random_bytes(32));
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $expires_at = date('Y-m-d H:i:s', strtotime('+2 hours'));
                
                $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user['user_id'], $session_token, $ip_address, $user_agent, $expires_at]);
                
                // If remember me is checked, create a persistent token
                if ($remember_me) {
                    $remember_token = bin2hex(random_bytes(32));
                    $token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                    
                    $stmt = $pdo->prepare("INSERT INTO remember_me_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$user['user_id'], $remember_token, $token_expires]);
                    
                    // Set cookie for remember token (30 days)
                    setcookie('remember_token', $remember_token, time() + (86400 * 30), '/', '', true, true);
                }
                
                $response['success'] = true;
                $response['message'] = "Login successful!";
                $response['redirect'] = "index.php"; // Redirect to home page after login
            } else {
                // Invalid credentials
                $response['message'] = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $response['message'] = "Login failed: " . $e->getMessage();
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);