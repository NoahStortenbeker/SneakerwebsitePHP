-- Drop tables if they exist
DROP TABLE IF EXISTS payment_methods;

-- Payment methods table to store user payment information
CREATE TABLE IF NOT EXISTS payment_methods (
    payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_type ENUM('credit_card', 'apple_pay', 'ideal') NOT NULL,
    card_type VARCHAR(50),
    card_last_four VARCHAR(4),
    card_expiry VARCHAR(5),
    ideal_bank VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create index for better performance
CREATE INDEX idx_payment_methods_user_id ON payment_methods(user_id);