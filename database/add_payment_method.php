<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Include database connection
require_once 'db_connection.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $card_type = filter_input(INPUT_POST, 'card_type', FILTER_SANITIZE_STRING);
    $card_number = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
    $card_expiry = filter_input(INPUT_POST, 'card_expiry', FILTER_SANITIZE_STRING);
    $card_cvv = filter_input(INPUT_POST, 'card_cvv', FILTER_SANITIZE_STRING);

    // Basic validation
    if (!$card_type || !$card_number || !$card_expiry || !$card_cvv) {
        $error = 'All fields are required';
    } else {
        // Format card number to get last 4 digits
        $card_last_four = substr($card_number, -4);
        
        try {
            // Insert payment method
            $stmt = $pdo->prepare(
                "INSERT INTO payment_methods (user_id, card_type, card_last_four, card_expiry) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $_SESSION['user_id'],
                $card_type,
                $card_last_four,
                $card_expiry
            ]);
            
            $success = 'Payment method added successfully';
            // Redirect to profile page after successful addition
            header('Location: userpage.php?success=payment_added');
            exit();
        } catch (PDOException $e) {
            $error = 'Error adding payment method. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment Method - ClassicsBasic</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/signup.css">
    <link rel="stylesheet" href="../css/alert.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    <link rel="stylesheet" href="../css/payment.css">
</head>
<body>
    <?php include '../header.php'; ?>
    <main class="add-payment-method">
        <div class="wrapper">
            <div class="form-container">
                <h1>Add Payment Method</h1>
                <?php if ($error): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="../database/add_payment_method.php" class="payment-form">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-options">
                            <div class="payment-option" data-type="credit_card">
                                <i class="ri-bank-card-line"></i>
                                <span>Credit Card</span>
                            </div>
                            <div class="payment-option" data-type="apple_pay">
                                <i class="ri-apple-fill"></i>
                                <span>Apple Pay</span>
                            </div>
                            <div class="payment-option" data-type="ideal">
                                <i class="ri-bank-line"></i>
                                <span>iDEAL</span>
                            </div>
                        </div>
                        <input type="hidden" name="payment_type" id="payment_type" required>
                    </div>

                    <div id="credit-card-fields" class="payment-fields">
                        <div class="form-group">
                            <label for="card_type">Card Type</label>
                            <select id="card_type" name="card_type">
                                <option value="">Select Card Type</option>
                                <option value="Visa">Visa</option>
                                <option value="MasterCard">MasterCard</option>
                                <option value="American Express">American Express</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" 
                                   pattern="[0-9]{16}" maxlength="16"
                                   placeholder="Enter your 16-digit card number">
                        </div>

                        <div class="form-group">
                            <label for="card_expiry">Expiry Date</label>
                            <input type="text" id="card_expiry" name="card_expiry" 
                                   pattern="(0[1-9]|1[0-2])/[0-9]{2}" maxlength="5"
                                   placeholder="MM/YY">
                        </div>

                        <div class="form-group">
                            <label for="card_cvv">CVV</label>
                            <input type="text" id="card_cvv" name="card_cvv" 
                                   pattern="[0-9]{3,4}" maxlength="4"
                                   placeholder="Enter CVV">
                        </div>
                    </div>

                    <div id="ideal-fields" class="payment-fields">
                        <div class="form-group">
                            <label for="ideal_bank">Select Bank</label>
                            <select id="ideal_bank" name="ideal_bank">
                                <option value="">Choose your bank</option>
                                <option value="ING">ING</option>
                                <option value="ABN AMRO">ABN AMRO</option>
                                <option value="Rabobank">Rabobank</option>
                                <option value="SNS">SNS</option>
                                <option value="ASN Bank">ASN Bank</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn primary">Add Payment Method</button>
                        <a href="../userpage.php" class="btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentOptions = document.querySelectorAll('.payment-option');
        const paymentTypeInput = document.getElementById('payment_type');
        const creditCardFields = document.getElementById('credit-card-fields');
        const idealFields = document.getElementById('ideal-fields');

        // Set credit card as default payment method
        const creditCardOption = document.querySelector('[data-type="credit_card"]');
        if (creditCardOption) {
            creditCardOption.classList.add('active');
            paymentTypeInput.value = 'credit_card';
            creditCardFields.style.display = 'block';
            idealFields.style.display = 'none';
        }

        // Add click handlers for payment options
        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                paymentOptions.forEach(opt => opt.classList.remove('active'));
                // Add active class to selected option
                this.classList.add('active');

                const paymentType = this.dataset.type;
                paymentTypeInput.value = paymentType;

                // Show/hide relevant fields based on payment type
                if (paymentType === 'credit_card') {
                    creditCardFields.style.display = 'block';
                    idealFields.style.display = 'none';
                } else if (paymentType === 'ideal') {
                    creditCardFields.style.display = 'none';
                    idealFields.style.display = 'block';
                } else {
                    creditCardFields.style.display = 'none';
                    idealFields.style.display = 'none';
                }

                // Ensure fields are properly visible
                creditCardFields.style.opacity = paymentType === 'credit_card' ? '1' : '0';
                idealFields.style.opacity = paymentType === 'ideal' ? '1' : '0';
                creditCardFields.style.height = paymentType === 'credit_card' ? 'auto' : '0';
                idealFields.style.height = paymentType === 'ideal' ? 'auto' : '0';}
            });
        });

        // Format expiry date input
        const expiryInput = document.getElementById('card_expiry');
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Format card number input
        const cardInput = document.getElementById('card_number');
        cardInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Format CVV input
        const cvvInput = document.getElementById('card_cvv');
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    });
    </script>
</body>
</html>