<?php
require_once __DIR__ . '/../../config.php';

class LogsModel
{
    private string $logDir;

    public function __construct()
    {
        $this->logDir = defined('LOG_DIR') ? LOG_DIR : __DIR__ . '/../../logs';
    }

    public function getAvailableFiles(): array
    {
        if (!is_dir($this->logDir))
        {
            return [];
        }

        $files = scandir($this->logDir, SCANDIR_SORT_DESCENDING);
        $logFiles = [];

        foreach ($files as $file)
        {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'log')
            {
                $logFiles[] = $file;
            }
        }

        return $logFiles;
    }

    public function getLogs(string $targetFile = '', string $type = '', string $search = '', int $page = 1, int $limit = 10): array
    {
        $availableFiles = $this->getAvailableFiles();
       
        if (empty($availableFiles))
        {
            return [
                'logs' => [],
                'total' => 0,
                'page' => 1,
                'pages' => 0,
                'files' => [],
                'activeFile' => ''
            ];
        }

        $cleanFile = basename($targetFile);
        if (empty($cleanFile) || !in_array($cleanFile, $availableFiles))
        {
            $cleanFile = $availableFiles[0];
        }

        $filePath = $this->logDir . '/' . $cleanFile;
        if (!file_exists($filePath))
        {
            return [
                'logs' => [],
                'total' => 0,
                'page' => 1,
                'pages' => 0,
                'files' => $availableFiles,
                'activeFile' => $cleanFile
            ];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);

        $filtered = [];
        $type = strtoupper(trim($type));
        $search = trim($search);

        foreach ($lines as $line)
        {
            if ($type !== '' && $type !== 'ALL')
            {
                if (!preg_match('/\[' . preg_quote($type, '/') . '\]/i', $line))
                {
                    continue;
                }
            }

            if ($search !== '')
            {
                if (stripos($line, $search) === false)
                {
                    continue;
                }
            }

            $filtered[] = $line;
        }

        $total = count($filtered);
        $totalPages = $total > 0 ? (int)ceil($total / $limit) : 1;
        $offset = ($page - 1) * $limit;
        $pagedLogs = array_slice($filtered, $offset, $limit);

        return [
            'logs' => $pagedLogs,
            'total' => $total,
            'page' => $page,
            'pages' => $totalPages,
            'files' => $availableFiles,
            'activeFile' => $cleanFile
        ];
    }
}