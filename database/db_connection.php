<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the ClassicsBasic database
 */

// Database configuration
$db_host = 'localhost';      // Database host
$db_name = 'PROGRAM2_100546'; // Database name
$db_user = 'db100546';          // Database username - change in production
$db_pass = 'Lego.2024';              // Database password - change in production

// Create connection using PDO
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    
    // Connection successful
    // echo "Database connection established successfully";
} catch (PDOException $e) {
    // Connection failed
    die("Database connection failed: " . $e->getMessage());
}