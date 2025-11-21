<?php
session_start();

// Include database connection and fetch user data if logged in
if (isset($_SESSION['user_id'])) {
    require_once 'database/db_connection.php';
    
    try {
        $stmt = $pdo->prepare("SELECT first_name FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        // Handle error silently
        $user = ['first_name' => ''];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClassicsBasic</title>

    <!-- css -->
    <link rel="stylesheet" href="./css/home.css">

    <!-- link icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css"
        integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- scripts -->

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
                        <a href="userpage.php"><p>Welcome, <span><?php echo htmlspecialchars($user['first_name']); ?>!</span></p></a>
                    </div>
                <?php else: ?>
                    <a href="signup.php"><p>Account</p></a>
                <?php endif; ?>
                <a href="wishlist.php">
                    <p>Wish list</p>
                </a>
            </div>
        </div>
    </div>

    <main class="home">
        <div class="wrapper">
            <div class="home-content">
                <div class="home-text">
                    <p>Welcome</p>
                    <h1><span>Classics</span>Basics</h1>
                    <p>Your trusted Sneakerplug. Take a look around!</p>
                    <button class="btn">
                        <a href="">Start Shopping</a>
                    </button>
                </div>
                <img src="./src/TS_SP.png" alt="">
                <div class="logo-dot">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </main>

    <section class="recommended">
        <div class="wrapper-products">
            <div class="recommended-content">
                <h1>Recommended<span>.</span></h1>
                <div class="product-cards-container">
                    <div class="product-card">
                        <span><i class="ri-heart-2-line"></i></span>
                        <a href="product_detail.php?id=1"><img src="./src/Travisscottvelvetbrown.png" alt="Jordan 1 Retro Low OG SP"></a>
                        <h3>Jordan 1 Retro Low OG SP</h3>
                        <p class="subtitle">Travis Scott Velvet Brown</p>
                        <div class="price-action">
                            <p class="price">€324,95</p>
                            <a href="#" class="buy-now">Buy Now</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <span><i class="ri-heart-2-line"></i></span>
                        <a href="product_detail.php?id=2"><img src="./src/by Parra Trees In The Wind Bag.png" alt="Jordan 1 Retro Low OG SP"></a>
                        <h3>by Parra Trees In The Wind Bag</h3>
                        <p class="subtitle">Camo Green</p>
                        <div class="price-action">
                            <p class="price">€199,95</p>
                            <a href="#" class="buy-now">Buy Now</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <span><i class="ri-heart-2-line"></i></span>
                        <a href="product_detail.php?id=3"><img src="./src/Off White_WettGrass.png" alt="Jordan 1 Retro Low OG SP"></a>
                        <h3>Virgil Abloh x IKEA “WET GRASS” Green Rug</h3>
                        <p class="subtitle">Dimensions 195×132 cm.</p>
                        <div class="price-action">
                            <p class="price">€495,95</p>
                            <a href="#" class="buy-now">Buy Now</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <span><i class="ri-heart-2-line"></i></span>
                        <a href="product_detail.php?id=4"><img src="./src/travis_scott.png" alt="Jordan 1 Retro Low OG SP"></a>
                        <h3>Jordan 1 Retro Low OG SP</h3>
                        <p class="subtitle">Travis Scott reverse Mocha</p>
                        <div class="price-action">
                            <p class="price">€949,95</p>
                            <a href="#" class="buy-now">Buy Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer>
        <div class="wrapper">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="index.html">
                        <h2><span>Classics</span>Basics</h2>
                    </a>
                </div>
                <div class="footer-links">
                    <div class="quick-links">
                        <h3>Information</h3>
                        <ul>
                            <li><a href="#">Shipping Information</a></li>
                            <li><a href="#">Return & Exchange</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="shop-links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#">Sneakers<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Apparel<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Vintage<i class="ri-arrow-right-line"></i></a></li>
                        </ul>
                    </div>
                    <div class="connect">
                        <h3>Connect</h3>
                        <ul>
                            <li><a href="#">Instagram<i class="ri-arrow-right-line"></i></a></li>
                            <li><a href="#">Twitter<i class="ri-arrow-right-line"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2024 ClassicsBasic. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script defer src="./scripts/search.js"></script>
    <script defer src="./scripts/cart.js"></script>
    <script defer src="./scripts/header.js"></script>
</body>

</html>