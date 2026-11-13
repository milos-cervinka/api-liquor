INSERT INTO products (item_number, name, bottle_volume_ml, category_code, vendor_number)
VALUES (:item_number, :product_name, :bottle_volume_ml, :category_code, :vendor_number)
ON DUPLICATE KEY UPDATE 
    name = VALUES(name), bottle_volume_ml = VALUES(bottle_volume_ml), category_code = VALUES(category_code), vendor_number = VALUES(vendor_number);