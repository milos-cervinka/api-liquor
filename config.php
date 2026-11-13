<?php
define('ROOT_DIR', __DIR__);
define('DATA_DIR', ROOT_DIR . '/data');
define('LOG_DIR', ROOT_DIR . '/logs');
define('SQL_DIR', ROOT_DIR . '/sql');
define('MAX_BACKUPS', 3);
define('DATASET_URL', 'https://data.iowa.gov/api/dataset-download?path=datasets%2F1263%2Frows.csv');
define('ACTIVE_MODULES', [
    'dashboard',
    'logs',
    'user'
]);

require_once ROOT_DIR . '/src/Core/App.php';
App::run();