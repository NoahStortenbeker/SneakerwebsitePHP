<?php
/**
 * User Registration Handler
 * 
 * This file processes the user registration form data and inserts it into the database
 */

// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Function to format error messages
function formatErrors($errors) {
    if (count($errors) === 1) {
        return $errors[0];
    }
    return "<ul>" . implode('', array_map(function($error) {
        return "<li>$error</li>";
    }, $errors)) . "</ul>";
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $first_name = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Address information
    $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING);
    $huisnummer = filter_input(INPUT_POST, 'huisnummer', FILTER_SANITIZE_STRING);
    $toevoeging = filter_input(INPUT_POST, 'toevoeging', FILTER_SANITIZE_STRING);
    $straatnaam = filter_input(INPUT_POST, 'straatnaam', FILTER_SANITIZE_STRING);
    $plaats = filter_input(INPUT_POST, 'plaats', FILTER_SANITIZE_STRING);
    $provincie = filter_input(INPUT_POST, 'provincie', FILTER_SANITIZE_STRING);
    $land = filter_input(INPUT_POST, 'land', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    $required_fields = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'password' => $password,
        'confirm_password' => $confirm_password,
        'postcode' => $postcode,
        'huisnummer' => $huisnummer,
        'straatnaam' => $straatnaam,
        'plaats' => $plaats,
        'provincie' => $provincie,
        'land' => $land
    ];
    
    foreach ($required_fields as $field => $value) {
        if (empty($value)) {
            $response['errors'][] = "$field is required";
        }
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Invalid email format";
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $response['errors'][] = "Passwords do not match";
    }
    
    // Check password strength (at least 8 chars, includes number and special char)
    if (!preg_match('/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/', $password)) {
        $response['errors'][] = "Password must be at least 8 characters long and include a number and special character";
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $response['errors'][] = "Email already registered";
    }
    
    // If no errors, proceed with registration
    if (empty($response['errors'])) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user data
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, newsletter_subscription, terms_accepted) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash, $newsletter, $terms]);
            
            // Get the user ID
            $user_id = $pdo->lastInsertId();
            
            // Insert address data
            $stmt = $pdo->prepare("INSERT INTO addresses (user_id, postcode, huisnummer, toevoeging, straatnaam, plaats, provincie, land) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $postcode, $huisnummer, $toevoeging, $straatnaam, $plaats, $provincie, $land]);
            
            // Commit transaction
            $pdo->commit();
            
            // Set success response
            $response['success'] = true;
            $response['message'] = "Registration successful! You can now log in.";
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $response['message'] = "Registration failed: " . $e->getMessage();
        }
    } else {
        // Format error messages for display
        $response['message'] = formatErrors($response['errors']);
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);