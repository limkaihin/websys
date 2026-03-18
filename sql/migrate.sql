-- MeowMart Migration Script
-- Run this if you already imported meowmart.sql before the orders feature was added.
-- It is SAFE to run on an existing database — it will NOT drop or overwrite any existing data.
-- Usage: mysql -u root -p meowmart < sql/migrate.sql

USE meowmart;

-- ─── ORDERS ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED   DEFAULT NULL,
    name       VARCHAR(120)   NOT NULL,
    email      VARCHAR(180)   NOT NULL,
    address    TEXT           NOT NULL,
    payment    VARCHAR(30)    NOT NULL DEFAULT 'card',
    total      DECIMAL(10,2)  NOT NULL,
    status     ENUM('confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ─── ORDER ITEMS ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    order_id   INT UNSIGNED   NOT NULL,
    product_id INT UNSIGNED   DEFAULT NULL,
    name       VARCHAR(200)   NOT NULL,
    price      DECIMAL(8,2)   NOT NULL,
    qty        INT UNSIGNED   NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ─── CONTACT MESSAGES ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120)   NOT NULL,
    email      VARCHAR(180)   NOT NULL,
    subject    VARCHAR(255)   NOT NULL,
    message    TEXT           NOT NULL,
    is_read    TINYINT(1)     NOT NULL DEFAULT 0,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

SELECT 'Migration complete — orders, order_items, and contact_messages tables are ready.' AS status;

-- ─── SESSION DATA (Zebra_Session) ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `session_data` (
    `session_id`      VARCHAR(128)     NOT NULL,
    `session_data`    BLOB             NOT NULL,
    `session_expire`  INT(11) UNSIGNED NOT NULL,
    `http_user_agent` VARCHAR(250)     DEFAULT NULL,
    `ip_address`      VARCHAR(45)      DEFAULT NULL,
    `last_active`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Migration complete.' AS status;
