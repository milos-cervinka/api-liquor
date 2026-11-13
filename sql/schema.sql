CREATE TABLE IF NOT EXISTS stores (
    store_number INT UNSIGNED NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    zip_code VARCHAR(10) NULL,
    county VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    category_code INT UNSIGNED NOT NULL PRIMARY KEY,
    name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vendors (
    vendor_number INT UNSIGNED NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    item_number INT UNSIGNED NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bottle_volume_ml INT UNSIGNED NOT NULL,
    category_code INT UNSIGNED NULL,
    vendor_number INT UNSIGNED NULL,
    INDEX idx_category (category_code),
    INDEX idx_vendor (vendor_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id VARCHAR(50) NOT NULL,
    ordered_on DATE NOT NULL,
    store_number INT UNSIGNED NOT NULL,
    item_number INT UNSIGNED NOT NULL,
    state_bottle_cost DECIMAL(8,2) NOT NULL,
    state_bottle_retail DECIMAL(8,2) NOT NULL,
    bottles_sold INT UNSIGNED NOT NULL,
    INDEX idx_ordered (ordered_on),
    INDEX idx_store (store_number),
    INDEX idx_item (item_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;