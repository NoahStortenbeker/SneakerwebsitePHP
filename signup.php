<?php
session_start();

// If user is already logged in, redirect to profile page
if (isset($_SESSION['user_id'])) {
    header('Location: userpage.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - ClassicsBasic</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/signup.css">
    <link rel="stylesheet" href="./css/alert.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
</head>
<body>
<header>
        <div class="wrapper">
            <div class="logo">
                <a href="index.php">
                    <img src="./src/Logo.png" alt="ClassicsBasic Logo">
                    <h1><span>Classics</span>Basics</h1>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>home</a></li>
                    <li><a href="sneakers.php">sneaker</a></li>
                    <li><a href="apparel.php">apparel</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="icon-bar">
                <a href="#" class="search-toggle"><i class="ri-search-line"></i></a>
                <a href="#" class="cart-toggle"><i class="ri-shopping-cart-line"></i></a>
                <div class="cart-overlay"></div>
                <div class="search-container">
                    <form class="search-form">
                        <input type="text" placeholder="Search for products..." class="search-input">
                        <button type="submit" class="search-button"><i class="ri-search-line"></i></button>
                        <button type="button" class="search-close"><i class="ri-close-line"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <div class="bar">
        <div class="wrapper">
            <div class="bar-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <a href="userpage.php">My Profile</a>
                        <a href="database/logout.php">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="userpage.php"><p>Account</p></a>
                <?php endif; ?>
                <a href="wishlist.php">
                    <p>Wish list</p>
                </a>
            </div>
        </div>
    </div>
    
    <main class="signup">
        <div class="wrapper">
            <div class="signup-content">
                <div class="signup-title">
                    <h1 id="form-title">Create Account</h1>
                </div>
                
                <!-- Signup Form Container -->
                <div class="signup-form-container">
                    <form id="signup-form" class="signup-form">
                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h2>Personal Information</h2>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first-name">First Name *</label>
                                    <input type="text" id="first-name" name="first-name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name *</label>
                                    <input type="text" id="last-name" name="last-name" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Section -->
                        <div class="form-section">
                            <h2>Address Information</h2>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="postcode">Postcode *</label>
                                    <input type="text" id="postcode" name="postcode" required>
                                </div>
                                <div class="form-group">
                                    <label for="huisnummer">House Number *</label>
                                    <input type="text" id="huisnummer" name="huisnummer" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="toevoeging">Addition</label>
                                <input type="text" id="toevoeging" name="toevoeging">
                            </div>
                            <div class="form-group">
                                <label for="straatnaam">Street Name *</label>
                                <input type="text" id="straatnaam" name="straatnaam" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="plaats">City *</label>
                                    <input type="text" id="plaats" name="plaats" required>
                                </div>
                                <div class="form-group">
                                    <label for="provincie">Province *</label>
                                    <input type="text" id="provincie" name="provincie" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="land">Country *</label>
                                <select id="land" name="land" required>
                                    <option value="">Select Country</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="BE">Belgium</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Account Section -->
                        <div class="form-section">
                            <h2>Account Details</h2>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input type="password" id="password" name="password" required>
                                    <p class="password-hint">Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.</p>
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password">Confirm Password *</label>
                                    <input type="password" id="confirm-password" name="confirm-password" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <div class="form-actions-container">
                                <button type="submit" class="signup-btn">Create Account</button>
                                <p class="login-link">Already have an account? <a href="#">Log in</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <!-- Scripts -->
    <script src="./scripts/signup.js"></script>
    <script src="./scripts/header.js"></script>
</body>
</html>