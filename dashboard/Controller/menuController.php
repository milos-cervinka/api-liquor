<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';
require_once ROOT_DIR . '/src/Dashboard/MenuManager.php';

$manager = new MenuManager(ACTIVE_MODULES);
echo json_encode($manager->getMenu());