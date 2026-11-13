INSERT INTO stores (store_number, name, address, city, zip_code, county)
VALUES (:store_number, :store_name, :address, :city, :zip_code, :county)
ON DUPLICATE KEY UPDATE 
    name = VALUES(name), address = VALUES(address), city = VALUES(city), zip_code = VALUES(zip_code), county = VALUES(county);