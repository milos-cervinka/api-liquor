<?php
require_once __DIR__ . '/DirectoryChecker.php';
require_once __DIR__ . '/ErrorHandler.php';

class App 
{
    public static function run(): void 
    {
        $directories = [
            DATA_DIR,
            LOG_DIR,
            ROOT_DIR . '/dashboard/View/Errors',
            ROOT_DIR . '/dashboard/View/Sites'
        ];

        $checker = new DirectoryChecker($directories);
        $checker->verifyAndCreate();

        $errorHandler = new ErrorHandler();
        $errorHandler->register();
    }
}