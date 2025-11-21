-- Drop tables if they exist
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

-- Categories table to organize products
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table to store product information
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Insert initial categories
INSERT INTO categories (name, slug) VALUES
('Sneakers', 'sneakers'),
('Apparel', 'apparel'),
('Accessories', 'accessories');

-- Insert sample products
INSERT INTO products (category_id, name, subtitle, price, image_path, stock_quantity) VALUES
(1, 'Jordan 1 Retro Low OG SP', 'Travis Scott Velvet Brown', 324.95, './src/Travisscottvelvetbrown.png', 10),
(3, 'by Parra Trees In The Wind Bag', 'Camo Green', 199.95, './src/by Parra Trees In The Wind Bag.png', 5),
(3, 'Virgil Abloh x IKEA "WET GRASS" Green Rug', 'Dimensions 195×132 cm.', 495.95, './src/Off White_WettGrass.png', 3),
(1, 'Jordan 1 Retro Low OG SP', 'Travis Scott reverse Mocha', 949.95, './src/travis_scott.png', 7);

-- Create product_sizes table
CREATE TABLE IF NOT EXISTS product_sizes (
    size_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert sample sizes for products
INSERT INTO product_sizes (product_id, size) VALUES
(1, 'US 7'), (1, 'US 8'), (1, 'US 9'), (1, 'US 10'),
(4, 'US 7'), (4, 'US 8'), (4, 'US 9'), (4, 'US 10');

-- Create product_images table
CREATE TABLE IF NOT EXISTS product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Insert sample product images
INSERT INTO product_images (product_id, image_path, is_primary) VALUES
(1, './src/Travisscottvelvetbrown.png', TRUE),
(2, './src/by Parra Trees In The Wind Bag.png', TRUE),
(3, './src/Off White_WettGrass.png', TRUE),
(4, './src/travis_scott.png', TRUE);

-- Create indexes for better performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_product_sizes ON product_sizes(product_id);
CREATE INDEX idx_product_images ON product_images(product_id);