INSERT INTO categories (category_code, name)
VALUES (:category_code, :category_name)
ON DUPLICATE KEY UPDATE name = VALUES(name);