INSERT INTO vendors (vendor_number, name)
VALUES (:vendor_number, :vendor_name)
ON DUPLICATE KEY UPDATE name = VALUES(name);