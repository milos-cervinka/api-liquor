<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/src/Logger.php';
require_once __DIR__ . '/src/Downloader.php';
require_once __DIR__ . '/src/Importer.php';

global $db;

$logger = new Logger(LOG_DIR);
$stateFile = DATA_DIR . '/import_state.json';

try 
{
    $downloadedFile = null;

    if (file_exists($stateFile)) 
    {
        $state = json_decode(file_get_contents($stateFile), true);
        if (!empty($state['file']) && file_exists($state['file'])) 
        {
            $downloadedFile = $state['file'];
        }
    }

    if ($downloadedFile === null) {
        $downloader = new Downloader(DATASET_URL, DATA_DIR, MAX_BACKUPS, $logger);
        $downloadedFile = $downloader->run();

        if ($downloadedFile === null) 
        {
        // Source - https://stackoverflow.com/a/17161106
        // Posted by Tony Chen, modified by community. See post 'Timeline' for change history
        // Retrieved 2026-10-01, License - CC BY-SA 4.0

            $files = glob(DATA_DIR . '/data_*.csv');
            if (!empty($files)) 
            {
                // Source - https://stackoverflow.com/a/11854421
                // Posted by raidenace
                // Retrieved 2026-10-01, License - CC BY-SA 3.0

                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $downloadedFile = $files[0];
            } 
            else 
            {
                $logger->error("Error data not avaliable");
                exit(1);
            }
        }
    }

    $importer = new Importer($db, SQL_DIR, $logger);
    $importer->run($downloadedFile, 2500);

} 
catch (Throwable $e) 
{
    $logger->error("Error: " . $e->getMessage());
    exit(1);
}