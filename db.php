<?php
require_once __DIR__ . '/config.php';

if (!defined('DB_HOST')) 
{
    define('DB_HOST', '127.0.0.1');
}
if (!defined('DB_NAME')) 
{
    define('DB_NAME', 'cervimi24');
}
if (!defined('DB_USER')) 
{
    define('DB_USER', 'cervimi24');
}
if (!defined('DB_PASSWORD')) 
{
    define('DB_PASSWORD', 'rbUJmQzN');
}

global $pdo, $db;

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$db = $pdo;