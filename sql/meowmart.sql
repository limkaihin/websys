DROP DATABASE IF EXISTS meowmart;
CREATE DATABASE meowmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE meowmart;

-- ─── USERS ──────────────────────────────────────────────────────────────────
CREATE TABLE users (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)     NOT NULL,
    email       VARCHAR(180)     NOT NULL UNIQUE,
    cat_name    VARCHAR(80)      DEFAULT NULL,
    password    VARCHAR(255)     NOT NULL,
    role        ENUM('member','admin') NOT NULL DEFAULT 'member',
    phone       VARCHAR(30)      DEFAULT NULL,
    address       VARCHAR(255)     DEFAULT NULL,
    referred_by   VARCHAR(50)      DEFAULT NULL,
    wishlist_json LONGTEXT         DEFAULT NULL,
    cart_json     LONGTEXT         DEFAULT NULL,
    created_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

*products*
CREATE TABLE products (
    id          INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200)   NOT NULL,
    category    VARCHAR(50)    NOT NULL,
    price       DECIMAL(8,2)   NOT NULL,
    description TEXT           DEFAULT NULL,
    is_featured TINYINT(1)     NOT NULL DEFAULT 0,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

*blog_posts*
CREATE TABLE blog_posts (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)   NOT NULL,
    tag        VARCHAR(60)    DEFAULT NULL,
    author     VARCHAR(120)   NOT NULL,
    excerpt    TEXT           DEFAULT NULL,
    content    LONGTEXT       NOT NULL,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

*orders*
CREATE TABLE orders (
    id                INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED   DEFAULT NULL,
    name              VARCHAR(120)   NOT NULL,
    email             VARCHAR(180)   NOT NULL,
    address           TEXT           NOT NULL,
    payment           VARCHAR(30)    NOT NULL DEFAULT 'card',
    payment_reference VARCHAR(120)   DEFAULT NULL,
    coupon_code       VARCHAR(50)    DEFAULT NULL,
    referral_code     VARCHAR(50)    DEFAULT NULL,
    discount          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    total             DECIMAL(10,2)  NOT NULL,
    status            ENUM('confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'confirmed',
    created_at        TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

*order_items*
CREATE TABLE order_items (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    order_id   INT UNSIGNED   NOT NULL,
    product_id INT UNSIGNED   DEFAULT NULL,
    name       VARCHAR(200)   NOT NULL,
    price      DECIMAL(8,2)   NOT NULL,
    qty        INT UNSIGNED   NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);
*contact_messages*
CREATE TABLE contact_messages (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120)   NOT NULL,
    email      VARCHAR(180)   NOT NULL,
    subject    VARCHAR(255)   NOT NULL,
    message    TEXT           NOT NULL,
    is_read    TINYINT(1)     NOT NULL DEFAULT 0,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);


*products that can be used*
INSERT INTO products (name, category, price, description, is_featured) VALUES
('Grain-Free Salmon Pate for Adult Cats',   'Food',        14.90, 'Premium grain-free wet food packed with real salmon. Rich in omega-3, gentle on digestion. 400g per can.', 1),
('Ultra Clumping Lavender Cat Litter 8kg',  'Litter',      22.50, 'Superior clumping formula with natural lavender scent. Low dust, easy to scoop, long-lasting freshness.', 1),
('Interactive Feather Wand & Refill Set',   'Toys',         9.90, 'Entices natural hunting instincts with rustling feathers. Includes 3 refill attachments. Safe for all ages.', 1),
('Reversible Floral Bow Tie & Collar Set',  'Apparel',     12.00, 'Adjustable collar with a removable reversible bow tie. Soft velvet fabric, secure buckle, breakaway safety clasp.', 1),
('Freeze-Dried Chicken Treats 100g',        'Food',        11.50, 'Single-ingredient freeze-dried chicken treats. No additives, preservatives, or fillers. Irresistible to cats.', 0),
('Enclosed Self-Cleaning Litter Box',       'Litter',      89.00, 'Spacious enclosed litter box with a carbon filter and easy-pull drawer for effortless cleaning.', 0),
('Electronic Laser Chase Auto Toy',         'Toys',        34.90, 'Automatic rotating laser toy with 5 speed modes and auto-off timer. Keeps cats entertained for hours.', 0),
('Velvet Holiday Hoodie - Multiple Sizes',  'Apparel',     19.90, 'Cosy velvet hoodie perfect for festive photos. Available in XS to L. Washable and skin-safe material.', 0),
('Gourmet Tuna & Prawn Mousse 12-pack',     'Food',        28.80, 'Silky smooth mousse with real tuna and prawn pieces. High moisture content supports urinary health.', 0),
('Natural Tofu Cat Litter 6L',              'Litter',      16.50, 'Plant-based tofu litter - flushable, biodegradable, and ultra-absorbent. Odour-neutral formula.', 0),
('Multi-Level Cat Tree & Scratching Post',  'Accessories', 68.00, 'Sturdy sisal-wrapped posts with plush platforms, a dangling toy, and a cosy enclosed nest.', 0),
('Stainless Steel Double-Bowl Feeder',      'Accessories', 22.00, 'Elevated anti-spill feeder with removable stainless bowls. Improves posture during meal times.', 0),
('Faux-Fur Donut Cat Bed (Large)',          'Accessories', 45.00, 'Ultra-plush self-warming donut bed. Non-slip base, machine-washable cover, 55cm diameter.', 0),
('Catnip & Silver-vine Crinkle Balls',      'Toys',         8.90, 'Crinkle balls infused with catnip and silver vine. Lightweight, rattle inside, safe for solo play.', 0),
('Waterproof Sailor Raincoat - S/M/L',      'Apparel',     24.90, 'Lightweight waterproof raincoat with hood and velcro fastenings. Reflective strip for night walks.', 0);

*example of blogpost*
INSERT INTO blog_posts (title, tag, author, excerpt, content) VALUES
(
  'The Ultimate Guide to Feeding Your Cat a Balanced Diet in 2025',
  'Nutrition',
  'Dr. Lee Jun Wei',
  'From raw diets to premium kibble, we break down everything you need to know to keep your cat healthy, happy, and well-fed every single day.',
  'Cats are obligate carnivores, which means their bodies are designed to thrive on animal protein. Unlike dogs or humans, cats cannot synthesise certain nutrients on their own and must obtain them directly from meat.

When choosing a cat food, look for:
- Named protein source as the first ingredient (e.g. salmon, chicken, tuna)
- High moisture content - wet food closely mimics a cat''s natural prey diet
- No artificial colours, flavours, or preservatives
- AAFCO or similar certification for nutritional completeness

Dry food can be convenient, but it is low in moisture (around 10%). If your cat eats primarily dry food, ensure they drink plenty of water - a cat water fountain can help encourage hydration.

Feeding schedule: Most adult cats do well with 2 measured meals per day. Free-feeding dry food can lead to obesity, especially in indoor cats.

Always transition to new food gradually over 7-10 days to avoid digestive upset. Mix increasing amounts of the new food with the old.

Treats should make up no more than 10% of daily calories. Freeze-dried single-ingredient treats are a healthy choice.'
),
(
  '10 Toys That Actually Keep Cats Entertained (Tested!)',
  'Play',
  'Priya N.',
  'Our team tested 30+ toys with real cats. Here are the clear winners that kept them engaged longest.',
  'Not all cat toys are created equal. After months of testing with our feline panel, here are the toys that genuinely hold a cat''s attention.

1. Feather Wand - mimics bird movement, triggers hunting instinct
2. Electronic Laser Toy - random patterns prevent cats from predicting movement
3. Silver Vine Crinkle Balls - silver vine is more potent than catnip for many cats
4. Puzzle Feeders - slow feeding while stimulating the brain
5. Robotic Mouse - realistic scurrying motion
6. Tunnel with Crinkle Material - cats love the sound and the hiding opportunity
7. Catnip Kicker - long enough for cats to bunny-kick, satisfying for full-body play
8. Wand with Interchangeable Heads - keeps playtime varied
9. Window Bird Feeder (mounted outside) - free entertainment!
10. Cardboard Boxes - the classic, always effective

Key takeaway: rotate toys weekly to prevent boredom. Even the best toy loses novelty quickly.'
),
(
  'How to Groom Your Cat at Home Without the Drama',
  'Grooming',
  'Mei Lin',
  'Step-by-step guide for nail trimming, brushing, and even bathing a reluctant cat - without battle scars.',
  'Many cats resist grooming because they associate it with restraint and discomfort. The secret is to start slow and make it a positive experience from kittenhood.

Brushing
Start with a soft-bristle brush and short sessions of 2-3 minutes. Gradually increase duration. Brush in the direction of fur growth. Reward with treats immediately after.

Nail Trimming
Use proper cat nail clippers - never scissors. Expose one nail at a time by gently pressing the paw pad. Cut only the clear tip, avoiding the pink quick. If your cat resists, trim one nail per day across a week.

Ear Cleaning
Use a vet-approved ear cleaner and cotton balls (never cotton buds). Gently wipe the outer ear flap only. A healthy ear is light pink inside with minimal odour.

Bathing
Most cats do not need regular baths. If necessary, use lukewarm water and a cat-specific shampoo. Keep sessions under 10 minutes. Dry with a warm towel and keep the cat in a warm room after.

Always end every grooming session with play or treats to build a positive association.'
);

-- ─────────────────────────────────────────────────────────────────────────────
SELECT 'MeowMart database installed successfully.' AS status;

CREATE TABLE IF NOT EXISTS `session_data` (
    `session_id`      VARCHAR(128)     NOT NULL,
    `session_data`    BLOB             NOT NULL,
    `session_expire`  INT(11) UNSIGNED NOT NULL,
    `http_user_agent` VARCHAR(250)     DEFAULT NULL,
    `ip_address`      VARCHAR(45)      DEFAULT NULL,
    `last_active`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
