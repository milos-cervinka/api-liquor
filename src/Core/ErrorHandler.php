<?php

// Source - https://stackoverflow.com/a/2146171
// Posted by user259973, modified by community. See post 'Timeline' for change history
// Retrieved 2026-10-15, License - CC BY-SA 4.0

class ErrorHandler 
{
    public function register(): void 
    {
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleException(Throwable $e): void 
    {
        $this->logError('CRITICAL', $e->getMessage(), $e->getFile(), $e->getLine());
        $this->respond($e->getMessage());
    }

    public function handleShutdown(): void 
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) 
        {
            $this->logError('FATAL', $error['message'], $error['file'], $error['line']);
            $this->respond($error['message']);
        }
    }

    private function logError(string $type, string $message, string $file, int $line): void 
    {
        $logFile = LOG_DIR . '/' . date('Y-m-d') . '.log';
        $msg = sprintf("[%s] [%s] %s in %s:%d%s", date('Y-m-d H:i:s'), $type, $message, $file, $line, PHP_EOL);
        file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
    }

    private function respond(string $message): void 
    {
        http_response_code(500);
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        if ($isAjax || strpos($_SERVER['REQUEST_URI'] ?? '', '/Controller/') !== false) 
        {
            echo json_encode(['error' => 'Internal Server Error', 'details' => $message]);
        } 
        else 
        {
            $errorPage = ROOT_DIR . '/dashboard/View/Errors/500.html';
            if (file_exists($errorPage)) 
            {
                include $errorPage;
            } 
            else 
            {
                echo "<h1>500 Internal Server Error</h1>";
            }
        }
        exit;
    }
}