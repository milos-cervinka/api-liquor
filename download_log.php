<?php
require_once __DIR__ . '/config.php';

$logFile = LOG_DIR . '/' . date('Y-m-d') . '.log';

if (!file_exists($logFile)) 
{
    http_response_code(404);
    die("No log found");
}

header('Content-Description: File Transfer');
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="cron_report_' . date('Y-m-d') . '.log"');
header('Content-Length: ' . filesize($logFile));
readfile($logFile);
exit;