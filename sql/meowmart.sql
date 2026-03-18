-- MeowMart Database
-- Run in Google Cloud SSH:
-- sudo mysql -u root -p < sql/meowmart.sql

CREATE DATABASE IF NOT EXISTS meowmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE meowmart;

-- Members table (matches original world_of_pets pattern + cat_name)
CREATE TABLE IF NOT EXISTS meowmart_members (
    id         INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    fname      VARCHAR(45)      NOT NULL,
    lname      VARCHAR(45)      NOT NULL,
    email      VARCHAR(45)      NOT NULL UNIQUE,
    cat_name   VARCHAR(80)      DEFAULT NULL,
    password   VARCHAR(255)     NOT NULL,
    role       VARCHAR(20)      NOT NULL DEFAULT 'member',
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200)   NOT NULL,
    category    VARCHAR(50)    NOT NULL,
    price       DECIMAL(8,2)   NOT NULL,
    description TEXT           DEFAULT NULL,
    is_featured TINYINT(1)     NOT NULL DEFAULT 0,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE IF NOT EXISTS blog_posts (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)   NOT NULL,
    tag        VARCHAR(60)    DEFAULT NULL,
    author     VARCHAR(120)   NOT NULL,
    excerpt    TEXT           DEFAULT NULL,
    content    LONGTEXT       NOT NULL,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- Demo products
INSERT INTO products (name, category, price, description, is_featured) VALUES
('Grain-Free Salmon Pâté for Adult Cats',  'Food',        14.90, 'Premium grain-free wet food packed with real salmon.', 1),
('Ultra Clumping Lavender Cat Litter 8kg', 'Litter',      22.50, 'Superior clumping formula with natural lavender scent.', 1),
('Interactive Feather Wand & Refill Set',  'Toys',         9.90, 'Entices natural hunting instincts with rustling feathers.', 1),
('Reversible Floral Bow Tie & Collar Set', 'Apparel',     12.00, 'Adjustable collar with removable reversible bow tie.', 1),
('Freeze-Dried Chicken Treats 100g',       'Food',        11.50, 'Single-ingredient freeze-dried chicken treats.', 0),
('Enclosed Self-Cleaning Litter Box',      'Litter',      89.00, 'Spacious enclosed litter box with carbon filter.', 0),
('Electronic Laser Chase Auto Toy',        'Toys',        34.90, 'Automatic rotating laser toy with 5 speed modes.', 0),
('Velvet Holiday Hoodie – Multiple Sizes', 'Apparel',     19.90, 'Cosy velvet hoodie perfect for festive photos.', 0),
('Multi-Level Cat Tree & Scratching Post', 'Accessories', 68.00, 'Sturdy sisal-wrapped posts with plush platforms.', 0),
('Stainless Steel Double-Bowl Feeder',     'Accessories', 22.00, 'Elevated anti-spill feeder with removable bowls.', 0);

-- Demo blog posts
INSERT INTO blog_posts (title, tag, author, excerpt, content) VALUES
('The Ultimate Guide to Feeding Your Cat a Balanced Diet', 'Nutrition', 'Dr. Lee Jun Wei',
 'From raw diets to premium kibble, everything you need to know.',
 'Cats are obligate carnivores. Always choose food with a named protein as the first ingredient. Feed 2 meals per day for adult cats and ensure fresh water is always available.'),
('10 Toys That Actually Keep Cats Entertained (Tested!)', 'Play', 'Priya N.',
 'Our team tested 30+ toys with real cats. Here are the winners.',
 'Feather wands, laser toys, and silver vine balls topped our list. Rotate toys weekly to prevent boredom. Even the best toy loses novelty quickly.'),
('How to Groom Your Cat at Home Without the Drama', 'Grooming', 'Mei Lin',
 'Step-by-step guide for nail trimming, brushing, and bathing.',
 'Start with short 2-3 minute sessions. Use proper cat nail clippers. Always reward with treats immediately after grooming to build a positive association.');

SELECT 'MeowMart database setup complete!' AS status;
